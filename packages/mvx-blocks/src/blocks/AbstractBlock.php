<?php
/**
 * Abstract block class.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * AbstractBlock class.
 */
abstract class AbstractBlock {

	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'mvx';

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * InnerBlocks content.
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Query args.
	 *
	 * @var array
	 */
	protected $query_args = array();

	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Registers the block type with WordPress.
	 */
	public function register_block_type() {
		register_block_type(
			$this->namespace . '/' . $this->block_name,
			array(
				'render_callback' => array( $this, 'render' ),
				'editor_script'   => 'mvx_blocks-scripts-js',
				'editor_style'    => 'mvx_blocks-editor-css',
				'style'           => 'mvx_blocks-style-css',
				'attributes'      => $this->get_attributes(),
			)
		);
	}

	/**
	 * Include and render a dynamic block.
	 *
	 * @param array  $attributes Block attributes. Default empty array.
	 * @param string $content    Block content. Default empty string.
	 * @return string Rendered block type output.
	 */
	abstract public function render( $attributes = array(), $content = '' );

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return array();
	}

	/**
	 * Get the schema for the alignment property.
	 *
	 * @return array Property definition for align.
	 */
	protected function get_schema_align() {
		return array(
			'type' => 'string',
			'enum' => array( 'left', 'center', 'right', 'wide', 'full' ),
		);
	}

	/**
	 * Get the schema for a list of IDs.
	 *
	 * @return array Property definition for a list of numeric ids.
	 */
	protected function get_schema_list_ids() {
		return array(
			'type'    => 'array',
			'items'   => array(
				'type' => 'number',
			),
			'default' => array(),
		);
	}

	/**
	 * Get the schema for a boolean value.
	 *
	 * @param  string $default  The default value.
	 * @return array Property definition.
	 */
	protected function get_schema_boolean( $default = true ) {
		return array(
			'type'    => 'boolean',
			'default' => $default,
		);
	}

	/**
	 * Get the schema for a numeric value.
	 *
	 * @param  string $default  The default value.
	 * @return array Property definition.
	 */
	protected function get_schema_number( $default ) {
		return array(
			'type'    => 'number',
			'default' => $default,
		);
	}

	/**
	 * Get the schema for a string value.
	 *
	 * @param  string $default  The default value.
	 * @return array Property definition.
	 */
	protected function get_schema_string( $default = '' ) {
		return array(
			'type'    => 'string',
			'default' => $default,
		);
	}
}
