<?php

class StoreChat extends MVX_Elementor_TagBase {

    /**
     * Class constructor
     *
     * @since 3.7
     *
     * @param array $data
     */
    public function __construct( $data = [] ) {
        parent::__construct( $data );
    }

    /**
     * Tag name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-chat-tag';
    }

    /**
     * Tag title
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Chat Button', 'multivendorx' );
    }

    /**
     * Render tag
     *
     * @since 3.7
     *
     * @return void
     */
    public function render() {
        global $product;
        if (mvx_is_store_page()) {
            $vendor_id = mvx_find_shop_page_vendor();
        } else {
            $vendor = get_mvx_product_vendors($product->get_id());
            $vendor_id = $vendor->id;
        }
        $enable_vendor_chat = !empty(get_user_meta($vendor_id, 'vendor_chat_enable', true)) ? get_user_meta($vendor_id, 'vendor_chat_enable', true) : '';
        if( get_live_chat_settings('enable_chat') != 'Enable' ) {
            esc_html_e( 'Chat module is not active', 'multivendorx' );
            return;
        }
        esc_html_e('Chat Now', 'multivendorx');
    }
}
