<?php
/**
 * Render term item.
 *
 * @package taro-taxonomy-blocks
 * @var array{ term: WP_Term } $args
 */

?>
<a class="taro-taxonomy-item-link" data-taxonomy="<?php echo esc_attr( $args['term']->taxonomy ); ?>" href="<?php echo esc_url( get_term_link( $args['term'] ) ); ?>">
	<?php echo esc_html( $args['term']->name ); ?>
</a>
