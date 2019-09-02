<?php


namespace OTGS\TwigToWPML\TemplateProvider;


use OTGS\TwigToWPML\Logger\LoggerInterface;
use OTGS\TwigToWPML\Template\TemplateFactory;

/**
 * Recursively scans a provided directory for .twig files.
 */
class TemplateDirectoryReader implements TemplateProviderInterface {

	/** @var string */
	private $rootDir;

	/** @var bool */
	private $isScanned = false;

	/** @var string[] */
	private $templateFiles = array();

	/** @var TemplateFactory */
	private $templateFactory;

	/** @var LoggerInterface */
	private $logger;


	/**
	 * TemplateDirectoryReader constructor.
	 *
	 * @param string $rootDir Root directory to scan.
	 * @param TemplateFactory $templateFactory
	 * @param LoggerInterface $logger
	 */
	public function __construct( $rootDir, TemplateFactory $templateFactory, LoggerInterface $logger ) {
		$this->rootDir = $rootDir;
		$this->templateFactory = $templateFactory;
		$this->logger = $logger;
	}


	/**
	 * Recursively apply a glob pattern on a directory.
	 *
	 * @link https://stackoverflow.com/a/12109100
	 *
	 * @param string $pattern
	 * @param int $flags Note: Does not support flag GLOB_BRACE.
	 *
	 * @return string[] File paths.
	 */
	private function globRecursive( $pattern, $flags = 0 ) {
		$files = glob( $pattern, $flags );
		$subdirs = glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR | GLOB_NOSORT );

		if( false === $files || false === $subdirs ) {
			throw new \RuntimeException( 'glob error' );
		}
		foreach ( $subdirs as $dir ) {
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$files = array_merge( $files, $this->globRecursive( $dir . '/' . basename( $pattern ), $flags ) );
		}

		return $files;
	}


	/**
	 * Perform scan for twig templates if it hasn't been performed already.
	 */
	private function maybeScan() {
		if( $this->isScanned ) {
			return;
		}

		$this->templateFiles = $this->globRecursive( $this->trailingslashit( $this->rootDir ) . '*.twig' );

		$this->logger->log( sprintf( 'Discovered %d Twig templates in "%s".', count( $this->templateFiles ), $this->rootDir ) );
		$this->isScanned = true;
	}


	/**
	 * Add a directory separator to a path if it's missing.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private function trailingslashit( $path ) {
		if( in_array( substr( $path, - 1 ), [ DIRECTORY_SEPARATOR, '\'' ], true ) ) {
			return $path;
		}

		return $path . DIRECTORY_SEPARATOR;
	}


	/**
	 * @inheritDoc
	 */
	public function getNextTemplate() {
		$this->maybeScan();

		if( empty( $this->templateFiles ) ) {
			return null;
		}

		return $this->templateFactory->createFromFile( array_shift( $this->templateFiles ) );
	}
}
