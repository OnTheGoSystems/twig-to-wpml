<?php


namespace OTGS\TwigToWPML\Logger;

/**
 * Pseudo-enum specifying the logging severity.
 */
final class LogLevel {

	/** @var int Messages with this level will be always printed, ignoring even the isQuiet() option. */
	const OVERRIDE = 3;

	const ERROR = 2;

	const WARNING = 1;

	const INFO = 0;

}
