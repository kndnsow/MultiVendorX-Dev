<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class MVX_Elementor_StoreChat extends Widget_Button {

    /**
     * Widget name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-chat';
    }

    /**
     * Widget title
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Chat Button', 'multivendorx' );
    }

    /**
     * Widget icon class
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_icon() {
        return 'fa fa-comments';
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
        return [ 'mvx', 'store', 'vendor', 'button', 'chat', 'live chat', 'chat now' ];
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
                    'default' => $mvx_elementor->mvx_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'mvx-store-chat-tag' ),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .mvx-store-chat-btn' => 'width: auto; margin: 0;',
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
        return parent::get_button_wrapper_class() . ' mvx-store-chat-wrap';
    }
    /**
     * Button class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_class() {
        return 'mvx-store-chat';
    }

    /**
     * Render button
     *
     * @since 3.7
     *
     * @return void
     */
    protected function render() {
    	global $product, $MVX_Live_Chat;
    	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );  
        if ( !is_plugin_active('mvx-live-chat/mvx_live_chat.php') ) return;

    	$vendor_id = 0;
		if (mvx_is_store_page()) {
            $vendor_id = mvx_find_shop_page_vendor();
        } elseif (is_product()) {
            $vendor = get_mvx_product_vendors($product->get_id());
            $vendor_id = $vendor->id;
        }
        if( !$vendor_id ) return;
        
        $enable_vendor_chat = !empty(get_user_meta($vendor_id, 'vendor_chat_enable', true)) ? get_user_meta($vendor_id, 'vendor_chat_enable', true) : '';
        if( get_live_chat_settings('enable_chat') != 'Enable' || !is_user_logged_in() ) {
            return;
        }
        $vendor = get_mvx_vendor( $vendor_id );

        if( get_live_chat_settings('enable_chat') == 'Enable' && $enable_vendor_chat ) {
            if(get_live_chat_settings('chat_provider') == 'talkjs') {

                $user = wp_get_current_user();
                $chat_setting = mvx_get_option('mvx_live_chat_settings_name');

                $online = false;
                $remote_url = 'https://api.talkjs.com/v1/'.$chat_setting['app_id'].'/users/'.$vendor->id.'/sessions';
                $secret_token = $chat_setting['app_secret']; 
                $args = array(
                    'headers'     => array(
                        'Authorization' => 'Bearer ' . $secret_token,
                    ),
                ); 
                $response = wp_remote_get( $remote_url, $args );
                if (!is_wp_error($response) && isset($response['body'])) {
                    if(!empty(json_decode($response['body']))) {
                        $online = true;
                    }
                }
                $active_vendor = ($online) ? 'active' : 'offline';
                
                $me = array(
                    "id" => $user->ID, 
                    "name" => $user->data->display_name,
                    "email" => $user->data->user_email,
                );
                $other = array(
                    "id" => $vendor->id,
                    "name" => $vendor->user_data->data->display_name,
                    "email" => $vendor->user_data->data->user_email,
                );
                $talk_setup_data = array('me' => $me, 'other' => $other, 'app_id' => $chat_setting['app_id'], 'signature' => hash_hmac('sha256', $user->ID, $chat_setting['app_secret']), 'vendor_avatar' => $MVX_Live_Chat->plugin_url.'assets/images/seller_avatar.png', 'customer_avatar' => $MVX_Live_Chat->plugin_url.'assets/images/buyer_avatar.png');

                wp_enqueue_script('talk_min_setup', $MVX_Live_Chat->plugin_url . 'assets/frontend/js/customer_chat.js', array('jquery'), $MVX_Live_Chat->version, true);
                wp_localize_script('talk_min_setup', 'talk_setup_data', $talk_setup_data);
            }
        }

        $this->add_render_attribute( 'button', 'class', 'mvx-vendor-status' );
        $this->add_render_attribute( 'button', 'id', 'btn-getInTouch' );
        $this->add_render_attribute( 'button', 'class', $active_vendor );
        
		parent::render();
    }
}
