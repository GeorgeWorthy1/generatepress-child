<?php
/**
 * SmartHomeShopUK shared archive hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = '';

if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {

	$title = woocommerce_page_title( false );

	if ( is_shop() ) {
		$shop_page_id = wc_get_page_id( 'shop' );
		$title        = get_the_title( $shop_page_id );
	}

} elseif ( is_category() || is_tag() || is_tax() ) {

	$title = single_term_title( '', false );

} elseif ( is_author() ) {

	$title = get_the_author();

} elseif ( is_home() ) {

	$blog_page_id = get_option( 'page_for_posts' );
	$title        = $blog_page_id ? get_the_title( $blog_page_id ) : 'Guides';

} elseif ( is_post_type_archive() ) {

	$title = post_type_archive_title( '', false );

} else {

	$title = get_the_archive_title();

}

if ( empty( $title ) ) {
	$title = get_the_archive_title();
}

$banner_url = 'https://exp.smarthomeshopuk.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-28-2026-03_54_20-PM-1024x546.png';
?>

<div class="gb-element-1b80f07f shs-page-hero shs-product-archive-hero-wrap">
	<div class="wp-block-cover shs-page-hero-cover shs-product-archive-hero-cover" style="min-height:280px;aspect-ratio:unset;">

		<img
			class="wp-block-cover__image-background"
			alt=""
			src="<?php echo esc_url( $banner_url ); ?>"
			data-object-fit="cover"
		>

		<div class="wp-block-cover__inner-container is-layout-constrained wp-block-cover-is-layout-constrained">
			<div class="gb-element-ee25c9db shs-page-hero-inner shs-product-archive-hero-inner">

				<h1 class="wp-block-heading has-text-align-center has-large-font-size">
					<?php echo esc_html( $title ); ?>
				</h1>

				<div class="shs-product-archive-breadcrumbs">
					<?php
					if ( function_exists( 'yoast_breadcrumb' ) ) {
						yoast_breadcrumb( '', '' );
					} elseif ( function_exists( 'woocommerce_breadcrumb' ) && ( is_shop() || is_product_taxonomy() ) ) {
						woocommerce_breadcrumb();
					}
					?>
				</div>

			</div>
		</div>

	</div>
</div>