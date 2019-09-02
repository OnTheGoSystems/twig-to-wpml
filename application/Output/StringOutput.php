<?php


namespace OTGS\TwigToWPML\Output;


use OTGS\TwigToWPML\TranslatableString;

/**
 * Produces the PHP output and returns it as a string.
 */
class StringOutput implements OutputInterface {

	/**
	 * @inheritDoc
	 *
	 * @return string
	 */
	public function header() {
		return '<?php' . PHP_EOL;
	}


	/**
	 * Escape any PHP string before it gets printed (in a very basic way).
	 *
	 * @param string $string String to be escaped.
	 *
	 * @return string
	 */
	private function escape( $string ) {
		return addcslashes( $string, '\'' );
	}


	/**
	 * @inheritDoc
	 *
	 * @return string
	 */
	public function appendString( TranslatableString $string ) {
		return sprintf(
			'__( \'%s\', \'%s\' ); ' . PHP_EOL,
			$this->escape( $string->getString() ),
			$this->escape( $string->getTextdomain() )
		);
	}

	/**
	 * @inheritDoc
	 *
	 * @return string
	 */
	public function appendComment( $comment ) {
		return '// ' . str_replace( PHP_EOL, PHP_EOL . '// ', $comment ) . PHP_EOL;
	}

	/**
	 * @inheritDoc
	 *
	 * @return string
	 */
	public function footer() {
		return '';
	}

}
