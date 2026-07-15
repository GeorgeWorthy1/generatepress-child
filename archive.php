<?php
/**
 * SmartHomeShopUK custom blog/archive template.
 * Used for category, tag, author and standard WordPress archive pages.
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;

$archive_count = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;

$guides_category = function_exists( 'shs_get_guides_category' ) ? shs_get_guides_category() : null;
$all_guides_url  = $guides_category ? get_category_link( $guides_category ) : home_url( '/guides/' );
$is_guides_root  = $guides_category && is_category( (int) $guides_category->term_id );

$queried_object = get_queried_object();

$current_tag_id = is_tag() && isset( $queried_object->term_id ) ? (int) $queried_object->term_id : 0;

$active_guide_topic  = sanitize_title( (string) get_query_var( 'guide_topic' ) );
$active_guide_search = sanitize_text_field( (string) get_query_var( 'guide_search' ) );

$current_archive_url = remove_query_arg(
	array( 'guide_topic', 'guide_search', 'paged' ),
	get_pagenum_link( 1 )
);

/**
 * Topic links filter the current archive on the server.
 */
$topic_tags = get_tags(
	array(
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

/**
 * Sidebar related topics.
 */
$popular_tags = get_tags(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
	)
);
?>

<div <?php generate_do_attr( 'content' ); ?>>
	<main <?php generate_do_attr( 'main' ); ?>>

		<?php do_action( 'generate_before_main_content' ); ?>

		<?php get_template_part( 'template-parts/shs-archive-hero' ); ?>

		<div class="shs-blog-archive-container">

			<nav class="shs-blog-topic-nav" aria-label="Filter guides by topic">

				<a
					class="shs-blog-topic-nav__link <?php echo empty( $active_guide_topic ) && $is_guides_root ? 'is-active' : ''; ?>"
					href="<?php echo esc_url( $all_guides_url ); ?>"
				>
					All guides
				</a>

				<?php if ( ! empty( $topic_tags ) ) : ?>
					<?php foreach ( $topic_tags as $topic_tag ) : ?>

						<?php
						$filter_url = add_query_arg(
							array(
								'guide_topic' => $topic_tag->slug,
							),
							$current_archive_url
						);

						if ( ! empty( $active_guide_search ) ) {
							$filter_url = add_query_arg(
								array(
									'guide_search' => sanitize_text_field( $active_guide_search ),
								),
								$filter_url
							);
						}
						?>

						<a
							class="shs-blog-topic-nav__link <?php echo ( $active_guide_topic === $topic_tag->slug ) ? 'is-active' : ''; ?>"
							href="<?php echo esc_url( $filter_url ); ?>"
						>
							<?php echo esc_html( $topic_tag->name ); ?>
						</a>

					<?php endforeach; ?>
				<?php endif; ?>

			</nav>

			<div class="shs-blog-archive-layout">

				<section class="shs-blog-archive-results" aria-label="Guide results">

					<div class="shs-blog-archive-toolbar">

						<p class="shs-blog-archive-count">
							<?php
							printf(
								esc_html( _n( '%s guide found', '%s guides found', $archive_count, 'generatepress-child' ) ),
								esc_html( number_format_i18n( $archive_count ) )
							);
							?>
						</p>
						
						<form
							class="shs-blog-archive-search"
							role="search"
							method="get"
							action="<?php echo esc_url( $current_archive_url ); ?>"
							aria-label="Search this guide section"
						>

							<label class="screen-reader-text" for="shs-guide-archive-search">
								Search this section
							</label>

							<input
								id="shs-guide-archive-search"
								class="shs-blog-archive-search__input"
								type="search"
								name="guide_search"
								placeholder="Search this section..."
								value="<?php echo esc_attr( $active_guide_search ); ?>"
								autocomplete="off"
							>

							<?php if ( ! empty( $active_guide_topic ) ) : ?>
								<input
									type="hidden"
									name="guide_topic"
									value="<?php echo esc_attr( $active_guide_topic ); ?>"
								>
							<?php endif; ?>

						</form>

					</div>
					<?php if ( have_posts() ) : ?>

						<div class="shs-blog-archive-grid">

							<?php while ( have_posts() ) : ?>
								<?php the_post(); ?>

								<div class="shs-blog-card-filter-item">
									<?php get_template_part( 'template-parts/shs-blog-card' ); ?>
								</div>

							<?php endwhile; ?>

						</div>

						<?php get_template_part( 'template-parts/shs-archive-pagination' ); ?>

					<?php else : ?>

						<div class="shs-blog-archive-empty">

							<h2>No guides found</h2>

							<p>Try browsing another topic or viewing all smart home guides.</p>

							<a class="shs-blog-archive-empty__link shs-button-secondary" href="<?php echo esc_url( $all_guides_url ); ?>">
								View all guides
							</a>

						</div>

					<?php endif; ?>

				</section>

				<aside class="shs-blog-archive-sidebar" aria-label="Guide archive sidebar">

					<div class="shs-blog-archive-sidebar__inner">

						<section class="shs-blog-sidebar-block">

							<h2 class="shs-blog-sidebar-block__title">Hot topics</h2>

							<?php if ( ! empty( $popular_tags ) ) : ?>

								<ul class="shs-blog-sidebar-list">

									<?php foreach ( $popular_tags as $popular_tag ) : ?>

										<?php
										if ( $current_tag_id === (int) $popular_tag->term_id ) {
											continue;
										}
										?>

										<li>
											<a href="<?php echo esc_url( get_tag_link( $popular_tag ) ); ?>">
												<?php echo esc_html( $popular_tag->name ); ?>
											</a>
										</li>

									<?php endforeach; ?>

								</ul>

							<?php else : ?>

								<p>No related topics found.</p>

							<?php endif; ?>

						</section>

						<section class="shs-blog-sidebar-block shs-blog-sidebar-block--request">

							<h2 class="shs-blog-sidebar-block__title">Request a guide</h2>

							<p>
								Can’t find what you’re looking for? Tell us what guide or smart home problem you’d like us to cover, and we'll see if we can help.
							</p>

							<a class="shs-blog-sidebar-cta shs-button-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
								Request a guide
							</a>

						</section>

					</div>

				</aside>

			</div>

		</div>

		<?php do_action( 'generate_after_main_content' ); ?>

	</main>
</div>

<?php
do_action( 'generate_after_primary_content_area' );
get_footer();
