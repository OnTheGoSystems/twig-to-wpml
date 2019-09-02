<?php


namespace OTGS\TwigToWPML;


/**
 * Represents a single translatable string.
 */
class TranslatableString {

	/** @var string Contents of the string */
	private $string;

	/** @var string String textdomain */
	private $textdomain;


	/**
	 * TranslatableString constructor.
	 *
	 * @param string $string
	 * @param string $textdomain
	 */
	public function __construct( $string, $textdomain ) {
		$this->string = $string;
		$this->textdomain = $textdomain;
	}


	/**
	 * @return string
	 */
	public function getString() {
		return $this->string;
	}


	/**
	 * @return string
	 */
	public function getTextdomain() {
		return $this->textdomain;
	}

}
