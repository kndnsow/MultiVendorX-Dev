<?php
/**
 * Vendor Top Products block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery;

/**
 * VendorProductCategories class.
 */
class VendorProductCategories extends AbstractBlock {
	
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendor-products-catagory';

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
				'vendor_id'    		=> $this->get_schema_string(),
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
		$contentVisibility = isset($attributes['contentVisibility']) && !empty($attributes['contentVisibility']) ? $attributes['contentVisibility'] : '';
		$count = $contentVisibility && isset($contentVisibility['refund_policies']) && $contentVisibility['refund_policies'] ? true : false;
        $hierarchical = $contentVisibility && isset($contentVisibility['refund_policies']) && $contentVisibility['refund_policies'] ? true : false;

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

    	?>
    	<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if ( $vendor ) : 
			if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>';

    	if ($vendor) {
	    	$vendor_products = $vendor->get_products_ids();
	        $product_ids = wp_list_pluck($vendor_products, 'ID');
	        $associated_terms = [];
	        foreach ($product_ids as $product_id) {
	            $product_categories = get_the_terms($product_id, 'product_cat');
	            if ($product_categories) {
	                $term_ids = wp_list_pluck($product_categories, 'term_id');
	                if ($term_ids) {
	                    foreach ($term_ids as $term_id) {
	                        $associated_terms[$term_id][] = $product_id;
	                    }
	                }
	            }
	        }
	        $list_args = array('taxonomy' => 'product_cat');
	        $product_cats = get_terms($list_args);
	        if ($product_cats) {
	            echo '<ul class="product-categories">';
	            
	            if ($hierarchical) {
	                $product_cats = get_terms(array('taxonomy' => 'product_cat', 'include' => array_keys($associated_terms), 'hierarchical' =>true, 'hide_empty' => false));
	                echo $this->get_hierarchical_categories($product_cats, $associated_terms, $count);
	            } else {
					foreach ($product_cats as $product_cat) {
						$term_count = isset($associated_terms[$product_cat->term_id]) ? count(array_unique($associated_terms[$product_cat->term_id])) : 0;
						if ($term_count) {
							echo '<li class="cat-item cat-item-' . $product_cat->term_id . '"><a href="?category=' . $product_cat->slug . '">' . $product_cat->name . '</a>';
							if ($count) {
								echo '<span class="count">(' . $term_count . ')</span>';
							}
							echo '</li>';
						}
					}
				}
	            echo '</ul>';
	        }
    	}
    	?>
    	<?php endif; ?>
    	</div>
		<?php
    	$output = ob_get_contents();
    	ob_end_clean();
		return $output;
	}

	function get_hierarchical_categories($terms, $associated_terms, $show_count, $parent_id = 0) {
    	$itemOutput = '';
        $has_parent_list = array();
    	foreach ($terms as $term_key => $term) {
            if (apply_filters('mvx_widget_show_vpc_hierarchical_if_no_parents', true)) {
                $term_count = isset($associated_terms[$term->term_id]) ? count(array_unique($associated_terms[$term->term_id])) : 0;
                if ($term->parent != 0 ) { 
                    $parent_term = get_term( $term->parent, 'product_cat' );
                    if ($parent_term) {
                        if (in_array($parent_term->term_id, $has_parent_list)) continue;
                        $has_parent_list[] = $parent_term->term_id;
                        $term_count = isset($associated_terms[$term->term_id]) ? count(array_unique($associated_terms[$term->term_id])) : 0;
                        $parent_class = ' cat-parent';
                        $parent_count = 0;
                        $itemOutput .= '<li class="cat-item cat-item-' . $parent_term->term_id . $parent_class . '"><a href="?category=' . $parent_term->slug . '">' . $parent_term->name . '</a>';
                        if ($show_count) {
                                $itemOutput .= '<span class="count">(' . $parent_count . ')</span>';
                        }

                        $child_terms = get_terms(array('taxonomy' => 'product_cat', 'include' => array_keys($associated_terms), 'parent' => $parent_term->term_id, 'hierarchical' =>true, 'hide_empty' => false));
                        if ($child_terms) {
                            $parent_class = ' cat-parent';
                            $itemOutput .=  '<ul class="children">';
                            foreach ($child_terms as $c_term) {
                                $term_count = isset($associated_terms[$c_term->term_id]) ? count(array_unique($associated_terms[$c_term->term_id])) : 0;
                                $itemOutput .= '<li class="cat-item cat-item-' . $c_term->term_id . $parent_class . '"><a href="?category=' . $c_term->slug . '">' . $c_term->name . '</a>';
                                if ($show_count) {
                                        $itemOutput .= '<span class="count">(' . $term_count . ')</span>';
                                }
                            }
                            $itemOutput .=  '</ul>';
                        }

                        $itemOutput .= '</li>';
                    }
                } else { 
                    if (in_array($term->term_id, $has_parent_list)) continue;
                    $has_parent_list[] = $term->term_id;
                    $itemOutput .= '<li class="cat-item cat-item-' . $term->term_id . '"><a href="?category=' . $term->slug . '">' . $term->name . '</a>';
                    if ($show_count) {
                            $itemOutput .= '<span class="count">(' . $term_count . ')</span>';
                    }
                    $child_terms = get_terms(array('taxonomy' => 'product_cat', 'include' => array_keys($associated_terms), 'parent' => $term->term_id, 'hierarchical' =>true, 'hide_empty' => false));
                    if ($child_terms) {
                        $parent_class = ' cat-parent';
                        $itemOutput .=  '<ul class="children">';
                        foreach ($child_terms as $c_term) {
                            $term_count = isset($associated_terms[$c_term->term_id]) ? count(array_unique($associated_terms[$c_term->term_id])) : 0;
                            $itemOutput .= '<li class="cat-item cat-item-' . $c_term->term_id . $parent_class . '"><a href="?category=' . $c_term->slug . '">' . $c_term->name . '</a>';
                            if ($show_count) {
                                    $itemOutput .= '<span class="count">(' . $term_count . ')</span>';
                            }
                        }
                        $itemOutput .=  '</ul>';
                    }
                    $itemOutput .= '</li>';
                }

            } else {
                if ($parent_id == $term->parent) { 
                    $output_inner = $this->get_hierarchical_categories($terms, $associated_terms, $show_count, $term->term_id);
                    $term_count = isset($associated_terms[$term->term_id]) ? count(array_unique($associated_terms[$term->term_id])) : 0;  
                    if ($term_count) {
                            if ($output_inner != '') $parent_class = ' cat-parent';
                            else $parent_class = '';
                            $itemOutput .= '<li class="cat-item cat-item-' . $term->term_id . $parent_class . '"><a href="?category=' . $term->slug . '">' . $term->name . '</a>';
                            if ($show_count) {
                                    $itemOutput .= '<span class="count">(' . $term_count . ')</span>';
                            }
                            if ($output_inner != '') {
                                    $itemOutput .=  '<ul class="children">' . $output_inner . '</ul>';
                                    $output_inner = '';
                            }
                            $itemOutput .= '</li>';
                    } else {

                    }
                }
            }
        }
		
        return $itemOutput;
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
