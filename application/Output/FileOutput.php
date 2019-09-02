<?php


namespace OTGS\TwigToWPML\Output;


use OTGS\TwigToWPML\TranslatableString;

/**
 * Writes the output to a file (while truncating it).
 */
class FileOutput implements OutputInterface {

	/** @var string*/
	private $filename;

	/** @var resource */
	private $fileHandle;

	/** @var StringOutput */
	private $stringOutput;


	/**
	 * FileOutput constructor.
	 *
	 * @param string $filename
	 * @param StringOutput $stringOutput
	 */
	public function __construct( $filename, StringOutput $stringOutput ) {
		$this->filename = $filename;
		$this->stringOutput = $stringOutput;
	}


	/**
	 * Write a string to the open file.
	 *
	 * @param string $string
	 */
	private function write( $string ) {
		fwrite( $this->fileHandle, $string );
	}


	/**
	 * @inheritDoc
	 */
	public function header() {
		$this->fileHandle = fopen( $this->filename, 'wb' );
		$this->write( $this->stringOutput->header() );
	}


	/**
	 * @inheritDoc
	 */
	public function appendString( TranslatableString $string ) {
		$this->write( $this->stringOutput->appendString( $string ) );
	}


	/**
	 * @inheritDoc
	 */
	public function appendComment( $comment ) {
		$this->write( $this->stringOutput->appendComment( $comment ) );
	}


	/**
	 * @inheritDoc
	 */
	public function footer() {
		$this->write( $this->stringOutput->footer() );
		fclose( $this->fileHandle );
	}
}
