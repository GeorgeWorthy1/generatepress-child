<?php
/**
 * SmartHomeShopUK recent guides/articles section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$guide_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
	)
);

if ( ! $guide_query->have_posts() ) {
	return;
}
?>

<section class="shs-archive-guides" aria-label="New smart home articles">

	<div class="shs-archive-guides__header">

		<div class="shs-archive-guides__intro">
			<h2>New Smart Home Articles</h2>
			<p>Helpful guides for choosing, installing and automating your smart home devices.</p>
		</div>

		<a class="shs-archive-guides__view-all shs-button-secondary" href="<?php echo esc_url( home_url( '/guides/' ) ); ?>">
			View all guides
		</a>

	</div>

	<div class="shs-archive-guides__grid">

		<?php while ( $guide_query->have_posts() ) : ?>
			<?php $guide_query->the_post(); ?>

			<article class="shs-archive-guide-card">

				<a class="shs-archive-guide-card__link" href="<?php the_permalink(); ?>">

					<div class="shs-archive-guide-card__image-wrap">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php
							the_post_thumbnail(
								'medium_large',
								array(
									'class' => 'shs-archive-guide-card__image',
									'alt'   => esc_attr( get_the_title() ),
								)
							);
							?>
						<?php else : ?>
							<div class="shs-archive-guide-card__placeholder"></div>
						<?php endif; ?>
					</div>

					<div class="shs-archive-guide-card__content">
						<h3><?php the_title(); ?></h3>

						<div class="shs-archive-guide-card__date">
							<?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
						</div>
					</div>

				</a>

			</article>

		<?php endwhile; ?>

	</div>

</section>

<?php wp_reset_postdata(); ?>