<?php
/**
 * Get tem list.
 *
 * @package taro-taxonomy-blocks
 * @var array{ terms: WP_Term[], className: string } $args
 */

?>

<ul class="taro-taxonomy-list<?php echo $args['className'] ? esc_attr( ' ' . $args['className'] ) : ''; ?>">
	<?php foreach ( $args['terms'] as $term ) : ?>
	<li class="taro-taxonomy-item taro-taxonomy-item" data-taxonomy="<?php echo esc_attr( $term->taxonomy ); ?>">
		<?php taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-item', '', [ 'term' => $term ] ); ?>
	</li>
	<?php endforeach; ?>
</ul>
