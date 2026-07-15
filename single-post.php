<?php
/**
 * Single blog post template.
 *
 * SmartHomeShopUK
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main shs-single-post">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'shs-post-article' ); ?>>

			<header class="shs-post-hero">

				<div class="shs-post-hero-content">

					<?php
					$categories = get_the_category();

					if ( ! empty( $categories ) ) :
						?>
						<a class="shs-post-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
							<?php echo esc_html( $categories[0]->name ); ?>
						</a>
					<?php endif; ?>

					<h1 class="shs-post-title"><?php the_title(); ?></h1>

					<div class="shs-post-meta">
						<?php if ( get_the_modified_date( 'Y-m-d' ) !== get_the_date( 'Y-m-d' ) ) : ?>
							<span>Updated on <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></span>
						<?php else : ?>
							<span>Written on <?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
						<?php endif; ?>
					</div>

					<?php
					$post_tags = get_the_tags();

					if ( ! empty( $post_tags ) ) :
						?>
						<div class="shs-post-tags" aria-label="Post tags">
							<?php foreach ( $post_tags as $tag ) : ?>
								<a class="shs-post-tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
									<?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				</div>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="shs-post-hero-image">
						<?php
						the_post_thumbnail(
							'large',
							array(
								'class' => 'shs-post-hero-image__img',
								'alt'   => esc_attr( get_the_title() ),
							)
						);
						?>
					</div>
				<?php endif; ?>

			</header>

			<div class="shs-post-layout">

				<div class="shs-post-main">

					<div class="shs-post-content">
						<?php the_content(); ?>
					</div>

					<?php get_template_part( 'template-parts/recent-products' ); ?>

					<?php get_template_part( 'template-parts/recent-guides' ); ?>

				</div>

			</div>

		</article>

	<?php endwhile; ?>

</main>

<?php
get_footer();