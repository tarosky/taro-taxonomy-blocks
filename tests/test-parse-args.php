<?php
/**
 * Test attribute parsing.
 *
 * @package taro-taxonomy-blocks
 */

class TestParseArgs extends WP_UnitTestCase {

	/**
	 * Test parse_args fills defaults for empty input.
	 */
	public function test_fills_defaults() {
		$result = taro_taxonomy_parse_args( [] );
		$this->assertSame( '', $result['taxonomy'] );
		$this->assertSame( '', $result['className'] );
		$this->assertSame( 'name', $result['orderby'] );
		$this->assertSame( 'ASC', $result['order'] );
		$this->assertTrue( $result['hide_empty'] );
	}

	/**
	 * Test parse_args preserves custom values.
	 */
	public function test_preserves_custom_values() {
		$result = taro_taxonomy_parse_args( [
			'taxonomy' => 'category',
			'orderby'  => 'count',
		] );
		$this->assertSame( 'category', $result['taxonomy'] );
		$this->assertSame( 'count', $result['orderby'] );
		// Defaults preserved for unset keys.
		$this->assertSame( 'ASC', $result['order'] );
	}

	/**
	 * Test parse_args for posts target.
	 */
	public function test_posts_target() {
		$result = taro_taxonomy_parse_args( [], 'posts' );
		$this->assertArrayHasKey( 'post_type', $result );
		$this->assertArrayHasKey( 'terms', $result );
		$this->assertArrayHasKey( 'limit', $result );
		$this->assertSame( 'date', $result['orderby'] );
		$this->assertSame( 'DESC', $result['order'] );
	}

	/**
	 * Test parse_args for post_terms target.
	 */
	public function test_post_terms_target() {
		$result = taro_taxonomy_parse_args( [], 'post_terms' );
		$this->assertSame( '', $result['taxonomy'] );
		$this->assertSame( '', $result['className'] );
		// Should NOT have orderby/order/meta/hide_empty.
		$this->assertArrayNotHasKey( 'orderby', $result );
	}
}
