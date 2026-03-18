<?php
/**
 * Test render callbacks.
 *
 * @package taro-taxonomy-blocks
 */

class TestRender extends WP_UnitTestCase {

	/**
	 * Test callback_terms returns empty for invalid taxonomy.
	 */
	public function test_callback_terms_empty_for_invalid_taxonomy() {
		$result = taro_taxonomy_blocks_callback_terms( [
			'taxonomy' => 'nonexistent_taxonomy',
		] );
		$this->assertSame( '', $result );
	}

	/**
	 * Test callback_terms returns content for category with terms.
	 */
	public function test_callback_terms_renders_categories() {
		// Create a category.
		$term_id = self::factory()->term->create( [
			'taxonomy' => 'category',
			'name'     => 'Test Category',
		] );
		// Create a post with that category so hide_empty works.
		$post_id = self::factory()->post->create();
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );

		$result = taro_taxonomy_blocks_callback_terms( [
			'taxonomy'   => 'category',
			'hide_empty' => true,
		] );
		$this->assertNotEmpty( $result );
		$this->assertStringContainsString( 'Test Category', $result );
	}

	/**
	 * Test callback_terms returns empty for empty taxonomy.
	 */
	public function test_callback_terms_empty_for_empty_taxonomy() {
		// Register a custom taxonomy with no terms.
		register_taxonomy( 'test_empty_tax', 'post' );
		$result = taro_taxonomy_blocks_callback_terms( [
			'taxonomy'   => 'test_empty_tax',
			'hide_empty' => true,
		] );
		$this->assertSame( '', $result );
		unregister_taxonomy( 'test_empty_tax' );
	}

	/**
	 * Test callback_post_terms returns empty without terms.
	 */
	public function test_callback_post_terms_returns_empty() {
		$post_id = self::factory()->post->create();
		// Register a custom taxonomy with no assigned terms.
		register_taxonomy( 'test_tax_pt', 'post' );
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$result = taro_taxonomy_blocks_callback_post_terms( [
			'taxonomy' => 'test_tax_pt',
		] );
		$this->assertSame( '', $result );

		wp_reset_postdata();
		unregister_taxonomy( 'test_tax_pt' );
	}

	/**
	 * Test callback_post_terms renders terms for current post.
	 */
	public function test_callback_post_terms_renders() {
		$post_id = self::factory()->post->create();
		$term_id = self::factory()->term->create( [
			'taxonomy' => 'category',
			'name'     => 'Rendered Term',
		] );
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$result = taro_taxonomy_blocks_callback_post_terms( [
			'taxonomy' => 'category',
		] );
		$this->assertNotEmpty( $result );
		$this->assertStringContainsString( 'Rendered Term', $result );

		wp_reset_postdata();
	}

	/**
	 * Test callback_post_terms_query returns empty without terms.
	 */
	public function test_callback_post_terms_query_returns_empty_without_terms() {
		$post_id = self::factory()->post->create();
		register_taxonomy( 'test_tax_ptq', 'post' );
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$result = taro_taxonomy_blocks_callback_post_terms_query( [
			'taxonomy' => 'test_tax_ptq',
		] );
		$this->assertSame( '', $result );

		wp_reset_postdata();
		unregister_taxonomy( 'test_tax_ptq' );
	}

	/**
	 * Test callback_post_terms_query with specific term slugs.
	 */
	public function test_callback_post_terms_query_with_slugs() {
		// Create term and post.
		$term_id = self::factory()->term->create( [
			'taxonomy' => 'category',
			'name'     => 'Query Term',
			'slug'     => 'query-term',
		] );
		$post_id = self::factory()->post->create( [ 'post_status' => 'publish' ] );
		wp_set_post_terms( $post_id, [ $term_id ], 'category' );

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$result = taro_taxonomy_blocks_callback_post_terms_query( [
			'taxonomy' => 'category',
			'terms'    => 'query-term',
		] );
		$this->assertNotEmpty( $result );

		wp_reset_postdata();
	}

	/**
	 * Test template part loading uses plugin directory as fallback.
	 */
	public function test_template_part_loading() {
		ob_start();
		taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list', '', [
			'terms'     => [],
			'className' => '',
		] );
		$output = ob_get_clean();
		$this->assertStringContainsString( 'taro-taxonomy-list', $output );
	}

	/**
	 * Test template filter can override template path.
	 */
	public function test_template_filter() {
		// Create a real temp file to avoid require fatal error.
		$tmp = tempnam( sys_get_temp_dir(), 'ttb_test_' );
		file_put_contents( $tmp, '<?php echo "custom-template-loaded";' );
		$callback = function () use ( $tmp ) {
			return $tmp;
		};
		add_filter( 'taro_taxonomy_blocks_template', $callback );
		ob_start();
		taro_taxonomy_blocks_get_template_part( 'template-parts/taxonomy-blocks/term-list', '' );
		$output = ob_get_clean();
		remove_filter( 'taro_taxonomy_blocks_template', $callback );
		unlink( $tmp );
		$this->assertStringContainsString( 'custom-template-loaded', $output );
	}
}
