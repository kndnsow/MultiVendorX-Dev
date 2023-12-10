<?php
/**
 * Vendor Top Products block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery;

/**
 * VendorProductsSearch class.
 */
class VendorProductsSearch extends AbstractBlock {
	
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendor-search-products';

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	protected $vendor = array();

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return array_merge(
			parent::get_attributes(),
			array(
				'block_title'    		=> $this->get_schema_string(),
				'block_columns'  	=> $this->get_schema_number( 3 ),
				'block_rows'     	=> $this->get_schema_number( 1 ),
				'contentVisibility' => $this->get_schema_content_visibility(),
			)
		);
	}

	/**
	 * Render the Product Categories List block.
	 *
	 * @param array  $attributes Block attributes. Default empty array.
	 * @param string $content    Block content. Default empty string.
	 * @return string Rendered block type output.
	 */
	public function render( $attributes = array(), $content = '' ) {
		global $MVX;
		wp_enqueue_style('frontend_css');
		$output = '';
    	ob_start();
        if ( !mvx_is_store_page() ) return;

        ?>
		<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';
        do_action( 'mvx_widget_before_vendor_product_search_form' );
		$MVX->template->get_template('widget/vendor-product-searchform.php');
    	?>
		</div>
		<?php

    	$output = ob_get_contents();
    	ob_end_clean();
		return $output;
	}

	/**
	 * Get the schema for the contentVisibility attribute
	 *
	 * @return array List of block attributes with type and defaults.
	 */
	protected function get_schema_content_visibility() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'title'  => $this->get_schema_boolean( true ),
				'price'  => $this->get_schema_boolean( true ),
				'rating' => $this->get_schema_boolean( true ),
				'button' => $this->get_schema_boolean( true ),
			),
		);
	}
}
