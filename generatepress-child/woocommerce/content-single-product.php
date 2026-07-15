<?php
/**
 * SmartHomeShopUK custom single product template.
 *
 * Location:
 * /wp-content/themes/YOUR-CHILD-THEME/woocommerce/content-single-product.php
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
	return;
}

$product_id = $product->get_id();

/* =========================================================
   Helpers
   ========================================================= */

if ( ! function_exists( 'shs_get_acf_field' ) ) {
	function shs_get_acf_field( $field_name, $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $field_name, $post_id );
		}

		return get_post_meta( $post_id, $field_name, true );
	}
}

if ( ! function_exists( 'shs_render_textarea_list' ) ) {
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
}

if ( ! function_exists( 'shs_render_spec_table' ) ) {
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
			if ( strpos( $line, ':' ) !== false ) {
				$parts = explode( ':', $line, 2 );

				echo '<div class="shs-spec-row">';
				echo '<div class="shs-spec-label">' . esc_html( trim( $parts[0] ) ) . '</div>';
				echo '<div class="shs-spec-value">' . esc_html( trim( $parts[1] ) ) . '</div>';
				echo '</div>';
			} else {
				echo '<div class="shs-spec-row">';
				echo '<div class="shs-spec-value shs-spec-value-full">' . esc_html( $line ) . '</div>';
				echo '</div>';
			}
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'shs_render_product_information_item' ) ) {
	/**
	 * Renders a native product-information accordion item.
	 *
	 * No JavaScript is required. The browser handles opening,
	 * closing and keyboard interaction through <details>/<summary>.
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
}

if ( ! function_exists( 'shs_render_sidebar_upsells' ) ) {
	function shs_render_sidebar_upsells( $product, $limit = 3 ) {
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$upsell_ids = array_slice( $product->get_upsell_ids(), 0, $limit );
		$upsells    = array();

		foreach ( $upsell_ids as $upsell_id ) {
			$upsell_product = wc_get_product( $upsell_id );

			if ( ! $upsell_product || ! $upsell_product->is_visible() ) {
				continue;
			}

			$upsells[] = array(
				'id'      => $upsell_id,
				'product' => $upsell_product,
			);
		}

		if ( empty( $upsells ) ) {
			return;
		}

		echo '<div class="shs-sidebar-card shs-sidebar-upsells-card">';
		echo '<h2>Recommended upgrades</h2>';
		echo '<div class="shs-sidebar-upsells">';

		foreach ( $upsells as $upsell ) {
			$upsell_id      = $upsell['id'];
			$upsell_product = $upsell['product'];

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
}

/* =========================================================
   Product fields
   ========================================================= */

$product_description      = get_post_field( 'post_content', $product_id );
$key_features             = shs_get_acf_field( 'key_features', $product_id );
$use_cases                = shs_get_acf_field( 'use_cases', $product_id );
$technical_specifications = shs_get_acf_field( 'technical_specifications', $product_id );
$whats_included           = shs_get_acf_field( 'whats_included', $product_id );
$guides_resources         = shs_get_acf_field( 'guides_resources', $product_id );

if ( ! empty( $guides_resources ) && ! is_array( $guides_resources ) ) {
	$guides_resources = array( $guides_resources );
}

/* =========================================================
   Prepare selected guides
   ========================================================= */

$guides = array();

if ( ! empty( $guides_resources ) ) {
	foreach ( $guides_resources as $guide ) {
		$guide_id = is_object( $guide ) ? (int) $guide->ID : (int) $guide;

		if ( ! $guide_id || 'publish' !== get_post_status( $guide_id ) ) {
			continue;
		}

		$guide_title = get_the_title( $guide_id );
		$guide_url   = get_permalink( $guide_id );

		if ( empty( $guide_title ) || empty( $guide_url ) ) {
			continue;
		}

		$guides[] = array(
			'title' => $guide_title,
			'url'   => $guide_url,
		);
	}
}

$has_product_information =
	! empty( $use_cases ) ||
	! empty( $technical_specifications ) ||
	! empty( $whats_included ) ||
	! empty( $guides );

?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'shs-single-product', $product ); ?>>

	<?php
	do_action( 'woocommerce_before_single_product' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
	?>

	<section class="shs-product-hero">

		<div class="shs-product-gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>

		<div class="shs-product-summary summary entry-summary">
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
		</div>

	</section>

	<section class="shs-product-layout">

		<main class="shs-product-content" aria-label="Product details">

			<?php if ( ! empty( trim( wp_strip_all_tags( $product_description ) ) ) ) : ?>
				<section class="shs-product-section shs-product-overview-section">

					<div class="shs-section-heading">
						<h2>Overview</h2>
					</div>

					<div class="shs-section-content shs-product-overview-content">
						<?php echo apply_filters( 'the_content', $product_description ); ?>
					</div>

				</section>
			<?php endif; ?>

			<?php if ( ! empty( $key_features ) ) : ?>
				<section class="shs-product-section shs-product-features-section">

					<div class="shs-section-heading">
						<h2>Key features</h2>
					</div>

					<div class="shs-section-content">
						<?php shs_render_textarea_list( $key_features, 'shs-key-feature-list' ); ?>
					</div>

				</section>
			<?php endif; ?>

			<?php if ( $has_product_information ) : ?>
				<section class="shs-product-information" aria-labelledby="shs-product-information-title">

					<div class="shs-product-information__heading">
						<h2 id="shs-product-information-title">Product information</h2>
					</div>

					<div class="shs-product-information__items">

						<?php
						if ( ! empty( $use_cases ) ) {
							shs_render_product_information_item(
								'Use cases',
								'shs-product-use-cases-section',
								static function() use ( $use_cases ) {
									shs_render_textarea_list( $use_cases, 'shs-use-case-list' );
								}
							);
						}

						if ( ! empty( $technical_specifications ) ) {
							shs_render_product_information_item(
								'Technical specifications',
								'shs-product-specs-section',
								static function() use ( $technical_specifications ) {
									shs_render_spec_table( $technical_specifications );
								}
							);
						}

						if ( ! empty( $whats_included ) ) {
							shs_render_product_information_item(
								'What’s included',
								'shs-product-included-section',
								static function() use ( $whats_included ) {
									shs_render_textarea_list( $whats_included, 'shs-included-list' );
								}
							);
						}

						if ( ! empty( $guides ) ) {
							shs_render_product_information_item(
								'Guides and resources',
								'shs-product-resources-section',
								static function() use ( $guides ) {
									echo '<div class="shs-resource-list">';

									foreach ( $guides as $guide ) {
										echo '<a class="shs-resource-card" href="' . esc_url( $guide['url'] ) . '">';
										echo '<span>' . esc_html( $guide['title'] ) . '</span>';
										echo '<span aria-hidden="true">→</span>';
										echo '</a>';
									}

									echo '</div>';
								}
							);
						}
						?>

					</div>

				</section>
			<?php endif; ?>

		</main>

		<aside class="shs-product-sidebar">

			<?php shs_render_sidebar_upsells( $product, 3 ); ?>

			<div class="shs-sidebar-card shs-help-card">

				<h2>Need help choosing?</h2>

				<p>
					Not sure whether this is right for your setup? Send us your current hub,
					coordinator, and what you want to achieve.
				</p>

				<a class="shs-button shs-button-primary" href="/request-help/">
					Request help
				</a>

			</div>

		</aside>

	</section>

	<?php if ( function_exists( 'woocommerce_output_related_products' ) ) : ?>
		<section class="shs-related-products">
			<?php woocommerce_output_related_products(); ?>
		</section>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_single_product' ); ?>

</div>