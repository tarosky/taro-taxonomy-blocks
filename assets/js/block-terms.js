/*!
 * Terms block.
 *
 */

/* global TaroTermsBlockEditor:false */

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

const taxonomyOptions = [
	{
		label: __( 'Please Select', 'taro-taxonomy-blocks' ),
		value: '',
	}
];
TaroTermsBlockEditor.taxonomies.forEach( ( taxonomy ) => {
	taxonomyOptions.push( {
		label: taxonomy.label,
		value: taxonomy.name,
	} );
} );

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
						<SelectControl label={ __( 'Taxonomy', 'taro-taxonomy-blocks' ) } options={ taxonomyOptions } value={ attributes.taxonomy } onChange={ taxonomy => setAttributes( { taxonomy } ) } />
						<ToggleControl checked={ attributes.hide_empty } label={ __( 'Hide Empty', 'taro-taxonomy-blocks' ) } onChange={ ( hide_empty ) => setAttributes( { hide_empty } ) } />
						<hr />
						<SelectControl label={ __( 'Order By', 'taro-taxonomy-blocks' ) } onChange={ orderby => setAttributes( { orderby } ) }
							options={ [
								{
									label: __( 'Name', 'taro-taxonomy-blocks' ),
									value: 'name',
								},
								{
									label: __( 'Slug', 'taro-taxonomy-blocks' ),
									value: 'slug',
								},
								{
									label: __( 'Count', 'taro-taxonomy-blocks' ),
									value: 'count',
								},
							] }
							help={ __( 'To use custom fields for sort, enter the field name above.', 'taro-taxonomy-blocks' ) }
						/>
						<TextControl label={ __( 'Custom Field(optional)', 'taro-taxonomy-blocks' ) } value={ attributes.meta } onChange={ meta => setAttributes( { meta } ) } />
						<SelectControl label={ __( 'Order', 'taro-taxonomy-blocks' ) } onChange={ order => setAttributes( { order } ) }
							options={ [
								{
									label: __( 'Ascending', 'taro-taxonomy-blocks' ),
									value: 'ASC',
								},
								{
									label: __( 'Descending', 'taro-taxonomy-blocks' ),
									value: 'DESC',
								},
							] }/>
					</PanelBody>
				</InspectorControls>

					{ ( ! attributes.taxonomy ) ? (
						<div style={ { margin: "40px 0" } }>
							<p>{ __( 'No taxonomy set. Please choose one.', 'taro-taxonomy-' ) }</p>
							<SelectControl label={ __( 'Taxonomy', 'taro-taxonomy-blocks' ) } options={ taxonomyOptions } value={ attributes.taxonomy } onChange={ taxonomy => setAttributes( { taxonomy } ) } />
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
