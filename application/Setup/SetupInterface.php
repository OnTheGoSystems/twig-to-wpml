<?php


namespace OTGS\TwigToWPML\Setup;

/**
 * Represents an object holding the options for execution.
 */
interface SetupInterface {

	/**
	 * Must be called after instantiating.
	 *
	 * @return void
	 */
	public function initialize();


	/**
	 * Check whether the configuration provided by the user is valid.
	 *
	 * @return bool
	 */
	public function isValid();


	/**
	 * Get the input directory for scanning Twig templates.
	 *
	 * @return string
	 */
	public function getInputDirectory();


	/**
	 * Get the output filename or null/empty string if standard output should be used.
	 *
	 * @return string|null
	 */
	public function getOutputFile();


	/**
	 * Get the default textdomain that should be used if strings are missing one.
	 *
	 * @return string
	 */
	public function getDefaultTextdomain();


	/**
	 * Check if the "quiet" flag has been specified.
	 *
	 * @return bool
	 */
	public function getIsQuiet();


	/**
	 * Determine if the execution should continue after the configuration has been processed.
	 *
	 * This can be handy in case of, for example, printing usage information.
	 *
	 * @return bool
	 */
	public function shouldContinue();


	/**
	 * Minimal log level that should be printed.
	 *
	 * @return int One of the LogLevel constants.
	 */
	public function getLogLevel();
}
