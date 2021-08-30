<?php
/**
 * Get posts with terms.
 *
 * @package taro-taxonomy-blocks
 * @var array{ query: WP_Query[], terms: WP_Term[] } $args
 */

?>
<ul class="taro-taxonomy-query-block">
	<?php
	while ( $args['query']->have_posts() ) {
		$args['query']->the_post();
		taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/post-loop', get_post_type(), $args );
	}
	wp_reset_postdata();
	?>
</ul>

