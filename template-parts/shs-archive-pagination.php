<?php
/**
 * SmartHomeShopUK simple product archive pagination.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$current_page = max( 1, get_query_var( 'paged' ) );
$total_pages  = $wp_query->max_num_pages;

if ( $total_pages <= 1 ) {
	return;
}
?>

<nav class="shs-simple-pagination" aria-label="Product pagination">

	<?php if ( $current_page > 1 ) : ?>
		<a class="shs-page-prev" href="<?php echo esc_url( get_pagenum_link( $current_page - 1 ) ); ?>">
			← Previous
		</a>
	<?php else : ?>
		<span class="shs-page-prev shs-page-disabled">
			← Previous
		</span>
	<?php endif; ?>

	<span class="shs-page-count">
		Page <?php echo esc_html( $current_page ); ?> of <?php echo esc_html( $total_pages ); ?>
	</span>

	<?php if ( $current_page < $total_pages ) : ?>
	<a class="shs-page-next" href="<?php echo esc_url( get_pagenum_link( $current_page + 1 ) ); ?>">
		Next →
	</a>
	<?php else : ?>
		<span class="shs-page-next shs-page-disabled">
			Next →
		</span>
	<?php endif; ?>

</nav>