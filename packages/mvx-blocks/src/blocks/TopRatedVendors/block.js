/**
 * BLOCK: TopRatedVendors
 *
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

/**
 * External dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
import { InspectorControls, PlainText } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {
	PanelBody,
	Placeholder,
	RangeControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

// load MVX Components
import {
	NAMESPACE,
	MVXICONCOLOR,
	DEFAULT_COLUMNS,
	MIN_COLUMNS,
	MAX_COLUMNS,
	DEFAULT_ROWS,
	MIN_ROWS,
	MAX_ROWS,
} from '../../utils/constants';
import MVXIcon from '../../components/icons';

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */

const BLOCK_NAME = 'top-rated-vendors';

registerBlockType( NAMESPACE + '/' + BLOCK_NAME, {
	title: __( 'Top Rated Vendors', 'multivendorx' ),
	icon: {
		src: <MVXIcon icon="top-vendor" />,
		foreground: MVXICONCOLOR,
	},
	category: 'mvx',
	description: __( 'Display marketplace top rated vendors.', 'multivendorx' ),
	keywords: [
		__( 'Top rated vendors', 'multivendorx' ),
		__( 'MVX Vendors', 'multivendorx' ),
		__( 'Rating vendors', 'multivendorx' ),
		__( 'Vendors', 'multivendorx' ),
	],
	attributes: {
		block_title: {
			type: 'string',
			default: '',
		},
		block_columns: {
			type: 'number',
			default: DEFAULT_COLUMNS,
		},
		block_rows: {
			type: 'number',
			default: DEFAULT_ROWS,
		},
		contentVisibility: {
			type: 'object',
			default: {
				banner: true,
				logo: true,
				rating: true,
				title: true,
				social_link: true,
			},
		},
	},
	example: {},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: ( props ) => {
		const { attributes, setAttributes } = props;
		const {
			block_title,
			block_columns,
			block_rows,
			contentVisibility,
			preview,
		} = attributes;

		return (
			<Fragment>
				<InspectorControls key="inspector">
					<PanelBody
						title={ __( 'Layout', 'multivendorx' ) }
						initialOpen={ true }
					>
						{ /* <RangeControl
							label={ __(
								'Columns',
								'multivendorx'
							) }
							value={ block_columns }
							onChange={ ( value ) =>
								setAttributes( { block_columns: value } )
							}
							min={ MIN_COLUMNS }
							max={ MAX_COLUMNS }
						/> */ }
						<RangeControl
							label={ __( 'Rows', 'multivendorx' ) }
							value={ block_rows }
							onChange={ ( value ) =>
								setAttributes( { block_rows: value } )
							}
							min={ MIN_ROWS }
							max={ MAX_ROWS }
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Content', 'multivendorx' ) }
						initialOpen={ false }
					>
						<ToggleControl
							label={ __( 'Vendor Banner', 'multivendorx' ) }
							help={
								contentVisibility.banner
									? __(
											'Vendor banner is visible.',
											'multivendorx'
									  )
									: __(
											'Vendor banner is hidden.',
											'multivendorx'
									  )
							}
							checked={ contentVisibility.banner }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										banner: value,
									},
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Vendor Logo', 'multivendorx' ) }
							help={
								contentVisibility.logo
									? __(
											'Vendor logo is visible.',
											'multivendorx'
									  )
									: __(
											'Vendor logo is hidden.',
											'multivendorx'
									  )
							}
							checked={ contentVisibility.logo }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										logo: value,
									},
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Vendor Rating', 'multivendorx' ) }
							help={
								contentVisibility.rating
									? __(
											'Vendor rating is visible.',
											'multivendorx'
									  )
									: __(
											'Vendor rating is hidden.',
											'multivendorx'
									  )
							}
							checked={ contentVisibility.rating }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										rating: value,
									},
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Vendor Title', 'multivendorx' ) }
							help={
								contentVisibility.banner
									? __(
											'Vendor title is visible.',
											'multivendorx'
									  )
									: __(
											'Vendor title is hidden.',
											'multivendorx'
									  )
							}
							checked={ contentVisibility.title }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										title: value,
									},
								} )
							}
						/>
						<ToggleControl
							label={ __( 'Vendor Social link', 'multivendorx' ) }
							help={
								contentVisibility.social_link
									? __(
											'Vendor social link is visible.',
											'multivendorx'
									  )
									: __(
											'Vendor social link is hidden.',
											'multivendorx'
									  )
							}
							checked={ contentVisibility.social_link }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										social_link: value,
									},
								} )
							}
						/>
					</PanelBody>
				</InspectorControls>
				<Placeholder
					icon={ <MVXIcon icon="top-vendor" size="24" /> }
					label={ __( 'Top Rated Vendors', 'multivendorx' ) }
					className="mvx-block mvx-block-top-rated-vendors"
				>
					{ __(
						'Display top rated vendors in a grid.',
						'multivendorx'
					) }
					<div className="mvx-block__selection mvx-block-top-rated-vendors__selection">
						<TextControl
							placeholder={ __(
								'Add some title',
								'multivendorx'
							) }
							value={ block_title }
							onChange={ ( value ) => {
								setAttributes( { block_title: value } );
							} }
						/>
					</div>
				</Placeholder>
			</Fragment>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: ( props ) => {
		return 'null';
	},
} );
