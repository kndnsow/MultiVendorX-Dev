<?php
/**
 * Vendor Top Products block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery;

/**
 * VendorTopProducts class.
 */
class VendorTopProducts extends AbstractBlock {
	
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'vendor-top-products';

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
		global $MVX;
		$vendor_id = ( $attributes['vendor_id'] ) ? absint( $attributes['vendor_id'] ) : false;
		if( !$vendor_id ) return '';
		$this->vendor     = get_mvx_vendor( $vendor_id );
		$this->attributes = $attributes;
		$this->content    = $content;
		$this->query_args = $this->parse_query_args();
		$products         = $this->get_products();
		
		if ( ! $products ) {
			return '';
		}

		$classes = $this->get_container_classes();
		$output  = implode( '', array_map( array( $this, 'render_product' ), $products ) );

		return sprintf( '<div class="%s"><ul class="wc-block-grid__products">%s</ul></div>', esc_attr( $classes ), $output );
	}

	/**
	 * Parse query args.
	 *
	 * @return array
	 */
	protected function parse_query_args() {
		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
			'orderby'             => 'rating',
			'order'               => '',
			'meta_query'          => WC()->query->get_meta_query(), // phpcs:ignore WordPress.DB.SlowDBQuery
			'tax_query'           => array(), // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page'      => $this->get_products_limit(),
		);

		$this->set_vendor_query_args( $query_args );
		$this->set_visibility_query_args( $query_args );

		return $query_args;
	}

	/**
	 * Set Vendor query args.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_vendor_query_args( &$query_args ) {
		global $MVX;
		if ( ! empty( $this->attributes['vendor_id'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => $MVX->taxonomy->taxonomy_name,
				'field' => 'term_id',
				'terms' => absint($this->vendor->term_id)
			);
		}
	}

	/**
	 * Set visibility query args.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_visibility_query_args( &$query_args ) {
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = array( $product_visibility_terms['exclude-from-catalog'] );

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
		}

		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => $product_visibility_not_in,
			'operator' => 'NOT IN',
		);
	}

	/**
	 * Works out the item limit based on rows and columns, or returns default.
	 *
	 * @return int
	 */
	protected function get_products_limit() {
		if ( isset( $this->attributes['block_rows'], $this->attributes['block_columns'] ) && ! empty( $this->attributes['block_rows'] ) ) {
			$this->attributes['limit'] = intval( $this->attributes['block_columns'] ) * intval( $this->attributes['block_rows'] );
		}
		return intval( $this->attributes['limit'] );
	}

	/**
	 * Run the query and return an array of product IDs
	 *
	 * @return array List of product IDs
	 */
	protected function get_products() {
		$is_cacheable      = (bool) apply_filters( 'woocommerce_blocks_product_grid_is_cacheable', true, $this->query_args );
		$transient_version = \WC_Cache_Helper::get_transient_version( 'product_query' );

		$query   = new BlocksWpQuery( $this->query_args );
		$results = wp_parse_id_list( $is_cacheable ? $query->get_cached_posts( $transient_version ) : $query->get_posts() );

		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		// Prime caches to reduce future queries.
		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( $results );
		}

		return $results;
	}

	/**
	 * Get the list of classes to apply to this block.
	 *
	 * @return string space-separated list of classes.
	 */
	protected function get_container_classes() {
		$classes = array(
			'mvx-block-wrapper',
			'wc-block-grid',
			"wp-block-{$this->block_name}",
			"wc-block-{$this->block_name}",
			"has-{$this->attributes['block_columns']}-columns",
		);

		if ( $this->attributes['block_rows'] > 1 ) {
			$classes[] = 'has-multiple-rows';
		}

		if ( ! empty( $this->attributes['className'] ) ) {
			$classes[] = $this->attributes['className'];
		}

		return implode( ' ', $classes );
	}

	/**
	 * Render a single products.
	 *
	 * @param int $id Product ID.
	 * @return string Rendered product output.
	 */
	public function render_product( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product ) {
			return '';
		}

		$data = (object) array(
			'permalink' => esc_url( $product->get_permalink() ),
			'image'     => $this->get_image_html( $product ),
			'title'     => $this->get_title_html( $product ),
			'rating'    => $this->get_rating_html( $product ),
			'price'     => $this->get_price_html( $product ),
			'badge'     => $this->get_sale_badge_html( $product ),
			'button'    => $this->get_button_html( $product ),
		);

		return apply_filters(
			'woocommerce_blocks_product_grid_item_html',
			"<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->title}
				</a>
				{$data->badge}
				{$data->price}
				{$data->rating}
				{$data->button}
			</li>",
			$data,
			$product
		);
	}

	/**
	 * Get the product image.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_image_html( $product ) {
		return '<div class="wc-block-grid__product-image">' . $product->get_image( 'woocommerce_thumbnail' ) . '</div>';
	}

	/**
	 * Get the product title.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_title_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['title'] ) ) {
			return '';
		}
		return '<div class="wc-block-grid__product-title">' . $product->get_title() . '</div>';
	}

	/**
	 * Render the rating icons.
	 *
	 * @param WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_rating_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['rating'] ) ) {
			return '';
		}
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();

		if ( $rating_count > 0 ) {
			return sprintf(
				'<div class="wc-block-grid__product-rating">%s</div>',
				wc_get_rating_html( $average, $rating_count )
			);
		}
		return '';
	}

	/**
	 * Get the price.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_price_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['price'] ) ) {
			return '';
		}
		return sprintf(
			'<div class="wc-block-grid__product-price price">%s</div>',
			$product->get_price_html()
		);
	}

	/**
	 * Get the sale badge.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_sale_badge_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['price'] ) ) {
			return '';
		}

		if ( ! $product->is_on_sale() ) {
			return;
		}

		return '<span class="wc-block-grid__product-onsale">
			<span aria-hidden="true">' . esc_html__( 'Sale!', 'multivendorx' ) . '</span>
			<span class="screen-reader-text">' . esc_html__( 'Product on sale', 'multivendorx' ) . '</span>
		</span>';
	}

	/**
	 * Get the button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_button_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['button'] ) ) {
			return '';
		}
		return '<div class="wp-block-button wc-block-grid__product-add-to-cart">' . $this->get_add_to_cart( $product ) . '</div>';
	}

	/**
	 * Get the "add to cart" button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_add_to_cart( $product ) {
		$attributes = array(
			'aria-label'       => $product->add_to_cart_description(),
			'data-quantity'    => '1',
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'rel'              => 'nofollow',
			'class'            => 'wp-block-button__link add_to_cart_button',
		);

		if ( $product->supports( 'ajax_add_to_cart' ) ) {
			$attributes['class'] .= ' ajax_add_to_cart';
		}

		return sprintf(
			'<a href="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			wc_implode_html_attributes( $attributes ),
			esc_html( $product->add_to_cart_text() )
		);
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
