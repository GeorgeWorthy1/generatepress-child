<?php
/**
 * Theme stylesheet registration and conditional loading.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Use the GeneratePress RTL stylesheet when WordPress requests one.
 *
 * @param string $uri Current locale stylesheet URI.
 * @return string
 */
function shs_locale_stylesheet_uri( $uri ) {
	$rtl_path = get_template_directory() . '/rtl.css';

	if ( empty( $uri ) && is_rtl() && file_exists( $rtl_path ) ) {
		return get_template_directory_uri() . '/rtl.css';
	}

	return $uri;
}
add_filter( 'locale_stylesheet_uri', 'shs_locale_stylesheet_uri' );

/**
 * Enqueue a child-theme stylesheet with automatic cache busting.
 *
 * @param string   $handle        WordPress stylesheet handle.
 * @param string   $relative_path File path relative to the child theme.
 * @param string[] $dependencies  Stylesheet dependencies.
 */
function shs_enqueue_theme_style( $handle, $relative_path, $dependencies = array() ) {
	$relative_path = ltrim( $relative_path, '/' );
	$file_path     = get_stylesheet_directory() . '/' . $relative_path;

	if ( ! file_exists( $file_path ) ) {
		return;
	}

	wp_enqueue_style(
		$handle,
		get_stylesheet_directory_uri() . '/' . $relative_path,
		$dependencies,
		(string) filemtime( $file_path )
	);
}

/**
 * Load shared styles and template-specific styles in a predictable order.
 */
function shs_enqueue_theme_styles() {
	shs_enqueue_theme_style( 'shs-variables', 'css/variables.css', array( 'generate-style' ) );
	shs_enqueue_theme_style( 'shs-child-style', 'style.css', array( 'shs-variables' ) );
	shs_enqueue_theme_style( 'shs-base', 'css/base.css', array( 'shs-child-style' ) );

	if ( is_page() && ! is_front_page() ) {
		shs_enqueue_theme_style( 'shs-pages', 'css/pages.css', array( 'shs-base' ) );
	}

	if ( is_category() || is_tag() || is_author() || is_date() ) {
		shs_enqueue_theme_style( 'shs-blog-archive', 'css/blog-archive.css', array( 'shs-base' ) );
	}

	if ( is_singular( 'post' ) ) {
		shs_enqueue_theme_style( 'shs-single-post', 'css/single-post.css', array( 'shs-base' ) );
	}

	shs_enqueue_theme_style( 'shs-header', 'css/header.css', array( 'shs-base' ) );
	shs_enqueue_theme_style( 'shs-footer', 'css/footer.css', array( 'shs-base' ) );

	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) ) {
		shs_enqueue_theme_style( 'shs-woocommerce-archive', 'css/woocommerce-archive.css', array( 'shs-base' ) );
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		shs_enqueue_theme_style( 'shs-woocommerce-product', 'css/woocommerce-product.css', array( 'shs-base' ) );
	}

	/* Reusable card and content components load last by design. */
	shs_enqueue_theme_style( 'shs-components', 'css/components.css', array( 'shs-base' ) );
}
add_action( 'wp_enqueue_scripts', 'shs_enqueue_theme_styles', 20 );

