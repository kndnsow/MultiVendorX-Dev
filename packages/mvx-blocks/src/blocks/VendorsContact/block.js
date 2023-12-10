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
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {
	PanelBody,
	Placeholder,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
	TextareaControl,
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

registerBlockType( NAMESPACE + '/vendors-quick-info', {
	title: __( 'MVX: Contact Vendor', 'multivendorx' ),
	icon: {
		src: <MVXIcon icon="contact-vendor" />,
		foreground: MVXICONCOLOR,
	},
	category: 'mvx',
	description: __(
		"Adds a contact form on vendor's shop page so that customers can contact vendor directly( Admin will also get a copy of the same )",
		'multivendorx'
	),
	keywords: [
		__( 'Top Products', 'multivendorx' ),
		__( 'MVX Vendor Products', 'multivendorx' ),
		__( 'Products', 'multivendorx' ),
		__( 'Vendor', 'multivendorx' ),
	],
	attributes: {
		block_title: {
			type: 'string',
			default: '',
		},
		block_description: {
			type: 'string',
			default: '',
		},
		block_submit_title: {
			type: 'string',
			default: '',
		},
		recapta_id: {
			type: 'string',
			default: '',
		},
		recapta_script_v: {
			type: 'string',
			default: '',
		},
		site_key_v: {
			type: 'string',
			default: '',
		},
		secret_key_v: {
			type: 'string',
			default: '',
		},
		vendor_id: {
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
				form: true,
				button: true,
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
			block_description,
			block_submit_title,
			recapta_id,
			recapta_script_v,
			site_key_v,
			secret_key_v,
			vendor_id,
			block_columns,
			block_rows,
			contentVisibility,
		} = attributes;

		const bindVendorsOptionData = [
			{ value: '', label: 'Select a type...' },
		];
		let vendors = mvx_blocks_scripts_data_params.allVendors;
		vendors.map( function ( vendor_data ) {
			bindVendorsOptionData.push( {
				value: vendor_data.vendor_id,
				label: vendor_data.vendor_title,
			} );
		} );

		const captaOptionData = [ { value: '', label: 'Select a type...' } ];
		let capta = mvx_blocks_scripts_data_params.recapta;
		capta.map( function ( vendor_data ) {
			captaOptionData.push( {
				value: vendor_data.key,
				label: vendor_data.title,
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
							label={ __( 'Product Columns', 'multivendorx' ) }
							value={ block_columns }
							onChange={ ( value ) =>
								setAttributes( { block_columns: value } )
							}
							min={ MIN_COLUMNS }
							max={ MAX_COLUMNS }
						/>
						<RangeControl
							label={ __( 'Product Rows', 'multivendorx' ) }
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
							label={ __( 'Hide from guests: ', 'woocommerce' ) }
							help={
								contentVisibility.form
									? __( 'Form is visible.', 'woocommerce' )
									: __( 'Form is hidden.', 'woocommerce' )
							}
							checked={ contentVisibility.form }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										form: value,
									},
								} )
							}
						/>
						<ToggleControl
							label={ __(
								'Enable Google Recaptcha',
								'woocommerce'
							) }
							help={
								contentVisibility.button
									? __(
											'Google recapta is visible.',
											'woocommerce'
									  )
									: __(
											'Google recapta is hidden.',
											'woocommerce'
									  )
							}
							checked={ contentVisibility.button }
							onChange={ ( value ) =>
								setAttributes( {
									contentVisibility: {
										...contentVisibility,
										button: value,
									},
								} )
							}
						/>
					</PanelBody>
				</InspectorControls>
				<Placeholder
					icon={ <MVXIcon icon="contact-vendor" size="24" /> }
					label={ __( 'Contact Vendor', 'multivendorx' ) }
					className="mvx-block mvx-block-contact-vendor"
				>
					{ __( 'Title.', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextControl
							placeholder={ __( 'Title:', 'multivendorx' ) }
							value={ block_title }
							onChange={ ( value ) => {
								setAttributes( { block_title: value } );
							} }
						/>
					</div>
					{ __( 'Description', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextControl
							placeholder={ __( 'Description:', 'multivendorx' ) }
							value={ block_description }
							onChange={ ( value ) => {
								setAttributes( { block_description: value } );
							} }
						/>
					</div>
					{ __( 'Submit button text', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextControl
							placeholder={ __(
								'Submit Button Label Text:',
								'multivendorx'
							) }
							value={ block_submit_title }
							onChange={ ( value ) => {
								setAttributes( { block_submit_title: value } );
							} }
						/>
					</div>
					{ __( 'Recapta Type', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<SelectControl
							value={ recapta_id }
							onChange={ ( value ) => {
								setAttributes( { recapta_id: value } );
							} }
							options={ captaOptionData }
						/>
					</div>
					{ __( 'Recapta Script', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextareaControl
							placeholder={ __(
								'Recaptcha Script:',
								'multivendorx'
							) }
							value={ recapta_script_v }
							onChange={ ( value ) => {
								setAttributes( { recapta_script_v: value } );
							} }
						/>
					</div>
					{ __( 'Site Key', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextControl
							placeholder={ __( 'Site key:', 'multivendorx' ) }
							value={ site_key_v }
							onChange={ ( value ) => {
								setAttributes( { site_key_v: value } );
							} }
						/>
					</div>
					{ __( 'Secret Key', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<TextControl
							placeholder={ __( 'Secret key:', 'multivendorx' ) }
							value={ secret_key_v }
							onChange={ ( value ) => {
								setAttributes( { secret_key_v: value } );
							} }
						/>
					</div>
					{ __( 'Select Vendor', 'multivendorx' ) }
					<div className="mvx-block__selection mvx-block-contact-vendor__selection">
						<SelectControl
							value={ vendor_id }
							onChange={ ( value ) => {
								setAttributes( { vendor_id: value } );
							} }
							options={ bindVendorsOptionData }
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
