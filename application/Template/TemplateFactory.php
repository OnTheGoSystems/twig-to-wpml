<?php


namespace OTGS\TwigToWPML\Template;

/**
 * Instantiates TemplateInterface classes.
 */
class TemplateFactory {

	/**
	 * @param string $path Path to the template file.
	 *
	 * @return TemplateInterface
	 */
	public function createFromFile( $path ) {
		return new FileTemplate( $path );
	}
}
