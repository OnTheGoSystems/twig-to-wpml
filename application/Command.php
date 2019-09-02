<?php


namespace OTGS\TwigToWPML;


use OTGS\TwigToWPML\Logger\LoggerInterface;
use OTGS\TwigToWPML\Logger\LogLevel;
use OTGS\TwigToWPML\Output\OutputInterface;
use OTGS\TwigToWPML\Setup\SetupInterface;
use OTGS\TwigToWPML\Template\TemplateInterface;
use OTGS\TwigToWPML\TemplateProvider\TemplateProviderInterface;

/**
 * The command class that handles the actual process, after having received all the well-structured inputs.
 */
class Command {

	/** @var TemplateProviderInterface */
	private $templateProvider;

	/** @var OutputInterface */
	private $output;

	/** @var LoggerInterface */
	private $logger;

	/** @var TwigService */
	private $twigService;

	/** @var SetupInterface */
	private $setup;


	/**
	 * Command constructor.
	 *
	 * @param TemplateProviderInterface $templateProvider
	 * @param OutputInterface $output
	 * @param LoggerInterface $logger
	 * @param TwigService $twigService
	 * @param SetupInterface $setup
	 */
	public function __construct(
		TemplateProviderInterface $templateProvider,
		OutputInterface $output,
		LoggerInterface $logger,
		TwigService $twigService,
		SetupInterface $setup
	) {
		$this->templateProvider = $templateProvider;
		$this->output = $output;
		$this->logger = $logger;
		$this->twigService = $twigService;
		$this->setup = $setup;
	}


	/**
	 * Run the whole process.
	 *
	 * @return bool True on success.
	 */
	public function run() {
		$this->logger->log( 'Writing output file header' );
		$this->output->header();

		$hadErrors = false;

		while( $template = $this->templateProvider->getNextTemplate() ) {
			$this->logger->log( sprintf( 'Processing template "%s" ...', $template->getName() ) );
			$this->logger->indent();

			try {
				$this->processSingleTemplate( $template );
			} catch( \Throwable $t ) {
				$this->logger->log(
					sprintf(
						'There has been an error while processing this template: "%s". The template has been skipped.',
						$t->getMessage()
					),
					LogLevel::ERROR
				);

				$hadErrors = true;
				continue;
			} finally {
				$this->logger->unindent();
			}
		}

		$this->output->footer();
		$this->logger->log( 'Operation completed.' );
		return ! $hadErrors;
	}


	/**
	 * Process a single template file.
	 *
	 * @param TemplateInterface $template
	 *
	 * @throws \Twig\Error\SyntaxError
	 */
	private function processSingleTemplate( TemplateInterface $template ) {
		$this->output->appendComment( sprintf( 'Template: "%s"' . PHP_EOL . PHP_EOL, $template->getName() ) );
		$rootNode = $this->twigService->parseTemplate( $template );
		$this->processTwigNode( $rootNode );
	}


	/**
	 * Recursively process a single Twig node and look for gettext calls and other subnodes.
	 *
	 * @param \Twig\Node\Node $node
	 */
	private function processTwigNode( \Twig\Node\Node $node ) {
		if ( $this->twigService->isGettextFunctionCall( $node ) ) {
			$args = $this->twigService->getFunctionCallArgs( $node );
			$string = $this->parseGettextArguments( $args );
			if( null !== $string ) {
				$this->logger->log( sprintf( 'Discovered string: TXD %s "%s"', $string->getTextdomain(), $string->getString() ) );
				$this->output->appendString( $string );
				return;
			}

			$this->logger->log(
				sprintf( 'Misunderstood gettext function arguments, skipping: "%s"', print_r( $args, true ) ),
				LogLevel::WARNING
			);
			// Still try to process subnodes. We're not sure what exactly happened here.
		}

		$subnodes = $this->twigService->getTwigSubnodes( $node );
		foreach( $subnodes as $subnode ) {
			$this->processTwigNode( $subnode );
		}
	}


	/**
	 * Process arguments of a gettext function and turn them into a TranslatableString instance if possible.
	 *
	 * @param string[] $args
	 * @return TranslatableString|null
	 */
	private function parseGettextArguments( $args ) {
		switch( count( $args ) ) {
			case 2:
				return new TranslatableString( $args[0], $args[1] );
			case 1:
				return new TranslatableString( $args[0], $this->setup->getDefaultTextdomain() );
			default:
				return null;
		}
	}

}
