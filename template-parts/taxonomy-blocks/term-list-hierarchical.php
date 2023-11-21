<?php
/**
 * Get tem list.
 *
 * @package taro-taxonomy-blocks
 * @var array{ parent: int, terms: WP_Term[], className: string } $args
 */

$parent = empty( $args['parent'] ) ? 0 : $args['parent'];
$terms  = array_values( array_filter( $args['terms'], function( $term ) use ( $parent ) {
	return (int) $parent === (int) $term->parent;
} ) );
if ( ! $terms ) {
	// If no matching terms, do nothing.
	return;
}

$list_classes   = [ 'taro-taxonomy-list', 'taro-taxonomy-list-hierarchical' ];
$list_classes[] = $parent ? 'taro-taxonomy-list-child' : 'taro-taxonomy-list-parent';
if ( $args['className'] ) {
	$list_classes[] = $args['className'];
}
?>

<ul class="<?php echo esc_attr( implode( ' ', $list_classes ) ); ?>">
	<?php
	foreach ( $terms as $term ) :
		$classes   = [ 'taro-taxonomy-item', 'taro-taxonomy-item-hierarchical' ];
		$classes[] = $parent ? 'taro-taxonomy-item-child' : 'taro-taxonomy-item-parent';
		?>
		<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-taxonomy="<?php echo esc_attr( $term->taxonomy ); ?>">
			<?php
			taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-item', '', [ 'term' => $term ] );
			taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list-hierarchical', $term->taxonomy, [
				'terms'  => $args['terms'],
				'parent' => $term->term_id,
			] );
			?>
		</li>
	<?php endforeach; ?>
</ul>
