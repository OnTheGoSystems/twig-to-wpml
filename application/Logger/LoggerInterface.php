<?php
namespace OTGS\TwigToWPML\Logger;

use OTGS\TwigToWPML\Setup\SetupInterface;

/**
 * Logger interface.
 */
interface LoggerInterface {

	/**
	 * @param string $message
	 * @param int $level One of the LogLevel constants.
	 *
	 * @return void
	 */
	public function log( $message, $level = 0 );


	/**
	 * Apply the relevant logging options once they have been processed.
	 *
	 * @param SetupInterface $setup
	 * @return void
	 */
	public function applySetup( SetupInterface $setup );


	/**
	 * Add one indentation level to following messages.
	 *
	 * @return void
	 */
	public function indent();


	/**
	 * Remove one indentation level from following messages.
	 *
	 * @return void
	 * @throws \RuntimeException if trying to unindent when there's no indentation anymore.
	 */
	public function unindent();
}
