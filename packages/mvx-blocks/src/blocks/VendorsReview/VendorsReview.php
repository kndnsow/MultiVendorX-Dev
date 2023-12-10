<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * VendorsReview class.
 */
class VendorsReview extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendors-review';

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
		global $MVX, $post;
		wp_enqueue_style('frontend_css');
		$vendor_id = isset($attributes['vendor_id']) && !empty($attributes['vendor_id']) ? $attributes['vendor_id'] : '';
		$review_no = isset($attributes['review_no']) && !empty($attributes['review_no']) ? $attributes['review_no'] : 0;
		if ($vendor_id) {
			$vendor = get_mvx_vendor($vendor_id);
		} elseif (mvx_is_store_page()) {
			$vendor_id = mvx_find_shop_page_vendor();
        	$vendor = get_mvx_vendor($vendor_id);
		} elseif (is_singular('product')) {
            $vendor = get_mvx_product_vendors($post->ID);
		}
		$output = '';
		$comments = [];
    	ob_start();
    	
    	?>
    	<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if ( $vendor ) : 
			if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';

		if ($vendor) {
            $reviews_lists = $vendor->get_reviews_and_rating(0); 
            if(isset($reviews_lists) && count($reviews_lists) > 0) {
                foreach($reviews_lists as $comment) {
                    $reviews_number = $review_no;
                    if($review_count >= $reviews_number)
                    break;
                    $rating   = intval( get_comment_meta( $comment->comment_ID, 'vendor_rating', true ) );
                    if ( $rating && get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $rating >= intval(apply_filters('mvx_vendor_rating_widget_set_avg',3)) ){
                        $review_count++;
                        $comments[] = $comment;
                    }
                }
            }
            $MVX->template->get_template('widget/vendor-review.php', array('vendor' => $vendor,'comments' => $comments));
        }
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
				'banner'  		=> $this->get_schema_boolean( true ),
				'logo'  		=> $this->get_schema_boolean( true ),
				'rating' 		=> $this->get_schema_boolean( true ),
				'title' 		=> $this->get_schema_boolean( true ),
				'social_link' 	=> $this->get_schema_boolean( true ),
			),
		);
	}
}
