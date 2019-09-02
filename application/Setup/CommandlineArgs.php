<?php


namespace OTGS\TwigToWPML\Setup;


use GetOpt\GetOpt;
use OTGS\TwigToWPML\Logger\LoggerInterface;
use OTGS\TwigToWPML\Logger\LogLevel;

class CommandlineArgs implements SetupInterface {

	private $getopt;

	private $argv;

	private $isInitialized = false;

	/**
	 * @var LoggerInterface
	 */
	private $logger;


	public function __construct( $argv, GetOpt $getopt, LoggerInterface $logger ) {
		$this->getopt = $getopt;
		$this->argv = $argv;
		$this->logger = $logger;
	}


	private function initializeGetOpt() {
		$this->getopt->addOptions( [
			[ 'i', 'input', \GetOpt\GetOpt::REQUIRED_ARGUMENT, 'Directory with twig templates' ],
			[
				'o',
				'output',
				\GetOpt\GetOpt::REQUIRED_ARGUMENT,
				'Path of the output file. If none provided, the result is printed to the standard output',
			],
			[
				't',
				'default-textdomain',
				\GetOpt\GetOpt::REQUIRED_ARGUMENT,
				'Default textdomain to be used when one is missing.',
			],
			[ 'q', 'quiet', \GetOpt\GetOpt::NO_ARGUMENT ],
			[
				'l',
				'log-level',
				\GetOpt\GetOpt::REQUIRED_ARGUMENT,
				'Desired log level: 0 is info, 1 will print warnings and 2 is for printing errors only. Ignored if --quiet is provided.',
			],
			[ 'help', \GetOpt\GetOpt::NO_ARGUMENT ],
		] );
	}


	private function maybeInit() {
		if ( $this->isInitialized ) {
			return;
		}

		$this->initializeGetOpt();
		$this->getopt->process( $this->argv );
		$this->isInitialized = true;
	}


	public function getInputDirectory() {
		return $this->getopt->getOption( 'input' );
	}


	public function getOutputFile() {
		return $this->getopt->getOption( 'output' );
	}


	public function isValid() {
		$this->maybeInit();
		if ( null === $this->getInputDirectory() ) {
			$this->logger->log( 'Missing --input argument.', LogLevel::ERROR );

			return false;
		}

		return true;
	}


	public function getDefaultTextdomain() {
		return $this->getopt->getOption( 'default-textdomain' );
	}


	public function getIsQuiet() {
		return $this->getopt->offsetExists( 'quiet' );
	}


	public function shouldContinue() {
		if ( $this->getopt->offsetExists( 'help' ) ) {
			$this->logger->log( $this->getopt->getHelpText(), LogLevel::OVERRIDE );

			return false;
		}

		return true;
	}


	public function getLogLevel() {
		return (int) $this->getopt->getOption( 'log-level' );
	}


	public function initialize() {
		$this->maybeInit();
	}
}
