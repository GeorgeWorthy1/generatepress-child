<?php
/**
 * SmartHomeShopUK recent products section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$recent_products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => 4,
		'orderby' => 'date',
		'order'   => 'DESC',
		'return'  => 'objects',
	)
);

if ( empty( $recent_products ) ) {
	return;
}
?>

<section class="shs-recent-products" aria-label="Recently added smart home products">

	<div class="shs-recent-products__header">

		<div class="shs-recent-products__intro">
			<h2>Recently Added Products</h2>
			<p>Explore the latest smart home products added to SmartHomeShopUK.</p>
		</div>

		<a class="shs-recent-products__view-all shs-button-secondary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			View all products
		</a>

	</div>

	<div class="shs-recent-products__grid">

		<?php foreach ( $recent_products as $product ) : ?>

			<?php
			$product_id  = $product->get_id();
			$product_url = get_permalink( $product_id );
			$image_id    = $product->get_image_id();
			?>

			<article class="shs-recent-product-card">

				<a class="shs-recent-product-card__link" href="<?php echo esc_url( $product_url ); ?>">

					<div class="shs-recent-product-card__image-wrap">
						<?php
						if ( $image_id ) {
							echo wp_get_attachment_image(
								$image_id,
								'woocommerce_thumbnail',
								false,
								array(
									'class' => 'shs-recent-product-card__image',
									'alt'   => esc_attr( $product->get_name() ),
								)
							);
						} else {
							echo wc_placeholder_img(
								'woocommerce_thumbnail',
								array(
									'class' => 'shs-recent-product-card__image',
								)
							);
						}
						?>
					</div>

					<div class="shs-recent-product-card__content">

						<h3><?php echo esc_html( $product->get_name() ); ?></h3>

						<?php if ( $product->get_price_html() ) : ?>
							<div class="shs-recent-product-card__price">
								<?php echo wp_kses_post( $product->get_price_html() ); ?>
							</div>
						<?php endif; ?>

					</div>

				</a>

			</article>

		<?php endforeach; ?>

	</div>

</section>