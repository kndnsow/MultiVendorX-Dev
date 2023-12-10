<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * VendorOnSellProducts class.
 */
class VendorOnSellProducts extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendor-on-sale-products';

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
		$no_of_product = isset($attributes['no_of_product']) && !empty($attributes['no_of_product']) ? $attributes['no_of_product'] : '';
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
    	
        $query_args = array(
            'posts_per_page' => $no_of_product,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'author'         => $vendor->id,
            'no_found_rows'  => 1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );
        $product_ids_on_sale    = wc_get_product_ids_on_sale();
        $product_ids_on_sale[]  = 0;
        $query_args['post__in'] = $product_ids_on_sale;
        
        $products = new WP_Query( apply_filters( 'woocommerce_products_widget_query_args', $query_args ) );
        ?>
		<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';
        if ( $products && $products->have_posts() ) {
            echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );
            $template_args = array(
                'widget_id'   => 'mvx_vendor_on_sale_products',
            );
            while ( $products->have_posts() ) {
                $products->the_post();
                wc_get_template( 'content-widget-product.php', $template_args );
            }
            echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );
        }
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
