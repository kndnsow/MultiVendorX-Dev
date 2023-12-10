<?php

class StoreGetSupport extends MVX_Elementor_TagBase {

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
        return 'mvx-store-get-support-tag';
    }

    /**
     * Tag title
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_title() {
        return __('Store Get Support Button', 'multivendorx');
    }

    /**
     * Render tag
     *
     * @since 3.7
     *
     * @return void
     */
    public function render() {
    	global $MVX;
        if (mvx_is_store_page()) {
        	echo esc_html_e('Get Support', 'multivendorx');
        } else {
            return;
        }
    }
}
