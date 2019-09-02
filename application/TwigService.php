<?php


namespace OTGS\TwigToWPML;

use OTGS\TwigToWPML\Logger\LoggerInterface;
use OTGS\TwigToWPML\Logger\LogLevel;
use OTGS\TwigToWPML\Template\TemplateInterface;
use ReflectionClass;

/**
 * This is where the dark magic happens.
 *
 * Interactions with Twig in ways that the authors probably didn't anticipate.
 */
class TwigService {

	/** @var string[] Gettext functions to look for. */
	const GETTEXT_FUNCTIONS = [
		'__',
		'_e',
	];

	/** @var string[] Names of functions that need to be dynamically added to Twig so that the templates can be parsed. */
	private $dynamicallyDiscoveredFunctions = [];

	/**
	 * @var \Twig\Environment Latest Twig environment object.
	 *        Can be recreated during the process after a new function has been added.
	 */
	private $twigEnvironment;

	/** @var LoggerInterface */
	private $logger;


	/**
	 * TwigService constructor.
	 *
	 * @param LoggerInterface $logger
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}


	/**
	 * Parse a single template into Twig nodes.
	 *
	 * @param TemplateInterface $template
	 *
	 * @return \Twig\Node\ModuleNode
	 * @throws \Twig\Error\SyntaxError
	 */
	public function parseTemplate( TemplateInterface $template ) {
		$twig = $this->getEnvironment();
		$repeatParsing = false;
		$nodes = [];

		do {
			try {
				$tokenStream = $twig->tokenize( new \Twig\Source( $template->getContent(), $template->getName() ) );
				$nodes = $twig->parse( $tokenStream );
				$repeatParsing = false;
			} catch ( \Twig\Error\SyntaxError $e ) {
				// Try to recognize a syntax error because of an undefined function.
				//
				// If that happens, add the function to the list, recreate the environment and try again.
				$matches = [];
				$matchResult = preg_match( '/Unknown "([^"]+)" function./m', $e->getRawMessage(), $matches );
				if ( $matchResult === 1 ) {
					$missingFunctionName = $matches[1];
					$this->dynamicallyDiscoveredFunctions[ $missingFunctionName ] = $missingFunctionName;
					$twig = $this->recreateEnvironment();

					$this->logger->log(
						sprintf( 'Adding a missing Twig function "%s" and trying to parse the template again.', $missingFunctionName ),
						LogLevel::WARNING
					);

					$repeatParsing = true;
				} else {
					// Not our known problem, throw it again.
					throw $e;
				}
			}
		} while ( $repeatParsing );

		return $nodes;
	}


	/**
	 * Get the current Twig environment or create a new one.
	 *
	 * @return \Twig\Environment
	 */
	private function getEnvironment() {
		if ( null === $this->twigEnvironment ) {
			$this->twigEnvironment = $this->createEnvironment();
		}

		return $this->twigEnvironment;
	}


	/**
	 * Create a new twig environment.
	 *
	 * @return \Twig\Environment
	 */
	private function createEnvironment() {
		$twig = new \Twig\Environment( new \Twig\Loader\ArrayLoader() );
		foreach ( array_merge( self::GETTEXT_FUNCTIONS, $this->dynamicallyDiscoveredFunctions ) as $functionName ) {
			$this->addTwigFunction( $twig, $functionName );
		}

		return $twig;
	}


	/**
	 * Add a new (faux) function to the Twig environment, so that it can be parsed.
	 *
	 * @param \Twig\Environment $twig
	 * @param string $functionName
	 */
	private function addTwigFunction( \Twig\Environment $twig, $functionName ) {
		$noop = static function () {
			// Just a faux function to allow Twig to parse the template.
		};

		$twig->addFunction( new \Twig\TwigFunction( $functionName, $noop ) );
	}


	/**
	 * Recreate the Twig environment and return it.
	 *
	 * @return \Twig\Environment
	 */
	private function recreateEnvironment() {
		$this->twigEnvironment = $this->createEnvironment();

		return $this->twigEnvironment;
	}


	/**
	 * Determine if a provided node represents a call to a gettext function.
	 *
	 * @param \Twig\Node\Node $node
	 *
	 * @return bool
	 * @throws \ReflectionException
	 */
	public function isGettextFunctionCall( \Twig\Node\Node $node ) {
		if ( ! $node instanceof \Twig\Node\Expression\FunctionExpression ) {
			return false;
		}

		$attributes = $this->getTwigNodeAttributes( $node );

		return (
			array_key_exists( 'name', $attributes )
			&& in_array( $attributes['name'], self::GETTEXT_FUNCTIONS, true )
		);
	}


	/**
	 * Retrieve node attributes.
	 *
	 * @param \Twig\Node\Node $node
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function getTwigNodeAttributes( \Twig\Node\Node $node ) {
		return $this->getProtectedProperty( $node, 'attributes' );
	}


	/**
	 * Extract a protected property from an object.
	 *
	 * @param object $object
	 * @param string $propertyName
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	private function getProtectedProperty( $object, $propertyName ) {
		$reflection = new ReflectionClass( $object );
		$property = $reflection->getProperty( $propertyName );
		$property->setAccessible( true );

		return $property->getValue( $object );

	}


	/**
	 * Extract (constant) call arguments from a Twig node.
	 *
	 * @param \Twig\Node\Node $node
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getFunctionCallArgs( \Twig\Node\Node $node ) {
		if ( ! $node instanceof \Twig\Node\Expression\FunctionExpression ) {
			throw new \InvalidArgumentException( 'Invalid node type.' );
		}

		$callSubnodes = $this->getTwigSubnodes( $node );
		if ( ! array_key_exists( 'arguments', $callSubnodes ) ) {
			throw new \InvalidArgumentException( 'Missing the "arguments" subnode.' );
		}

		return array_map(
			function ( \Twig\Node\Expression\ConstantExpression $argumentNode ) {
				$attributes = $this->getTwigNodeAttributes( $argumentNode );

				if ( ! is_array( $attributes )
					|| ! array_key_exists( 'value', $attributes )
					|| ! is_string( $attributes['value'] ) ) {
					throw new \RuntimeException( 'Missing the "value" attribute from a ConstantExpression Twig node.' );
				}

				return $attributes['value'];
			},
			array_filter( $this->getTwigSubnodes( $callSubnodes['arguments'] ), static function ( \Twig\Node\Node $node ) {
				return ( $node instanceof \Twig\Node\Expression\ConstantExpression );
			} )
		);

	}


	/**
	 * Retrieve subnodes from a Twig node.
	 *
	 * @param \Twig\Node\Node $node
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function getTwigSubnodes( \Twig\Node\Node $node ) {
		return $this->getProtectedProperty( $node, 'nodes' );
	}
}
