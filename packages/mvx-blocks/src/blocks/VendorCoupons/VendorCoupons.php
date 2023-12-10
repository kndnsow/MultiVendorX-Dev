<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * VendorsQuickInfo class.
 */
class VendorCoupons extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'coupon-vendors';

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
		$vendor_id = isset($attributes['vendor_id']) && !empty($attributes['vendor_id']) ? $attributes['vendor_id'] : '';
		if ($vendor_id) {
			$vendor = get_mvx_vendor($vendor_id);
		} elseif (mvx_is_store_page()) {
			$vendor_id = mvx_find_shop_page_vendor();
        	$vendor = get_mvx_vendor($vendor_id);
		} elseif (is_singular('product')) {
            $vendor = get_mvx_product_vendors($post->ID);
		}
		$output = '';
    	ob_start();
    	
    	$coupon_args = apply_filters( 'mvx_get_vendor_coupon_widget_list_query_args', array(
	        'posts_per_page' => -1,
	        'offset' => 0,
	        'orderby' => 'date',
	        'order' => 'DESC',
	        'post_type' => 'shop_coupon',
	        'author' => $vendor->id,
	        'post_status' => array('publish', 'pending', 'draft', 'trash'),
	        'suppress_filters' => true
	    ), $vendor );
        $vendor_total_coupons = get_posts($coupon_args);

        if( empty( $vendor_total_coupons ) ) return;

        ?>
		<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';

        $content = '<div class="mvx_store_coupons">';
		foreach( $vendor_total_coupons as $vendor_coupon ) {
			$coupon = new WC_Coupon( $vendor_coupon->ID );
			
			if ( $coupon->get_date_expires() && ( current_time( 'timestamp', true ) > $coupon->get_date_expires()->getTimestamp() ) ) continue;
			
			$content .= '<span class="mvx-store-coupon-single tips text_tip" title="' . esc_html( wc_get_coupon_type( $coupon->get_discount_type() ) ) . ': ' . esc_html( wc_format_localized_price( $coupon->get_amount() ) ) . ($coupon->get_date_expires() ? ' ' . __( 'Expiry Date: ', 'multivendorx' ) . $coupon->get_date_expires()->date_i18n( 'F j, Y' ) : '' ) . ' ' . $vendor_coupon->post_excerpt . '">' . $vendor_coupon->post_title . '</span>';
		}
		$content .= '</div>';
		echo $content;

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
			),
		);
	}
}
