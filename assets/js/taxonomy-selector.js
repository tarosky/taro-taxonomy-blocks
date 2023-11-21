/*!
 * Description
 */

/*global TaroTaxonomySelector: false */

const { __ } = wp.i18n;
const { SelectControl } = wp.components;

const taxonomyOptions = [
	{
		label: __( 'Please Select', 'taro-taxonomy-blocks' ),
		value: '',
	},
];

TaroTaxonomySelector.taxonomies.forEach( ( taxonomy ) => {
	taxonomyOptions.push( {
		label: taxonomy.label,
		value: taxonomy.name,
	} );
} );

const TaxonomySelector = ( { value, onChange } ) => {
	return (
		<SelectControl label={ __( 'Taxonomy', 'taro-taxonomy-blocks' ) } options={ taxonomyOptions } value={ value } onChange={ ( taxonomy ) => onChange( taxonomy ) } />
	);
};

const OrderSelector = ( { value, onChange } ) => {
	return (
		<SelectControl label={ __( 'Order', 'taro-taxonomy-blocks' ) } onChange={ ( order ) => onChange( order ) }
			value={ value }
			options={ [
				{
					label: __( 'Ascending', 'taro-taxonomy-blocks' ),
					value: 'ASC',
				},
				{
					label: __( 'Descending', 'taro-taxonomy-blocks' ),
					value: 'DESC',
				},
			] } />
	);
};

const OrderBySelector = ( { value, onChange } ) => {
	return (
		<SelectControl label={ __( 'Order By', 'taro-taxonomy-blocks' ) } onChange={ ( orderby ) => onChange( orderby ) }
			value={ value }
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
	);
};

const PostsOrderBySelector = ( { value, onChange } ) => {
	return (
		<SelectControl label={ __( 'Order By', 'taro-taxonomy-blocks' ) } onChange={ ( orderby ) => onChange( orderby ) }
			value={ value }
			options={ [
				{
					label: __( 'Date', 'taro-taxonomy-blocks' ),
					value: 'date',
				},
				{
					label: __( 'Random', 'taro-taxonomy-blocks' ),
					value: 'rand',
				},
				{
					label: __( 'Menu Order', 'taro-taxonomy-blocks' ),
					value: 'menu_order',
				},
			] }
			help={ __( 'To use custom fields for sort, enter the field name above.', 'taro-taxonomy-blocks' ) }
		/>
	);
};

if ( ! wp.taro ) {
	wp.taro = {};
}

wp.taro.TaxonomySelector = TaxonomySelector;
wp.taro.OrderSelector = OrderSelector;
wp.taro.OrderBySelector = OrderBySelector;
wp.taro.PostsOrderBySelector = PostsOrderBySelector;
