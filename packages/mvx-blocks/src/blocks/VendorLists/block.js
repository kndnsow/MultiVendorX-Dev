/**
 * BLOCK: VendorsQuickInfo
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
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {
	PanelBody,
	Placeholder,
	RangeControl,
	SelectControl,
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

const BLOCK_NAME = 'list-vendors';

registerBlockType( NAMESPACE + '/' + BLOCK_NAME, {
	title: __( 'MVX: Vendors List', 'multivendorx' ),
	icon: {
		src: <MVXIcon icon="vendor-list" />,
		foreground: MVXICONCOLOR,
	},
	category: 'mvx',
	description: __(
		'Display list of registered vendors on your site.',
		'multivendorx'
	),
	keywords: [
		__( 'Vendor list', 'multivendorx' ),
		__( 'MVX Vendors', 'multivendorx' ),
		__( 'Vendors', 'multivendorx' ),
	],
	attributes: {
		block_title: {
			type: 'string',
			default: '',
		},
		block_rows: {
			type: 'number',
			default: DEFAULT_ROWS,
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
		const { block_title, block_rows, preview } = attributes;

		const bindVendorsOptionData = [
			{ value: '', label: 'Select a Vendor...' },
		];
		let vendors = mvx_blocks_scripts_data_params.allVendors;
		vendors.map( function ( vendor_data ) {
			bindVendorsOptionData.push( {
				value: vendor_data.vendor_id,
				label: vendor_data.vendor_title,
			} );
		} );

		return (
			<Fragment>
				<InspectorControls key="inspector">
					<PanelBody
						title={ __( 'Layout', 'multivendorx' ) }
						initialOpen={ true }
					>
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
				</InspectorControls>
				<Placeholder
					icon={ <MVXIcon icon="vendor-list" size="24" /> }
					label={ __( 'Vendor List', 'multivendorx' ) }
					className="mvx-block mvx-block-list-vendors"
				>
					{ __( 'Title', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-list-vendors__selection">
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
