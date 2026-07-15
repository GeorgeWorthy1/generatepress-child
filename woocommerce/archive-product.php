<?php
/**
 * SmartHomeShopUK custom WooCommerce archive.
 */

defined( 'ABSPATH' ) || exit;

$has_product_filters = shortcode_exists( 'fe_widget' );

get_header();
?>

<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>

		<?php do_action( 'generate_before_main_content' ); ?>

		<?php get_template_part( 'template-parts/shs-archive-hero' ); ?>

		<div class="shs-product-archive-container">

			<?php woocommerce_output_all_notices(); ?>

			<?php if ( $has_product_filters ) : ?>
				<div class="shs-product-archive-topbar">

					<?php if ( shortcode_exists( 'fe_open_button' ) ) : ?>
						<div class="shs-product-archive-mobile-filter-button">
							<?php echo do_shortcode( '[fe_open_button]' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( shortcode_exists( 'fe_chips' ) ) : ?>
						<div class="shs-product-archive-chips">
							<?php echo do_shortcode( '[fe_chips]' ); ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

			<div class="shs-product-archive-layout<?php echo $has_product_filters ? '' : ' shs-product-archive-layout--no-filters'; ?>">

				<?php if ( $has_product_filters ) : ?>
					<aside class="shs-product-archive-sidebar" aria-label="Product filters">

						<div class="shs-product-archive-sidebar__inner">

							<h2 class="shs-product-archive-sidebar__title">Filter products</h2>

							<?php echo do_shortcode( '[fe_widget]' ); ?>

						</div>

					</aside>
				<?php endif; ?>

				<section
					id="shs-product-results"
					class="shs-product-archive-results"
					aria-label="Product results"
				>

					<?php if ( woocommerce_product_loop() ) : ?>

						<div class="shs-product-archive-toolbar">

							<div class="shs-product-archive-count">
								<?php woocommerce_result_count(); ?>
							</div>

							<div class="shs-product-archive-sort">
								<?php woocommerce_catalog_ordering(); ?>
							</div>

						</div>

						<?php woocommerce_product_loop_start(); ?>

						<?php while ( have_posts() ) : ?>
							<?php
							the_post();
							wc_get_template_part( 'content', 'product' );
							?>
						<?php endwhile; ?>

						<?php woocommerce_product_loop_end(); ?>

						<?php get_template_part( 'template-parts/shs-archive-pagination' ); ?>

					<?php else : ?>

						<?php do_action( 'woocommerce_no_products_found' ); ?>

					<?php endif; ?>

				</section>

			</div>

			<?php get_template_part( 'template-parts/recent-guides' ); ?>

		</div>

		<?php do_action( 'generate_after_main_content' ); ?>

	</main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
get_footer();
