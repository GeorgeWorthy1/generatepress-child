<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );


// END ENQUEUE PARENT ACTION


/**
 * SHS: Category archive filters + posts per page.
 * Supports:
 * - ?tag=slug
 * - ?q=search terms
 * Applies only to: guides, fixes, smart-home-basics
 */
add_action( 'pre_get_posts', function ( $query ) {

	if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_category() ) {
		return;
	}

	// Get current category slug in a way that does NOT rely on queried object methods.
	$cat_slug = (string) $query->get( 'category_name' );

	// Only apply to your learning archives.
	if ( in_array( $cat_slug, [ 'guides', 'fixes', 'smart-home-basics' ], true ) ) {
		$query->set( 'posts_per_page', 12 );

		// Tag filter: /guides/?tag=zigbee
		if ( isset( $_GET['tag'] ) && is_string( $_GET['tag'] ) ) {
			$tag = sanitize_title( wp_unslash( $_GET['tag'] ) );
			if ( $tag !== '' ) {
				$query->set( 'tag', $tag );
			}
		}

		// Search filter: /guides/?q=something
		// (Use q so WP doesn't switch templates like it can with ?s=)
		if ( isset( $_GET['q'] ) && is_string( $_GET['q'] ) ) {
			$q = trim( sanitize_text_field( wp_unslash( $_GET['q'] ) ) );
			if ( $q !== '' ) {
				$query->set( 's', $q );
			}
		}
	}
} );


/**
 * Properly enqueue child theme stylesheet
 */
add_action( 'wp_enqueue_scripts', 'shs_enqueue_child_styles', 20 );
function shs_enqueue_child_styles() {
    wp_enqueue_style(
        'shs-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('generate-style'), // load AFTER GeneratePress
        wp_get_theme()->get('Version')
    );
}


/**
 * SmartHomeShopUK product archive filter sidebar.
 */
add_action( 'widgets_init', function() {
	register_sidebar( array(
		'name'          => 'Product Archive Filters',
		'id'            => 'shs-product-filters',
		'description'   => 'Filters shown on WooCommerce product archive pages.',
		'before_widget' => '<section id="%1$s" class="widget shs-filter-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="shs-filter-widget__title">',
		'after_title'   => '</h3>',
	) );
} );


/* =========================================================
   Breadcrumb cleanup
   Remove "Page X" from archive breadcrumbs
   ========================================================= */

/**
 * Remove "Page X" crumbs from Yoast breadcrumbs.
 */
add_filter( 'wpseo_breadcrumb_links', function( $links ) {

	if ( is_paged() ) {
		foreach ( $links as $key => $link ) {
			if (
				isset( $link['text'] )
				&& preg_match( '/^Page\s+\d+$/i', trim( $link['text'] ) )
			) {
				unset( $links[ $key ] );
			}
		}

		$links = array_values( $links );
	}

	return $links;

}, 20 );


/**
 * Remove "Page X" crumbs from WooCommerce breadcrumbs.
 */
add_filter( 'woocommerce_get_breadcrumb', function( $crumbs ) {

	if ( is_paged() ) {
		foreach ( $crumbs as $key => $crumb ) {
			if (
				isset( $crumb[0] )
				&& preg_match( '/^Page\s+\d+$/i', trim( $crumb[0] ) )
			) {
				unset( $crumbs[ $key ] );
			}
		}

		$crumbs = array_values( $crumbs );
	}

	return $crumbs;

}, 20 );



/* =========================================================
   SmartHomeShopUK child theme styles
   ========================================================= */

add_action( 'wp_enqueue_scripts', function() {

	$theme_uri  = get_stylesheet_directory_uri();
	$theme_path = get_stylesheet_directory();

	/*
	 * Helper function to enqueue child theme CSS files.
	 */
	$enqueue_shs_style = function( $handle, $file, $deps = array() ) use ( $theme_uri, $theme_path ) {

		$file_path = $theme_path . '/css/' . $file;
		$file_uri  = $theme_uri . '/css/' . $file;

		if ( file_exists( $file_path ) ) {
			wp_enqueue_style(
				$handle,
				$file_uri,
				$deps,
				filemtime( $file_path )
			);
		}
	};

	/* Core design tokens */
	$enqueue_shs_style(
		'shs-variables',
		'variables.css'
	);

	/* Base/global styling */
	$enqueue_shs_style(
		'shs-base',
		'base.css',
		array( 'shs-variables' )
	);
	
	/* Designed WordPress pages: Services, About, Contact, etc. */
	if ( is_page() && ! is_front_page() ) {
		$enqueue_shs_style(
			'shs-pages',
			'pages.css',
			array( 'shs-variables', 'shs-base' )
		);
	}
	
	/* Blog / Guides archive */
	$enqueue_shs_style(
		'shs-blog-archive',
		'blog-archive.css',
		array( 'shs-variables', 'shs-base' )
	);
	
	/* Blog / Guides archive */
	$enqueue_shs_style(
		'single-post',
		'single-post.css',
		array( 'shs-variables', 'shs-base' )
	);

	/* Header */
	$enqueue_shs_style(
		'shs-header',
		'header.css',
		array( 'shs-variables', 'shs-base' )
	);

	/* Footer */
	$enqueue_shs_style(
		'shs-footer',
		'footer.css',
		array( 'shs-variables', 'shs-base' )
	);
	
	/* WooCommerce archive pages: shop, product categories, product tags */
	if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
		$enqueue_shs_style(
			'shs-woocommerce-archive',
			'woocommerce-archive.css',
			array( 'shs-variables', 'shs-base' )
		);
	}


	/* Utilities last so helper classes can override earlier files if needed */
	$enqueue_shs_style(
		'shs-utilities',
		'utilities.css',
		array( 'shs-variables', 'shs-base' )
	);

}, 20 );


/**
 * Limit guide/blog archive pages to 12 posts per page.
 */
function shs_limit_guides_archive_posts_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_archive() ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'shs_limit_guides_archive_posts_per_page' );


/**
 * Allow guide archive topic filtering with ?guide_topic=tag-slug.
 */
function shs_guides_archive_query_vars( $vars ) {
	$vars[] = 'guide_topic';
	$vars[] = 'guide_search';

	return $vars;
}
add_filter( 'query_vars', 'shs_guides_archive_query_vars' );


/**
 * Filter guide archives by extra topic/search query parameters.
 */
function shs_filter_guides_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_archive() ) ) {
		return;
	}

	$query->set( 'posts_per_page', 12 );

	$guide_topic = get_query_var( 'guide_topic' );
	$guide_search = get_query_var( 'guide_search' );

	if ( ! empty( $guide_search ) ) {
		$query->set( 's', sanitize_text_field( $guide_search ) );
	}

	if ( empty( $guide_topic ) ) {
		return;
	}

	$guide_topic = sanitize_title( $guide_topic );

	$tax_query = (array) $query->get( 'tax_query' );

	$tax_query[] = array(
		'taxonomy' => 'post_tag',
		'field'    => 'slug',
		'terms'    => array( $guide_topic ),
		'operator' => 'IN',
	);

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'shs_filter_guides_archive_query' );



/**
 * Remove old SHS ACF product sections from the WooCommerce summary.
 *
 * The new content-single-product.php outputs these fields manually,
 * so this old hook causes duplicate Key Features, Use Cases, Specs, etc.
 */
add_action( 'wp', function () {
	if ( is_product() ) {
		remove_action( 'woocommerce_single_product_summary', 'shs_render_acf_sections', 45 );
	}
}, 20 );


