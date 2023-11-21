/*!
 * Post terms block.
 *
 * @handle taro-taxonomy-post-blocks-editor
 * @deps wp-i18n, wp-components, wp-blocks, wp-block-editor, wp-server-side-render, wp-compose, wp-data, taro-taxonomy-selector
 */

/* global TaroPostTermsBlockEditor:false */

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody } = wp.components;
const { serverSideRender: ServerSideRender } = wp;
const { TaxonomySelector } = wp.taro;

registerBlockType( 'taro/post-terms', {

	title: __( 'Post Terms', 'taro-taxonomy-blocks' ),

	icon: 'tag',

	category: 'widgets',

	// Preview
	example: {
		taxonomy: 'category',
	},

	keywords: [ 'term' ],

	attributes: TaroPostTermsBlockEditor.attributes,

	description: __( 'Display the list of terms assigned to this post in specified taxonomy.', 'taro-taxonomy-blocks' ),

	edit( { attributes, setAttributes } ) {
		return (
			<>
				<InspectorControls>
					<PanelBody defaultOpen={ true } title={ __( 'Taxonomy Setting', 'taro-taxonomy-blocks' ) } >
						<TaxonomySelector value={ attributes.taxonomy } onChange={ ( taxonomy ) => setAttributes( { taxonomy } ) } />
					</PanelBody>
				</InspectorControls>

				{ ( ! attributes.taxonomy ) ? (
					<div style={ { margin: '40px 0' } }>
						<p>{ __( 'No taxonomy set. Please choose one.', 'taro-taxonomy-' ) }</p>
						<TaxonomySelector value={ attributes.taxonomy } onChange={ ( taxonomy ) => setAttributes( { taxonomy } ) } />
					</div>
				) : (
					<div className="taro-taxonomy-blocks-editor">
						<ServerSideRender block="taro/post-terms" attributes={ attributes } />
					</div>
				) }
			</>
		);
	},

	save() {
		return null;
	},
} );
