<?php
/**
 * SmartHomeShopUK child theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

$shs_theme_includes = array(
	'/inc/archive.php',
	'/inc/woocommerce.php',
	'/inc/enqueue.php',
);

foreach ( $shs_theme_includes as $shs_theme_include ) {
	require_once get_stylesheet_directory() . $shs_theme_include;
}

