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
 * Replace WooCommerce's disabled mini-cart on Cart and Checkout pages.
 *
 * WooCommerce intentionally renders the block hidden and disabled on these
 * pages. A normal cart link keeps the header utility visible and usable while
 * preserving the existing icon and quantity badge styling.
 *
 * @param string $block_content Rendered mini-cart block.
 * @return string
 */
function shs_render_cart_page_header_link( $block_content ) {
	if ( ! function_exists( 'is_cart' ) || ( ! is_cart() && ! is_checkout() ) ) {
		return $block_content;
	}

	$cart_count = 0;

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$cart_count = WC()->cart->get_cart_contents_count();
	}

	$cart_label = sprintf(
		/* translators: %d: Number of products in the cart. */
		_n( 'Cart, %d item', 'Cart, %d items', $cart_count, 'generatepress-child' ),
		$cart_count
	);

	$cart_badge = $cart_count > 0
		? '<span class="wc-block-mini-cart__badge">' . esc_html( $cart_count ) . '</span>'
		: '<span class="wc-block-mini-cart__badge"></span>';

	return '<div data-block-name="woocommerce/mini-cart" class="wc-block-mini-cart wp-block-woocommerce-mini-cart shs-static-mini-cart">'
		. '<a class="wc-block-mini-cart__button shs-static-mini-cart__link" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="' . esc_attr( $cart_label ) . '">'
		. '<span class="wc-block-mini-cart__quantity-badge">'
		. '<svg xmlns="http://www.w3.org/2000/svg" class="wc-block-mini-cart__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false">'
		. '<circle cx="12.667" cy="24.667" r="2"></circle>'
		. '<circle cx="23.333" cy="24.667" r="2"></circle>'
		. '<path fill-rule="evenodd" d="M9.285 10.036a1 1 0 0 1 .776-.37h15.272a1 1 0 0 1 .99 1.142l-1.333 9.333A1 1 0 0 1 24 21H12a1 1 0 0 1-.98-.797L9.083 10.87a1 1 0 0 1 .203-.834m2.005 1.63L12.814 19h10.319l1.047-7.333z" clip-rule="evenodd"></path>'
		. '<path fill-rule="evenodd" d="M5.667 6.667a1 1 0 0 1 1-1h2.666a1 1 0 0 1 .984.82l.727 4a1 1 0 1 1-1.967.359l-.578-3.18H6.667a1 1 0 0 1-1-1" clip-rule="evenodd"></path>'
		. '</svg>'
		. $cart_badge
		. '</span>'
		. '</a>'
		. '</div>';
}
add_filter( 'render_block_woocommerce/mini-cart', 'shs_render_cart_page_header_link', 20 );

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
