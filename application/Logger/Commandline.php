<?php


namespace OTGS\TwigToWPML\Logger;

/**
 * Command line logger that prints to the error stream of the terminal.
 */
class Commandline extends AbstractLogger {

	/**
	 * @inheritDoc
	 *
	 * @param string $message
	 * @param int $level
	 */
	public function log( $message, $level = LogLevel::INFO ) {
		if( ! $this->shouldPrint( $level ) ) {
			return;
		}

		$indentation = str_repeat( "\t", $this->indentLevel );

		fwrite( STDERR, $indentation . $message . PHP_EOL );
	}


}
