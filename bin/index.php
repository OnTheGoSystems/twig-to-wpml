#!/usr/bin/env php
<?php

if ( PHP_VERSION_ID < 70100 ) {
	die( 'This tool requires PHP 7.1 or higher.' );
}

require_once __DIR__ . '/../vendor/autoload.php';

$bootstrap = new \OTGS\TwigToWPML\Bootstrap();
$bootstrap->initialize();

$bootstrap->processCommandline( $argv );

echo "Operation completed.\n";
