<?php
/**
 * Guide archive queries and filters.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the query parameters used by the guide archive toolbar.
 *
 * @param string[] $vars Public query variables.
 * @return string[]
 */
function shs_register_guide_query_vars( $vars ) {
	$vars[] = 'guide_topic';
	$vars[] = 'guide_search';

	return $vars;
}
add_filter( 'query_vars', 'shs_register_guide_query_vars' );

/**
 * Get the root Guides category.
 *
 * @return WP_Term|null
 */
function shs_get_guides_category() {
	static $guides_category = null;
	static $resolved        = false;

	if ( ! $resolved ) {
		$term            = get_category_by_slug( 'guides' );
		$guides_category = $term instanceof WP_Term ? $term : null;
		$resolved        = true;
	}

	return $guides_category;
}

/**
 * Check whether a category is Guides or one of its child categories.
 *
 * @param int $category_id Category term ID.
 * @return bool
 */
function shs_is_guides_category( $category_id ) {
	$guides_category = shs_get_guides_category();
	$category_id     = absint( $category_id );

	if ( ! $guides_category || ! $category_id ) {
		return false;
	}

	return (int) $guides_category->term_id === $category_id
		|| cat_is_ancestor_of( (int) $guides_category->term_id, $category_id );
}

/**
 * Check whether the current request is a Guides category archive.
 *
 * @return bool
 */
function shs_is_guides_archive() {
	return is_category() && shs_is_guides_category( get_queried_object_id() );
}

/**
 * Apply pagination, topic and search filters only to Guides archives.
 *
 * @param WP_Query $query Main WordPress query.
 */
function shs_filter_guides_archive_query( $query ) {
	if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() || ! $query->is_category() ) {
		return;
	}

	if ( ! shs_is_guides_category( $query->get_queried_object_id() ) ) {
		return;
	}

	$query->set( 'posts_per_page', 12 );

	$guide_search = $query->get( 'guide_search' );

	if ( is_string( $guide_search ) ) {
		$guide_search = trim( sanitize_text_field( wp_unslash( $guide_search ) ) );

		if ( '' !== $guide_search ) {
			$query->set( 's', $guide_search );
		}
	}

	$guide_topic = $query->get( 'guide_topic' );

	if ( ! is_string( $guide_topic ) || '' === $guide_topic ) {
		return;
	}

	$guide_topic = sanitize_title( wp_unslash( $guide_topic ) );

	if ( '' === $guide_topic ) {
		return;
	}

	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => 'post_tag',
		'field'    => 'slug',
		'terms'    => array( $guide_topic ),
		'operator' => 'IN',
	);

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'shs_filter_guides_archive_query' );

