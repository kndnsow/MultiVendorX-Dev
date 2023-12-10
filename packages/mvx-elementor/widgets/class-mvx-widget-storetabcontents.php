<?php

class MVX_Elementor_StoreTabContents extends MVX_Elementor_StoreName {

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-tab-contents';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Tab Contents', 'multivendorx' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-products';
    }

    /**
     * Widget categories
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_categories() {
        return [ 'mvx-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'mvx', 'store', 'vendor', 'tab', 'content', 'products' ];
    }

    /**
     * Register widget controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    	global $mvx_elementor;
        $this->add_control(
            'products',
            [
                'type' => MVX_Elementor_DynamicHidden::CONTROL_TYPE,
                'dynamic' => [
                    'active' => true,
                    'default' => $mvx_elementor->mvx_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'mvx-store-dummy-products' ),
                ]
            ],
            [
                'position' => [ 'of' => '_title' ],
            ]
        );
    }

    /**
     * Set wrapper classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' mvx-store-tab-content elementor-widget-' . parent::get_name();
    }

    /**
     * Frontend render method
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function render() {
        if ( mvx_is_store_page() ) {
            global $MVX;
            $store_id = mvx_find_shop_page_vendor();
            $tab = 'products';
            if ( get_query_var( 'reviews' ) ) {
                $tab = 'reviews';
            }
            if ( get_query_var( 'policies' ) ) {
                $tab = 'policies';
            }
            $vendor_id = $store_id;
            $vendor = get_mvx_vendor($vendor_id);
            $vendor_products = $vendor ? wp_list_pluck( $vendor->get_products_ids(), 'ID' ) : '';
            $vendor_product_ids_string = is_array($vendor_products) ? implode(",", $vendor_products) : '';

            $is_block = get_user_meta($vendor->id, '_vendor_turn_off' , true);
            if ($is_block) {
                $block_vendor_desc = apply_filters('mvx_blocked_vendor_text', __('Site Administrator has blocked this vendor', 'multivendorx'), $vendor);
                ?><p class="blocked_desc"><?php echo esc_html($block_vendor_desc); ?><p><?php
            } else {
                switch( $tab ) {
                    case 'reviews':
                    $MVX->review_rating->mvx_seller_review_rating_form();
                    break;

                    case 'policies':
                    $MVX->frontend->mvx_vendor_shop_page_policies_endpoint($store_id, $tab);
                    break;
                    
                    default:

                    if (is_array($vendor_products) && !empty($vendor_products) && count($vendor_products) > 0) {
                        echo apply_filters('mvx_elementor_vendor_product_page', do_shortcode( '[products ids='. $vendor_product_ids_string .' limit="12" paginate="true"]' ), $vendor_id);
                    } else {
                        do_action( 'woocommerce_no_products_found' );
                    }
                    break;
                }
            }

        } else {
            $settings = $this->get_settings_for_display();
            echo $settings['products'];
        }
    }

    /**
     * Elementor builder content template
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function content_template() {
        ?>
            <#
                print( settings.products );
            #>
        <?php
    }
}
