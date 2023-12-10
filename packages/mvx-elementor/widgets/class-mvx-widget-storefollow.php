<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class MVX_Elementor_StoreFollow extends Widget_Button {

    /**
     * Widget name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-follow';
    }

    /**
     * Widget title
     *
     * @since 3.7
     *
     * @return string
     */                                                  
    public function get_title() {
        return __( 'Store Follow Button', 'multivendorx' );
    }

    /**
     * Widget icon class
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_icon() {
        return 'fa fa-child';
    }
    
    /**
     * Widget categories
     *
     * @since 3.7
     *
     * @return array
     */
    public function get_categories() {
        return [ 'mvx-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 3.7
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'mvx', 'store', 'vendor', 'button', 'follower', 'follow', 'following', 'unfollow' ];
    }

    /**
     * Register widget controls
     *
     * @since 3.7
     *
     * @return void
     */
    protected function _register_controls() {
    	global $mvx_elementor;
    	  
        parent::_register_controls();
        
        $this->update_control(
            'icon_align',
            [
                'default' => 'left',
            ]
        );

        $this->update_control(
            'button_text_color',
            [
                'default' => '#ffffff',
            ]
        );

        $this->update_control(
            'background_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'border_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'text',
            [
                'dynamic'   => [
                    'default' => $mvx_elementor->mvx_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'mvx-store-follow-tag' ),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .mvx-store-follow-btn' => 'width: auto; margin: 0;',
                ]
            ]
        );
        
        $this->update_control(
            'link',
			[
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
				],
				'dynamic' => [
					'active' => false,
				],
				'placeholder' => __( 'No link required.', 'multivendorx' ),
			]
        );
    }

    /**
     * Button wrapper class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_wrapper_class() {
        return parent::get_button_wrapper_class() . ' mvx-store-follow-wrap';
    }
    /**
     * Button class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_class() {
        return 'mvx-store-follow';
    }

    /**
     * Render button
     *
     * @since 3.7
     *
     * @return void
     */
    protected function render() {
        global $MVX;

        if ( ! mvx_is_store_page() ) {
            return;
        }
        $vendor_id = mvx_find_shop_page_vendor();
        $mvx_customer_follow_vendor = get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) : array();
        $vendor_lists = !empty($mvx_customer_follow_vendor) ? wp_list_pluck( $mvx_customer_follow_vendor, 'user_id' ) : array();
        $follow_status = in_array($vendor_id, $vendor_lists) ? __( 'Unfollow', 'multivendorx' ) : __( 'Follow', 'multivendorx' );

        $this->add_render_attribute( 'button', 'class', 'mvx-butn' );
        if (is_user_logged_in()) {
            $this->add_render_attribute( 'button', 'class', 'mvx-stroke-butn' );
        }
        $this->add_render_attribute( 'button', 'data-vendor_id', $vendor_id );
        $this->add_render_attribute( 'button', 'data-status', $follow_status );			
		
        parent::render();
    }
    
}
