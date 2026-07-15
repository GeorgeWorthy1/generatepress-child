<?php
/**
 * SmartHomeShopUK blog / guide archive card.
 */

defined( 'ABSPATH' ) || exit;

$categories       = get_the_category();
$primary_category = ! empty( $categories ) ? $categories[0] : null;
?>

<article <?php post_class( 'shs-blog-card' ); ?>>

	<a
		class="shs-blog-card__link shs-content-card"
		href="<?php the_permalink(); ?>"
	>

		<div class="shs-blog-card__image-wrap shs-content-card__media">

			<?php if ( has_post_thumbnail() ) : ?>

				<?php
				the_post_thumbnail(
					'large',
					array(
						'class' => 'shs-blog-card__image',
						'alt'   => esc_attr( get_the_title() ),
					)
				);
				?>

			<?php else : ?>

				<div class="shs-blog-card__placeholder" aria-hidden="true"></div>

			<?php endif; ?>

		</div>

		<div class="shs-blog-card__content shs-content-card__content">

			<div class="shs-blog-card__meta">

				<?php if ( $primary_category ) : ?>
					<span class="shs-blog-card__category">
						<?php echo esc_html( $primary_category->name ); ?>
					</span>
				<?php endif; ?>

				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>

			</div>

			<h2 class="shs-blog-card__title">
				<?php the_title(); ?>
			</h2>

			<div class="shs-blog-card__excerpt">
				<?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
			</div>

			<span class="shs-blog-card__read-more shs-content-card__action">
				Read guide
			</span>

		</div>

	</a>

</article>
