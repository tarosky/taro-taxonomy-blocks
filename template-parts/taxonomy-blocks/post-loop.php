<?php
/**
 * Loop.
 *
 * @package taro-taxonomy-blocks
 */

?>
<li class="taro-taxonomy-query-item">
	<a href="<?php the_permalink(); ?>" class="taro-taxonomy-query-link">
		<?php if ( has_post_thumbnail() ) : ?>
		<figure class="taro-taxonomy-query-thumbnail">
			<?php the_post_thumbnail( 'thumbnail', [ 'class' => 'taro-taxonomy-query-image', 'alt' => get_the_title() ] ); ?>
		</figure>
		<?php endif; ?>
		<div class="taro-taxonomy-query-body">
			<span class="taro-taxonomy-query-title"><?php the_title(); ?></span>
			<time class="taro-taxonomy-query-date"><?php the_time( get_option( 'date_format' ) ); ?></time>
		</div>
	</a>
</li>
