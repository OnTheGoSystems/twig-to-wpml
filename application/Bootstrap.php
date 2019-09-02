<?php

namespace OTGS\TwigToWPML;


use GetOpt\GetOpt;
use OTGS\TwigToWPML\Output\OutputFactory;
use OTGS\TwigToWPML\Template\TemplateFactory;

/**
 * Main class to bootstrap the whole process.
 */
class Bootstrap {

	/**
	 * Initialization.
	 */
	public function initialize() {
		// Nothing yet.
	}


	/**
	 * Execute the tool from a terminal.
	 *
	 * @param array $argv Command line arguments.
	 *
	 * @return int
	 */
	public function processCommandline( $argv ) {
		$logger = new Logger\Commandline();
		$logger->log( 'Initializing...' );

		$args = new Setup\CommandlineArgs( $argv, new GetOpt(), $logger );
		$args->initialize();
		if( ! $args->isValid() ) {
			return 1; // error
		}

		if( ! $args->shouldContinue() ) {
			return 0; // success, but we're done here
		}

		$logger->applySetup( $args );

		$outputFactory = new OutputFactory();

		$command = new Command(
			new TemplateProvider\TemplateDirectoryReader( $args->getInputDirectory(), new TemplateFactory(), $logger ),
			$outputFactory->build( $args->getOutputFile() ),
			$logger,
			new TwigService( $logger ),
			$args
		);

		// Make the magic happen.
		$isSuccess = $command->run();

		return ( $isSuccess ? 0 : 1 ); // bash exit code
	}
}
