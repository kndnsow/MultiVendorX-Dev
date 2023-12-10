<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * VendorsContact class.
 */
class VendorsContact extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendors-quick-info';

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return array_merge(
			parent::get_attributes(),
			array(
				'block_title'    	=> $this->get_schema_string(),
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
		$block_description = isset($attributes['block_description']) && !empty($attributes['block_description']) ? $attributes['block_description'] : '';
		$block_submit_title = isset($attributes['block_submit_title']) && !empty($attributes['block_submit_title']) ? $attributes['block_submit_title'] : '';
		$recapta_id = isset($attributes['recapta_id']) && !empty($attributes['recapta_id']) ? $attributes['recapta_id'] : '';
		$recapta_script_v = isset($attributes['recapta_script_v']) && !empty($attributes['recapta_script_v']) ? $attributes['recapta_script_v'] : '';
		$site_key_v = isset($attributes['site_key_v']) && !empty($attributes['site_key_v']) ? $attributes['site_key_v'] : '';
		$secret_key_v = isset($attributes['secret_key_v']) && !empty($attributes['secret_key_v']) ? $attributes['secret_key_v'] : '';


		$contentVisibility = isset($attributes['contentVisibility']) && !empty($attributes['contentVisibility']) ? $attributes['contentVisibility'] : '';

		$instance = [];
		$instance['hide_from_guests'] = $contentVisibility && isset($contentVisibility['form']) && $contentVisibility['form'] ? true : false;
		$instance['submit_label'] = isset($attributes['block_submit_title']) && !empty($attributes['block_submit_title']) ? $attributes['block_submit_title'] : __( 'Submit', 'multivendorx' );
		$instance['enable_google_recaptcha'] = $contentVisibility && isset($contentVisibility['button']) && $contentVisibility['button'] ? true : false;
		$instance['google_recaptcha_type'] = $recapta_id;


		if ($vendor_id) {
			$vendor = get_mvx_vendor($vendor_id);
		} elseif (mvx_is_store_page()) {
			$vendor_id = mvx_find_shop_page_vendor();
        	$vendor = get_mvx_vendor($vendor_id);
		} elseif (is_singular('product')) {
            $vendor = get_mvx_product_vendors($post->ID);
		}
		$args = array(
            'instance' => $instance,
            'vendor' => isset($vendor) ? $vendor : false,
            'current_user' => wp_get_current_user(),
            'widget' => $this,
            'post'  =>  is_singular('product') ? $post : '',
            'recaptcha_v2_scripts'	=>	$recapta_script_v,
            'recaptcha_v3_sitekey'	=>	$site_key_v,
            'recaptcha_v3_secretkey'	=>	$secret_key_v
        );
		$output = '';
    	ob_start();

    	?>
    	<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if ( $vendor ) : 
			if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';
    	$MVX->template->get_template('widget/quick-info.php', $args);
    	?>
    	<?php endif; ?>
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
				'form'  		=> $this->get_schema_boolean( true ),
				'button'  		=> $this->get_schema_boolean( true ),
			),
		);
	}
}
