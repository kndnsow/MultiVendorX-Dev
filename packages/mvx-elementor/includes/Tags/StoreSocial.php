<?php

class StoreSocial extends MVX_Elementor_TagBase {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @param array $data
     */
    public function __construct( $data = [] ) {
        parent::__construct( $data );
    }

    /**
     * Tag name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-social-tag';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Social', 'multivendorx' );
    }

    /**
     * Render tag
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render() {
        global $MVX, $mvx_elementor;
        $links       = [];
        $network_map = $mvx_elementor->get_social_networks_map();

        if ( mvx_is_store_page() ) {
            $store_id = mvx_find_shop_page_vendor();

            $social_info = $this->get_social_profiles($store_id);

            foreach ( $network_map as $mvx_name => $elementor_name ) {
                if ( ! empty( $social_info[ $mvx_name ] ) ) {
                    $links[ $elementor_name ] = $social_info[ $mvx_name ];
                }
            }
        } else {
            foreach ( $network_map as $mvx_name => $elementor_name ) {
                $links[ $elementor_name ] = '#';
            }
        }

        echo json_encode( $links );
    }

    protected function get_social_profiles($vendor_id) {
        $store_infos = array();
        $vendor_fb_profile = get_user_meta($vendor_id, '_vendor_fb_profile', true);
        $vendor_twitter_profile = get_user_meta($vendor_id, '_vendor_twitter_profile', true);
        $vendor_linkdin_profile = get_user_meta($vendor_id, '_vendor_linkdin_profile', true);
        $vendor_youtube = get_user_meta($vendor_id, '_vendor_youtube', true);
        $vendor_instagram = get_user_meta($vendor_id, '_vendor_instagram', true);
        $vendor_pinterest_profile = get_user_meta($vendor_id, '_vendor_pinterest_profile', true);
        if ($vendor_fb_profile) {
           $store_infos['fb'] = $vendor_fb_profile;
        }
        if ($vendor_twitter_profile) {
           $store_infos['twitter'] = $vendor_twitter_profile;
        }
        if ($vendor_linkdin_profile) {
           $store_infos['linkedin'] = $vendor_linkdin_profile;
        }
        if ($vendor_youtube) {
           $store_infos['youtube'] = $vendor_youtube;
        }
        if ($vendor_instagram) {
           $store_infos['instagram'] = $vendor_instagram;
        }
        if ($vendor_pinterest_profile) {
           $store_infos['pinterest'] = $vendor_pinterest_profile;
        }
        return $store_infos;
    }

}
