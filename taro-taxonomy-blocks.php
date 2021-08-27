<?php
/**
Plugin Name: Taro iframe Block
Plugin URI: https://wordpress.org/plugins/taro-iframe-block/
Description: Add iframe block for block editor.
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
	wp_register_script( 'taro-terms-block-editor', $base . '/js/block-terms.js', [ 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ], $version, true );
	wp_register_script( 'taro-post-terms-block-editor', $base . '/js/block-posts-terms.js', [ 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ], $version, true );
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
		'attributes'      => taro_taxonomy_terms_blocks_option( true ),
		'render_callback' => 'taro_taxonomy_blocks_callback_post_terms',
		'editor_script'   => 'taro-post-terms-block-editor',
		'editor_style'    => 'taro-terms-block-editor',
		'style'           => 'taro-terms-block',
	] );
}

/**
 * Enqueue assets for editor.
 */
function taro_taxonomy_blocks_enqueue_editor() {
	wp_set_script_translations( 'taro-terms-block-editor', 'taro-taxonomy-blocks' );
	wp_localize_script( 'taro-terms-block-editor', 'TaroTermsBlockEditor', [
		'attributes' => taro_taxonomy_terms_blocks_option(),
		'taxonomies' => array_values( get_taxonomies( [
			'public' => true,
		], OBJECT ) ),
	] );
	wp_set_script_translations( 'taro-post-terms-block-editor', 'taro-taxonomy-blocks' );
	wp_localize_script( 'taro-post-terms-block-editor', 'TaroPostTermsBlockEditor', [
		'attributes' => taro_taxonomy_terms_blocks_option( true ),
	] );
}

/**
 * Option for iframe block.
 *
 * @return array[]
 */
function taro_taxonomy_terms_blocks_option( $is_post = false ) {
	$args = [
		'taxonomy' => [
			'type'    => 'string',
			'default' => '',
		],
		'ordeby'   => [
			'type'    => 'string',
			'default' => 'name',
		],
		'order'    => [
			'type'    => 'string',
			'default' => 'ASC',
		],
		'meta'     => [
			'type'    => 'string',
			'default' => '',
		],
	];
	return $is_post ? $args : array_merge( $args, [
		'hide_empty' => [
			'type'    => 'bool',
			'default' => true,
		],
	] );
}

/**
 * Parse attributes.
 *
 * @param array $attributes REST attributes.
 * @param bool  $is_post    Is post, true.
 * @return array
 */
function taro_taxonomy_parse_args( $attributes, $is_post = false ) {
	$default_args = [];
	foreach ( taro_taxonomy_terms_blocks_option( $is_post ) as $key => $setting ) {
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
	$files = [ $name .'.php' ];
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
	$term_args = [
		'taxonomy'   => $attributes['taxonomy'],
		'hide_empty' => $attributes['hide_empty'],
	];
	if ( $attributes['meta'] ) {
		$term_args['meta_key'] = $attributes['meta'];
		$term_args['ordeby']  = 'meta_value';
	} else {
		$term_args['ordeby'] = $attributes['ordeby'];
	}
	$term_args['order'] = $attributes['order'];
	$terms = get_terms( $term_args );
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
	return '投稿のタグ';
}

// Register hooks.
add_action( 'init', 'taro_taxonomy_blocks_assets', 20 );
add_action( 'enqueue_block_editor_assets', 'taro_taxonomy_blocks_enqueue_editor', 1 );
