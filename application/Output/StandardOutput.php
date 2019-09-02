<?php


namespace OTGS\TwigToWPML\Output;

use OTGS\TwigToWPML\TranslatableString;

/**
 * Prints to the standard terminal output.
 */
class StandardOutput implements OutputInterface {

	/** @var StringOutput */
	private $stringOutput;


	/**
	 * StandardOutput constructor.
	 *
	 * @param StringOutput $stringOutput
	 */
	public function __construct( StringOutput $stringOutput ) {
		$this->stringOutput = $stringOutput;
	}


	/**
	 * @inheritDoc
	 */
	public function header() {
		echo $this->stringOutput->header();
	}


	/**
	 * @inheritDoc
	 */
	public function appendString( TranslatableString $string ) {
		echo $this->stringOutput->appendString( $string );
	}


	/**
	 * @inheritDoc
	 */
	public function appendComment( $comment ) {
		echo $this->stringOutput->appendComment( $comment );
	}


	/**
	 * @inheritDoc
	 */
	public function footer() {
		echo $this->stringOutput->footer();
	}
}
