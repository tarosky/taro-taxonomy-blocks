<?php
/**
Plugin Name: Taro Taxonomy Blocks
Plugin URI: https://wordpress.org/plugins/taro-taxonomy-blocks/
Description: Add 3 taxonomy blockshh for block editor.
Author: Tarosky INC.
Version: nightly
Author URI: https://tarosky.co.jp/
License: GPL3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: taro-taxonomy-blocks
Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

/**
 * Register assets
 */
function taro_taxonomy_blocks_assets() {
	// Register assets.
	$data    = get_file_data( __FILE__, [
		'version' => 'Version',
	] );
	$base    = plugin_dir_url( __FILE__ ) . 'dist';
	$version = $data['version'];
	wp_register_script( 'taro-taxonomy-selector', $base . '/js/taxonomy-selector.js', [ 'wp-i18n', 'wp-components' ], $version, true );
	wp_register_script( 'taro-terms-block-editor', $base . '/js/block-terms.js', [ 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'taro-taxonomy-selector' ], $version, true );
	wp_register_script( 'taro-post-terms-block-editor', $base . '/js/block-posts-terms.js', [ 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'taro-taxonomy-selector' ], $version, true );
	wp_register_script( 'taro-post-terms-query-block-editor', $base . '/js/block-posts-terms-query.js', [ 'wp-i18n', 'wp-data', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'taro-taxonomy-selector' ], $version, true );
	wp_register_style( 'taro-terms-block-editor', $base . '/css/editor-block-terms.css', [], $version );
	wp_register_style( 'taro-post-terms-block-editor', $base . '/css/editor-block-posts-terms.css', [], $version );
	wp_register_style( 'taro-terms-block', $base . '/css/style-block-terms.css', [], $version );
	wp_register_style( 'taro-post-terms-block', $base . '/css/style-block-posts-terms.css', [], $version );
	// Register blocks.
	register_block_type( 'taro/terms', [
		'attributes'      => taro_taxonomy_terms_blocks_option(),
		'render_callback' => 'taro_taxonomy_blocks_callback_terms',
		'editor_script'   => 'taro-terms-block-editor',
		'editor_style'    => 'taro-terms-block-editor',
		'style'           => 'taro-terms-block',
	] );
	register_block_type( 'taro/post-terms', [
		'attributes'      => taro_taxonomy_terms_blocks_option( 'post_terms' ),
		'render_callback' => 'taro_taxonomy_blocks_callback_post_terms',
		'editor_script'   => 'taro-post-terms-block-editor',
		'editor_style'    => 'taro-terms-block-editor',
		'style'           => 'taro-terms-block',
	] );
	register_block_type( 'taro/post-terms-query', [
		'attributes'      => taro_taxonomy_terms_blocks_option( 'posts' ),
		'render_callback' => 'taro_taxonomy_blocks_callback_post_terms_query',
		'editor_script'   => 'taro-post-terms-query-block-editor',
		'editor_style'    => 'taro-terms-block-editor',
		'style'           => 'taro-terms-block',
	] );
}

/**
 * Enqueue assets for editor.
 */
function taro_taxonomy_blocks_enqueue_editor() {
	wp_localize_script( 'taro-taxonomy-selector', 'TaroTaxonomySelector', [
		'taxonomies' => array_values( get_taxonomies( [
			'public' => true,
		], OBJECT ) ),
	] );
	wp_set_script_translations( 'taro-terms-block-editor', 'taro-taxonomy-blocks' );
	wp_localize_script( 'taro-terms-block-editor', 'TaroTermsBlockEditor', [
		'attributes' => taro_taxonomy_terms_blocks_option(),
	] );
	wp_set_script_translations( 'taro-post-terms-block-editor', 'taro-taxonomy-blocks' );
	wp_localize_script( 'taro-post-terms-block-editor', 'TaroPostTermsBlockEditor', [
		'attributes' => taro_taxonomy_terms_blocks_option( 'post_terms' ),
	] );
	wp_set_script_translations( 'taro-post-terms-query-block-editor', 'taro-taxonomy-blocks' );
	wp_localize_script( 'taro-post-terms-query-block-editor', 'TaroPostTermsQueryBlockEditor', [
		'attributes' => taro_taxonomy_terms_blocks_option( 'posts' ),
	] );
}

/**
 * Option for iframe block.
 *
 * @param string $target posts, post_terms, terms.
 * @return array[]
 */
function taro_taxonomy_terms_blocks_option( $target = '' ) {
	$args = [
		'taxonomy' => [
			'type'    => 'string',
			'default' => '',
		],
	];
	switch ( $target ) {
		case 'posts':
			return array_merge( $args, [
				'post_type' => [
					'type'    => 'string',
					'default' => '',
				],
				'limit'     => [
					'type'    => 'number',
					'default' => (int) apply_filters( 'taro_taxonomy_blocks_posts_per_page', get_option( 'posts_per_page', 10 ) ),
				],
				'orderby'   => [
					'type'    => 'string',
					'default' => 'date',
				],
				'order'     => [
					'type'    => 'string',
					'default' => 'DESC',
				],
			] );
		case 'post_terms':
			return $args;
		case 'terms':
		default:
			return array_merge( $args, [
				'ordeby'     => [
					'type'    => 'string',
					'default' => 'name',
				],
				'order'      => [
					'type'    => 'string',
					'default' => 'ASC',
				],
				'meta'       => [
					'type'    => 'string',
					'default' => '',
				],
				'hide_empty' => [
					'type'    => 'bool',
					'default' => true,
				],
			] );
	}
}

