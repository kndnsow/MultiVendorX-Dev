<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * VendorPolicies class.
 */
class VendorPolicies extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendor-policies';

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
				'vendor_id'    		=> $this->get_schema_string(),
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
		global $MVX, $post;
		wp_enqueue_style('frontend_css');
		$MVX->library->load_gmap_api();
		$vendor_id = isset($attributes['vendor_id']) && !empty($attributes['vendor_id']) ? $attributes['vendor_id'] : '';
		$contentVisibility = isset($attributes['contentVisibility']) && !empty($attributes['contentVisibility']) ? $attributes['contentVisibility'] : '';
		if ($vendor_id) {
			$vendor = get_mvx_vendor($vendor_id);
		} elseif (mvx_is_store_page()) {
			$vendor_id = mvx_find_shop_page_vendor();
        	$vendor = get_mvx_vendor($vendor_id);
		} elseif (is_singular('product')) {
            $vendor = get_mvx_product_vendors($post->ID);
		}
		$content = '';
    	ob_start();
    	
        $policies = $this->get_mvx_vendor_policies($vendor);

        ?>
    	<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if ( $vendor ) : 
			if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';

        if (!empty($policies)) {
            $content .= '<div class="mvx-product-policies">';
            if (isset($policies['shipping_policy']) && !empty($policies['shipping_policy']) && $contentVisibility && isset($contentVisibility['shipping_policies']) && $contentVisibility['shipping_policies']) {
                $content .='<div class="mvx-shipping-policies policy">
                    <h2 class="mvx_policies_heading heading">'. esc_html_e('Shipping Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description" >'.$policies['shipping_policy'].'</div>
                </div>';
            } 
            if (isset($policies['refund_policy']) && !empty($policies['refund_policy']) && $contentVisibility && isset($contentVisibility['refund_policies']) && $contentVisibility['refund_policies']){ 
                $content .='<div class="mvx-refund-policies policy">
                    <h2 class="mvx_policies_heading heading heading">'. esc_html_e('Refund Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description">'.$policies['refund_policy'].'</div>
                </div>';
            } 
            if (isset($policies['cancellation_policy']) && !empty($policies['cancellation_policy']) && $contentVisibility && isset($contentVisibility['cancellation_policies']) && $contentVisibility['cancellation_policies']){ 
                $content .='<div class="mvx-cancellation-policies policy">
                    <h2 class="mvx_policies_heading heading">'. esc_html_e('Cancellation / Return / Exchange Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description" >'.$policies['cancellation_policy'].'</div>
                </div>';
            }
            $content .='</div>';
        }
        echo $content; 
  		?>
    	<?php endif; ?>
    	</div>
		<?php
		
    	$output = ob_get_contents();
    	ob_end_clean();
		return $output;
	}

	function get_mvx_vendor_policies($vendor = 0) {
        $policies = array();
        $shipping_policy = get_mvx_vendor_settings('shipping_policy');
        $refund_policy = get_mvx_vendor_settings('refund_policy');
        $cancellation_policy = get_mvx_vendor_settings('cancellation_policy');
        if (apply_filters('mvx_vendor_can_overwrite_policies', true) && $vendor) {
            $shipping_policy = get_user_meta($vendor->id, '_vendor_shipping_policy', true) ? get_user_meta($vendor->id, '_vendor_shipping_policy', true) : $shipping_policy;
            $refund_policy = get_user_meta($vendor->id, '_vendor_refund_policy', true) ? get_user_meta($vendor->id, '_vendor_refund_policy', true) : $refund_policy;
            $cancellation_policy = get_user_meta($vendor->id, '_vendor_cancellation_policy', true) ? get_user_meta($vendor->id, '_vendor_cancellation_policy', true) : $cancellation_policy;
        }
        if (!empty($shipping_policy)) {
            $policies['shipping_policy'] = $shipping_policy;
        }
        if (!empty($refund_policy)) {
            $policies['refund_policy'] = $refund_policy;
        }
        if (!empty($cancellation_policy)) {
            $policies['cancellation_policy'] = $cancellation_policy;
        }
        return $policies;
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
				'shipping_policies'  		=> $this->get_schema_boolean( true ),
				'refund_policies'  			=> $this->get_schema_boolean( true ),
				'cancellation_policies' 	=> $this->get_schema_boolean( true ),
			),
		);
	}
}
