<?php


namespace OTGS\TwigToWPML\Template;

/**
 * Twig template from a template file.
 */
class FileTemplate implements TemplateInterface {

	/** @var string */
	private $filename;


	/**
	 * FileTemplate constructor.
	 *
	 * @param string $filename Filename of the template.
	 */
	public function __construct( $filename ) {
		$this->filename = $filename;
	}


	/**
	 * @inheritDoc
	 */
	public function getContent() {
		$contents = file_get_contents( $this->filename );
		if ( false === $contents ) {
			throw new \RuntimeException( sprintf( 'Unable to open a template file at "%s".', $this->filename ) );
		}

		return $contents;
	}


	/**
	 * @inheritDoc
	 */
	public function getName() {
		return $this->filename;
	}
}
