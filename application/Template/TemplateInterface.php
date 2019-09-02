<?php


namespace OTGS\TwigToWPML\Template;

/**
 * Interface representing a single Twig template.
 */
interface TemplateInterface {

	/**
	 * Retrieve the content of the template.
	 *
	 * @return string
	 */
	public function getContent();


	/**
	 * Get the template name to identify it somehow.
	 *
	 * @return string
	 */
	public function getName();
}
