<?php


namespace OTGS\TwigToWPML\TemplateProvider;


use OTGS\TwigToWPML\Template\TemplateInterface;

/**
 * Interface representing an object that provides Twig templates to scan.
 */
interface TemplateProviderInterface {

	/**
	 * Return the next template to scan if available, null otherwise.
	 *
	 * @return TemplateInterface|null
	 */
	public function getNextTemplate();

}
