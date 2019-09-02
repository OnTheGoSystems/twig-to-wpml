<?php


namespace OTGS\TwigToWPML\Output;


use OTGS\TwigToWPML\TranslatableString;

/**
 * Interface for processing the output - the resulting PHP file.
 */
interface OutputInterface {

	/**
	 * Write the file header. Must be called at the very beginning.
	 *
	 * @return void
	 */
	public function header();


	/**
	 * Append a translatable string.
	 *
	 * @param TranslatableString $string
	 *
	 * @return void
	 */
	public function appendString( TranslatableString $string );


	/**
	 * Append a PHP comment.
	 *
	 * @param string $comment Contents of the comment without the comment syntax itself. New lines acceptable
	 * 		but not encouraged.
	 *
	 * @return void
	 */
	public function appendComment( $comment );


	/**
	 * Write the file footer. Must be called at the very end.
	 *
	 * @return void
	 */
	public function footer();

}
