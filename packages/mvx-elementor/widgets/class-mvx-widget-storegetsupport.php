<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class MVX_Elementor_StoreGetSupport extends Widget_Button {

    /**
     * Widget name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-get-support';
    }

    /**
     * Widget title
     *
     * @since 3.7
     *
     * @return string
     */                                                  
    public function get_title() {
        return __('Store Get Support Button', 'multivendorx');
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
        return ['mvx-store-elements-single'];
    }

    /**
     * Widget keywords
     *
     * @since 3.7
     *
     * @return array
     */
    public function get_keywords() {
        return ['mvx', 'store', 'get support', 'button'];
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
                    'default' => $mvx_elementor->mvx_elementor()->dynamic_tags->tag_data_to_tag_text(null, 'mvx-store-get-support-tag'),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .mvx-store-get-support-btn' => 'width: auto; margin: 0;',
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
                'placeholder' => __('No link required.', 'multivendorx'),
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
        return parent::get_button_wrapper_class() . 'mvx-store-get-support-wrap';
    }
    /**
     * Button class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_class() {
        return 'mvx-store-get-support';
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
        if (!mvx_is_store_page()) {
            return;
        }
        $current_user = wp_get_current_user() ? wp_get_current_user() : '';
        $vendor_id = mvx_find_shop_page_vendor() ? mvx_find_shop_page_vendor() : '';
        if (!empty($current_user) && !empty($vendor_id)) {
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'shop_order',
                'author__in' => $vendor_id,
                'post_status' => 'any',
                'order' => 'DESC',
                'meta_query' => array(
                    array(
                        'key' => '_customer_user',
                        'value' => absint($current_user->ID),
                        'compare' => '='
                    ),
                ),
            );
            $customer_orders = get_posts($args);
            ?>
            <button type="button" class="mvx-support-butn <?php echo is_user_logged_in() ? 'mvx-get-support-butn' : ''; ?>" ><span></span><?php esc_html_e('Get Support', 'multivendorx')?></button>
            <div id="store-support-modal" class="modal fade mvx-support-modal" role="dialog" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?php _e('Create a new support topic', 'multivendorx') ?></h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body"> 
                            <div class="form-group">
                                <strong><?php printf(__('Hi, %s', 'multivendorx'), $current_user->display_name); ?></strong>
                            </div>
                            <p id="show-sucesss-msg" class="show-sucesss-msg" ></p>
                            <div class="form-group">
                                <label><?php _e('Subject: ', 'multivendorx'); ?></label><br>
                                <input type="text" id="support_topic" ><br>
                            </div>
                            <div class="form-group">
                                <select id="order_id">
                                    <option><?php esc_html_e('Select Order ID', 'multivendorx'); ?></option>
                                    <?php
                                    if (!empty($customer_orders)) {
                                        foreach ($customer_orders as $order) :
                                            ?>
                                            <option value='<?php echo esc_attr($order->ID); ?>'><?php esc_html_e('Order', 'multivendorx'); ?> # <?php echo esc_html($order->ID); ?></option>
                                            <?php
                                        endforeach;  
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Message: ', 'multivendorx'); ?></label>
                                <textarea class="form-control" rows="5" id="support_msg" placeholder="<?php _e('Enter message...', 'multivendorx') ?>"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id='store_id' value="<?php echo esc_attr($vendor_id); ?>" />
                        <div class="modal-footer">
                            <button type="button" class="mvx-submit-store-support-info btn btn-default"><?php _e('Submit', 'multivendorx') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        parent::render();
    }  
}
