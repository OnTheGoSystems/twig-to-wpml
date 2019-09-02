<?php


namespace OTGS\TwigToWPML\Output;

/**
 * Instantiates OutputInterface instances.
 */
class OutputFactory {

	/**
	 * Provide the appropriate output handler based on the value of the output option.
	 *
	 * @param string|null $outputArg Value of the "output" option.
	 *
	 * @return OutputInterface
	 */
	public function build( $outputArg ) {
		if( empty( $outputArg ) ) {
			return new StandardOutput( new StringOutput() );
		}

		return new FileOutput( $outputArg, new StringOutput() );
	}

}
