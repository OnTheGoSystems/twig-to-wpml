<?php


namespace OTGS\TwigToWPML\Logger;


use OTGS\TwigToWPML\Setup\SetupInterface;

/**
 * Abstract logger that implements shared functionality.
 */
abstract class AbstractLogger implements LoggerInterface {

	/** @var bool */
	protected $isQuiet = false;

	/** @var int */
	protected $logLevel = LogLevel::INFO;

	/** @var int */
	protected $indentLevel = 0;


	/**
	 * @inheritDoc
	 *
	 * @param SetupInterface $setup
	 */
	public function applySetup( SetupInterface $setup ) {
		$this->isQuiet = (bool) $setup->getIsQuiet();
		$this->logLevel = (int) $setup->getLogLevel();
	}


	/**
	 * @inheritDoc
	 */
	public function indent() {
		$this->indentLevel ++;
	}


	/**
	 * @inheritDoc
	 */
	public function unindent() {
		if ( 0 === $this->indentLevel ) {
			throw new \RuntimeException( 'Invalid logger unindent' );
		}
		$this->indentLevel --;
	}


	/**
	 * Determine whether a message with a particular log level should be actually printed.
	 *
	 * @param int $logLevel One of the LogLevel constants.
	 *
	 * @return bool
	 */
	protected function shouldPrint( $logLevel ) {
		if( LogLevel::OVERRIDE === $logLevel ) {
			return true;
		}

		if( $this->isQuiet ) {
			return false;
		}

		return $this->logLevel <= $logLevel;
	}

}
