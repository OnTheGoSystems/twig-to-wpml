<?php
/*
Plugin Name: Twig to WPML
Plugin URI: @todo
Description: WP-CLI extension to parse Twig tempates for strings and register them with WPML String Translation.
Version: 0.1.0
Author: zaantar
Author URI: http://zaantar.eu
Text Domain: twig-to-wpml
*/

if ( PHP_VERSION_ID < 70100 ) {
	wp_die( 'This plugin requires PHP 7.1 or higher.' );
}

require_once __DIR__ . '/vendor/autoload.php';

$bootstrap = new \OTGS\TwigToWPML\Bootstrap();
$bootstrap->initialize();
