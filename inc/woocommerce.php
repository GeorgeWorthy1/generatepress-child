<?php
/**
 * WooCommerce presentation helpers and integrations.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the generated "Page X" item from Yoast breadcrumbs.
 *
 * @param array[] $links Breadcrumb links.
 * @return array[]
 */
function shs_remove_yoast_paged_breadcrumb( $links ) {
	if ( ! is_paged() ) {
		return $links;
	}

	foreach ( $links as $key => $link ) {
		if ( isset( $link['text'] ) && preg_match( '/^Page\s+\d+$/i', trim( $link['text'] ) ) ) {
			unset( $links[ $key ] );
		}
	}

	return array_values( $links );
}
add_filter( 'wpseo_breadcrumb_links', 'shs_remove_yoast_paged_breadcrumb', 20 );

/**
 * Remove the generated "Page X" item from WooCommerce breadcrumbs.
 *
 * @param array[] $crumbs Breadcrumb items.
 * @return array[]
 */
function shs_remove_woocommerce_paged_breadcrumb( $crumbs ) {
	if ( ! is_paged() ) {
		return $crumbs;
	}

	foreach ( $crumbs as $key => $crumb ) {
		if ( isset( $crumb[0] ) && preg_match( '/^Page\s+\d+$/i', trim( $crumb[0] ) ) ) {
			unset( $crumbs[ $key ] );
		}
	}

	return array_values( $crumbs );
}
add_filter( 'woocommerce_get_breadcrumb', 'shs_remove_woocommerce_paged_breadcrumb', 20 );

/**
 * Get an ACF value with a post-meta fallback.
 *
 * @param string $field_name Field name.
 * @param int    $post_id    Product ID.
 * @return mixed
 */
function shs_get_acf_field( $field_name, $post_id ) {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $field_name, $post_id );
	}

	return get_post_meta( $post_id, $field_name, true );
}

/**
 * Render one list item for each non-empty line in a textarea.
 *
 * @param string $content Textarea content.
 * @param string $class   List class.
 */
function shs_render_textarea_list( $content, $class = 'shs-product-list' ) {
	if ( empty( $content ) ) {
		return;
	}

	$lines = preg_split( '/\r\n|\r|\n/', trim( wp_strip_all_tags( $content ) ) );
	$lines = array_filter( array_map( 'trim', $lines ) );

	if ( empty( $lines ) ) {
		return;
	}

	echo '<ul class="' . esc_attr( $class ) . '">';

	foreach ( $lines as $line ) {
		echo '<li>' . esc_html( $line ) . '</li>';
	}

	echo '</ul>';
}

/**
 * Render colon-separated product specifications.
 *
 * @param string $content Specifications textarea content.
 */
function shs_render_spec_table( $content ) {
	if ( empty( $content ) ) {
		return;
	}

	$lines = preg_split( '/\r\n|\r|\n/', trim( wp_strip_all_tags( $content ) ) );
	$lines = array_filter( array_map( 'trim', $lines ) );

	if ( empty( $lines ) ) {
		return;
	}

	echo '<div class="shs-spec-table">';

	foreach ( $lines as $line ) {
		echo '<div class="shs-spec-row">';

		if ( false !== strpos( $line, ':' ) ) {
			$parts = explode( ':', $line, 2 );
			echo '<div class="shs-spec-label">' . esc_html( trim( $parts[0] ) ) . '</div>';
			echo '<div class="shs-spec-value">' . esc_html( trim( $parts[1] ) ) . '</div>';
		} else {
			echo '<div class="shs-spec-value shs-spec-value-full">' . esc_html( $line ) . '</div>';
		}

		echo '</div>';
	}

	echo '</div>';
}

/**
 * Render a native details/summary product-information item.
 *
 * @param string   $title            Item title.
 * @param string   $class_name       Additional item class.
 * @param callable $content_callback Content renderer.
 */
function shs_render_product_information_item( $title, $class_name, $content_callback ) {
	if ( ! is_callable( $content_callback ) ) {
		return;
	}

	echo '<details class="shs-product-information__item ' . esc_attr( $class_name ) . '">';
	echo '<summary class="shs-product-information__summary">';
	echo '<span class="shs-product-information__title">' . esc_html( $title ) . '</span>';
	echo '<span class="shs-product-information__icon" aria-hidden="true"></span>';
	echo '</summary>';
	echo '<div class="shs-product-information__content">';
	call_user_func( $content_callback );
	echo '</div>';
	echo '</details>';
}

/**
 * Render up to three visible upsell products in the sidebar.
 *
 * @param WC_Product $product Current product.
 * @param int        $limit   Maximum products to display.
 */
function shs_render_sidebar_upsells( $product, $limit = 3 ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$upsell_ids = array_slice( $product->get_upsell_ids(), 0, absint( $limit ) );
	$upsells    = array();

	foreach ( $upsell_ids as $upsell_id ) {
		$upsell_product = wc_get_product( $upsell_id );

		if ( ! $upsell_product || ! $upsell_product->is_visible() ) {
			continue;
		}

		$upsells[ $upsell_id ] = $upsell_product;
	}

	if ( empty( $upsells ) ) {
		return;
	}

	echo '<div class="shs-sidebar-card shs-sidebar-upsells-card">';
	echo '<h2>' . esc_html__( 'Recommended upgrades', 'generatepress-child' ) . '</h2>';
	echo '<div class="shs-sidebar-upsells">';

	foreach ( $upsells as $upsell_id => $upsell_product ) {
		echo '<a class="shs-sidebar-upsell-product" href="' . esc_url( get_permalink( $upsell_id ) ) . '">';
		echo '<span class="shs-sidebar-upsell-image">';
		echo get_the_post_thumbnail( $upsell_id, 'woocommerce_thumbnail' );
		echo '</span>';
		echo '<span class="shs-sidebar-upsell-content">';
		echo '<span class="shs-sidebar-upsell-title">' . esc_html( get_the_title( $upsell_id ) ) . '</span>';
		echo '<span class="shs-sidebar-upsell-price">' . wp_kses_post( $upsell_product->get_price_html() ) . '</span>';
		echo '</span>';
		echo '</a>';
	}

	echo '</div>';
	echo '</div>';
}

/**
 * Prevent a legacy snippet from duplicating the custom product sections.
 */
function shs_remove_legacy_acf_product_sections() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		remove_action( 'woocommerce_single_product_summary', 'shs_render_acf_sections', 45 );
	}
}
add_action( 'wp', 'shs_remove_legacy_acf_product_sections', 20 );

