/*!
 * Post terms block.
 *
 * @handle taro-taxonomy-post-blocks-editor
 * @deps wp-i18n, wp-data, wp-components, wp-blocks, wp-block-editor, wp-server-side-render, wp-compose, wp-data, taro-taxonomy-selector
 */

/* global TaroPostTermsQueryBlockEditor:false */

const { registerBlockType } = wp.blocks;
const { __, sprintf } = wp.i18n;
const { select } = wp.data;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;
const { TaxonomySelector, OrderSelector, PostsOrderBySelector } = wp.taro;

registerBlockType( 'taro/post-terms-query', {

	title: __( 'Post Terms Query', 'taro-taxonomy-blocks' ),

	icon: 'tag',

	category: 'widgets',

	// Preview
	example: {
		taxonomy: 'category',
	},

	keywords: [ 'term' ],

	attributes: TaroPostTermsQueryBlockEditor.attributes,

	description: __( 'Display posts with same terms of this post in specified taxonomy.', 'taro-taxonomy-blocks' ),

	edit( { attributes, setAttributes } ) {
		// translators: %s is post type.
		const postTypePlaceHolder = sprintf( __( 'Default: %s', 'taro-taxonomy-blocks' ), select( 'core/editor' ).getCurrentPostType() );
		return (
			<>
				<InspectorControls>
					<PanelBody defaultOpen={ true } title={ __( 'Taxonomy Setting', 'taro-taxonomy-blocks' ) } >
						<TextControl label={ __( 'Post Type', 'taro-taxonomy-blocks' ) } value={ attributes.post_type }
							placeholder={ postTypePlaceHolder }
							onChange={ ( postType ) => setAttributes( { post_type: postType } ) }
							help={ __( 'Enter post types in csv format. "any" is also available. If empty, current post type will be used.', 'taro-taxonomy-blocks' ) }
						/>
						<TaxonomySelector value={ attributes.taxonomy } onChange={ ( taxonomy ) => setAttributes( { taxonomy } ) } />
						<TextControl value={ attributes.terms } label={ __( 'Term Slugs', 'label' ) }
							help={ __( 'Enter term slugs in CSV format. Default value is post\'s terms', 'taro-taxonomy-blocks' ) }
							placeholder={ __( 'Post\'s terms.', 'taro-taxonomy-blocks' ) }
							onChange={ ( terms ) => setAttributes( { terms } ) } />
						<TextControl label={ __( 'Number of Posts', 'taro-taxonomy-blocks' ) } type="number" min={ -1 }
							value={ attributes.limit }
							placeholder={ 10 }
							onChange={ ( limit ) => setAttributes( { limit: parseInt( limit ) } ) }
						/>
						<PostsOrderBySelector valu={ attributes.orderby } onChange={ ( orderby ) => setAttributes( { orderby } ) } />
						<OrderSelector value={ attributes.order } onChange={ ( order ) => setAttributes( { order } ) } />
					</PanelBody>
				</InspectorControls>

				{ ( ! attributes.taxonomy ) ? (
					<div style={ { margin: '40px 0' } }>
						<p>{ __( 'No taxonomy set. Please choose one.', 'taro-taxonomy-' ) }</p>
						<TaxonomySelector value={ attributes.taxonomy } onChange={ ( taxonomy ) => setAttributes( { taxonomy } ) } />
					</div>
				) : (
					<div className="taro-taxonomy-blocks-editor">
						<ServerSideRender block="taro/post-terms-query" attributes={ attributes } />
					</div>
				) }
			</>
		);
	},

	save() {
		return null;
	},
} );