/**
 * Parse attributes.
 *
 * @param array $attributes REST attributes.
 * @param bool  $target     Target.
 * @see taro_taxonomy_terms_blocks_option()
 * @return array
 */
function taro_taxonomy_parse_args( $attributes, $target = '' ) {
	$default_args = [];
	foreach ( taro_taxonomy_terms_blocks_option( $target ) as $key => $setting ) {
		$default_args[ $key ] = $setting['default'];
	}
	return wp_parse_args( $attributes, $default_args );
}

/**
 * Get template.
 *
 * @param string $name   Template name.
 * @param string $suffix Suffix.
 * @param array  $args   Arguments.
 */
function taro_taxonomy_blocks_get_template_part( $name, $suffix = '', $args = [] ) {
	$dirs = [ get_stylesheet_directory() ];
	if ( get_stylesheet_directory() !== get_template_directory() ) {
		$dirs[] = get_template_directory();
	}
	$dirs[] = __DIR__;
	$files  = [ $name . '.php' ];
	if ( $suffix ) {
		array_unshift( $files, $name . '-' . $suffix . '.php' );
	}
	$found = '';
	foreach ( $files as $file ) {
		foreach ( $dirs as $dir ) {
			$path = trailingslashit( $dir ) . ltrim( $file, '/' );
			if ( file_exists( $path ) ) {
				$found = $path;
				break 2;
			}
		}
	}
	$found = apply_filters( 'taro_taxonomy_blocks_template', $found, $name, $suffix );
	if ( $found ) {
		load_template( $found, false, $args );
	}
}

/**
 * Render dynamic block.
 *
 * @param array  $attributes Options.
 * @param string $content    Body.
 *
 * @return string
 */
function taro_taxonomy_blocks_callback_terms( $attributes = [], $content = '' ) {
	// Create default args.
	$attributes = taro_taxonomy_parse_args( $attributes );
	$term_args  = [
		'taxonomy'   => $attributes['taxonomy'],
		'hide_empty' => $attributes['hide_empty'],
	];
	if ( $attributes['meta'] ) {
		$term_args['meta_key'] = $attributes['meta'];
		$term_args['ordeby']   = 'meta_value';
	} else {
		$term_args['ordeby'] = $attributes['ordeby'];
	}
	$term_args['order'] = $attributes['order'];
	$terms              = get_terms( $term_args );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	$taxonomy = get_taxonomy( $term_args['taxonomy'] );
	ob_start();
	if ( $taxonomy->hierarchical ) {
		taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list-hierarchical', $taxonomy->name, [
			'terms'  => $terms,
			'parent' => 0,
		] );
	} else {
		taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list', $taxonomy->name, [
			'terms' => $terms,
		] );
	}
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Render dynamic block.
 *
 * @param array  $attributes Options.
 * @param string $content    Body.
 *
 * @return string
 */
function taro_taxonomy_blocks_callback_post_terms( $attributes = [], $content = '' ) {
	// Create default args.
	$attributes = taro_taxonomy_parse_args( $attributes, 'post_terms' );
	$taxonomy   = get_taxonomy( $attributes['taxonomy'] );
	$terms      = get_the_terms( get_the_ID(), $attributes['taxonomy'] );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}
	ob_start();
	taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list', $taxonomy->name, [
		'terms' => $terms,
	] );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Render dynamic block for posts query.
 *
 * @param array  $attributes Options.
 * @param string $content    Body.
 *
 * @return string
 */
function taro_taxonomy_blocks_callback_post_terms_query( $attributes = [], $content = '' ) {
	// Create default args.
	$attributes = taro_taxonomy_parse_args( $attributes, 'posts' );
	$taxonomy   = get_taxonomy( $attributes['taxonomy'] );
	// Get assigned terms.
	$terms = get_the_terms( get_the_ID(), $attributes['taxonomy'] );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}
	// Posts query.
	$post_type = trim( $attributes['post_type'] );
	if ( empty( $attributes['post_type'] ) ) {
		$post_type = get_post_type();
	} elseif ( 'any' === $post_type ) {
		// Do nothing.
	} else {
		$post_type = array_map( 'trim', explode( ',', $post_type ) );
	}
	$args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => max( -1, (int) $attributes['limit'] ),
		'tax_query'      => [
			[
				'taxonomy' => $taxonomy->name,
				'field'    => 'term_id',
				'terms'    => array_map( function( $term ) {
					return $term->term_id;
				}, $terms ),
			],
		],
	];
	if ( 'rand' === $attributes['orderby'] ) {
		$args['orderby'] = 'rand';
	} else {
		$args['orderby'] = $attributes['orderby'];
		$args['order']   = $attributes['order'];
	}
	$args  = apply_filters( 'taro_taxonomy_blocks_posts_query_args', $args, $attributes, $terms, get_post() );
	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '';
	}
	ob_start();
	taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/posts-list', $taxonomy->name, [
		'query' => $query,
		'terms' => $terms,
	] );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

// Register hooks.
add_action( 'init', 'taro_taxonomy_blocks_assets', 20 );
add_action( 'enqueue_block_editor_assets', 'taro_taxonomy_blocks_enqueue_editor', 1 );
