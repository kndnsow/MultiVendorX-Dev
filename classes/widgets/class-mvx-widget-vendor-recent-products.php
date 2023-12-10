<?php

if (!defined('ABSPATH')) {
    exit;
}

class MVX_Widget_Vendor_Recent_Products extends WC_Widget {

	public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'mvx_vendor_rcent_products';
        $this->widget_description = __('Displays a list of vendor recent products on the vendor shop page.', 'multivendorx');
        $this->widget_id = 'mvx_vendor_recent_products';
        $this->widget_name = __('MVX: Vendor\'s Recent Products', 'multivendorx');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor recent products', 'multivendorx'),
                'label' => __('Title', 'multivendorx'),
            ),
            'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Number of products to show', 'multivendorx' ),
			),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $MVX;

        $store_id = mvx_find_shop_page_vendor();
        $vendor = get_mvx_vendor($store_id);        
        if ((!mvx_is_store_page() && !$vendor)) {
            return;
        }

        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

        $query_args = array(
            'posts_per_page' => $number,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'author'         => $vendor->id,
            'no_found_rows'  => 1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );
        
        $products = new WP_Query( apply_filters( 'woocommerce_products_widget_query_args', $query_args ) );
        
        if ( $products && $products->have_posts() ) {
            
            $this->widget_start( $args, $instance );;
            
            do_action($this->widget_cssclass . '_top', $vendor);

            echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

            $template_args = array(
                'widget_id'   => $args['widget_id'],
                //'show_rating' => true,
            );

            while ( $products->have_posts() ) {
                $products->the_post();
                wc_get_template( 'content-widget-product.php', $template_args );
            }

            echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );
            
            do_action($this->widget_cssclass . '_bottom', $vendor);

            $this->widget_end( $args );

        }
    }
}