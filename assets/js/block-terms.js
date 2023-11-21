/*!
 * Terms block.
 *
 */

/* global TaroTermsBlockEditor:false */

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ToggleControl, TextControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;
const { TaxonomySelector, OrderSelector, OrderBySelector } = wp.taro;

registerBlockType( 'taro/terms', {

	title: __( 'Terms', 'taro-taxonomy-blocks' ),

	icon: 'tag',

	category: 'widgets',

	// Preview
	example: {
		taxonomy: 'category',
	},

	keywords: [ 'term' ],

	attributes: TaroTermsBlockEditor.attributes,

	description: __( 'Display terms list in specified taxonomy.', 'taro-taxonomy-blocks' ),

	edit( { attributes, setAttributes } ) {
		return (
			<>
				<InspectorControls>
					<PanelBody defaultOpen={ true } title={ __( 'Taxonomy Setting', 'taro-taxonomy-blocks' ) } >
						<TaxonomySelector value={ attributes.taxonomy } onChange={ ( taxonomy ) => setAttributes( { taxonomy } ) } />
						<ToggleControl checked={ attributes.hide_empty } label={ __( 'Hide Empty', 'taro-taxonomy-blocks' ) } onChange={ ( hideEmpty ) => setAttributes( { hide_empty: hideEmpty } ) } />
						<hr />
						<OrderBySelector value={ attributes.orderby } onChange={ ( orderby ) => setAttributes( { orderby } ) } />
						<TextControl label={ __( 'Custom Field(optional)', 'taro-taxonomy-blocks' ) } value={ attributes.meta } onChange={ ( meta ) => setAttributes( { meta } ) } />
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
						<ServerSideRender block="taro/terms" attributes={ attributes } />
					</div>
				) }
			</>
		);
	},

	save() {
		return null;
	},
} );
