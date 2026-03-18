<?php
/**
 * Test block option schemas.
 *
 * @package taro-taxonomy-blocks
 */

class TestOptions extends WP_UnitTestCase {

	/**
	 * Test default (terms) option has expected keys.
	 */
	public function test_terms_option_keys() {
		$options = taro_taxonomy_terms_blocks_option();
		$expected = [ 'taxonomy', 'className', 'orderby', 'order', 'meta', 'hide_empty' ];
		$this->assertSame( $expected, array_keys( $options ) );
	}

	/**
	 * Test terms option is same as explicit 'terms' target.
	 */
	public function test_terms_option_same_as_explicit() {
		$this->assertSame(
			taro_taxonomy_terms_blocks_option(),
			taro_taxonomy_terms_blocks_option( 'terms' )
		);
	}

	/**
	 * Test post_terms option returns basic keys only.
	 */
	public function test_post_terms_option_keys() {
		$options = taro_taxonomy_terms_blocks_option( 'post_terms' );
		$expected = [ 'taxonomy', 'className' ];
		$this->assertSame( $expected, array_keys( $options ) );
	}

	/**
	 * Test posts option has extended keys.
	 */
	public function test_posts_option_keys() {
		$options = taro_taxonomy_terms_blocks_option( 'posts' );
		$expected = [ 'taxonomy', 'className', 'post_type', 'terms', 'limit', 'orderby', 'order' ];
		$this->assertSame( $expected, array_keys( $options ) );
	}

	/**
	 * Test terms default values.
	 */
	public function test_terms_default_values() {
		$options = taro_taxonomy_terms_blocks_option();
		$this->assertSame( '', $options['taxonomy']['default'] );
		$this->assertSame( 'name', $options['orderby']['default'] );
		$this->assertSame( 'ASC', $options['order']['default'] );
		$this->assertTrue( $options['hide_empty']['default'] );
	}

	/**
	 * Test posts default values.
	 */
	public function test_posts_default_values() {
		$options = taro_taxonomy_terms_blocks_option( 'posts' );
		$this->assertSame( '', $options['post_type']['default'] );
		$this->assertSame( '', $options['terms']['default'] );
		$this->assertSame( 'date', $options['orderby']['default'] );
		$this->assertSame( 'DESC', $options['order']['default'] );
	}
}
