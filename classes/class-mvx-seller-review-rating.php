<?php

/**
 * MVX Frontend Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Seller_Review_Rating {

    public function __construct() {
        global $MVX;
        $rating_settings = mvx_get_option('mvx_general_sellerreview_settings_name');
        if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
            //add_action('woocommerce_after_main_content', array($this, 'mvx_seller_review_rating_form'), 5);
            if ( apply_filters( 'mvx_load_default_vendor_store', false ) ) {
                add_action('woocommerce_after_shop_loop', array($this, 'mvx_seller_review_rating_form'), 30);
            }
            add_action('add_meta_boxes', array($this, 'add_mvx_rating_meta_box'));
            add_action('comment_save_pre', array($this, 'save_mvx_rating_meta_box'));
            add_filter('widget_comments_args', array($this, 'remove_vendor_rating_from_recent_comment'), 10);
            add_action('woocommerce_order_item_meta_end', array($this, 'mvx_review_rating_link'), 10, 3);
        }
    }

    function mvx_vendor_list_rating_rating_value($vendor_term_id, $vendor_id) {
        global $MVX;
        $rating_info = mvx_get_vendor_review_info($vendor_term_id);
        $MVX->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
    }

    function mvx_review_rating_link($item_id, $item, $order) {
        global $MVX;
        $rating_settings = mvx_get_option('mvx_general_sellerreview_settings_name');
        $arr_values = array();
        $arr_status[] = 'completed';
        $arr_status[] = 'processing';
        $arr_status_final = apply_filters('mvx_rating_review_order_status_filter', $arr_status);
        if (get_mvx_vendor_settings('is_sellerreview_varified', 'general') == 'Enable') {
            if (is_array($arr_status_final) && in_array($order->get_status(), $arr_status_final)) {
                if ($item['product_id']) {
                    $product = get_post($item['product_id']);
                    if ($product) {
                        if (is_user_mvx_vendor($product->post_author)) {
                            $vendor = new MVX_Vendor($product->post_author);
                            $term_id = get_user_meta($vendor->id, '_vendor_term_id', true);
                            $term = get_term_by('id', $term_id, $MVX->taxonomy->taxonomy_name);
                            $term_link = get_term_link($term, $MVX->taxonomy->taxonomy_name);
                            if ( apply_filters( 'mvx_load_default_vendor_store', false ) ) {
                                $review_link = trailingslashit($term_link) . '#reviews';
                            } else {
                                $review_link = trailingslashit($term_link) . 'reviews';
                            }
                            $arr_values['vendor_review_link'] = $review_link;
                            $arr_values['shop_name'] = $vendor->page_title;
                            $arr_values['product_name'] = $product->post_title;
                            $MVX->template->get_template('review/review-link.php', array('review_data' => $arr_values));
                        }
                    }
                }
            }
        } else {
            if ($item['product_id']) {
                $product = get_post($item['product_id']);
                if ($product) {
                    if (is_user_mvx_vendor($product->post_author)) {
                        $vendor = new MVX_Vendor($product->post_author);
                        $term_id = get_user_meta($vendor->id, '_vendor_term_id', true);
                        $term = get_term_by('id', $term_id, $MVX->taxonomy->taxonomy_name);
                        $term_link = get_term_link($term, $MVX->taxonomy->taxonomy_name);
                        if ( apply_filters( 'mvx_load_default_vendor_store', false ) ) {
                            $review_link = trailingslashit($term_link) . '#reviews';
                        } else {
                            $review_link = trailingslashit($term_link) . 'reviews';
                        }
                        $arr_values['vendor_review_link'] = $review_link;
                        $arr_values['shop_name'] = $vendor->page_title;
                        $arr_values['product_name'] = $product->post_title;
                        $MVX->template->get_template('review/review-link.php', array('review_data' => $arr_values));
                    }
                }
            }
        }
    }

    function remove_vendor_rating_from_recent_comment($args) {
        $args['post__not_in'] = mvx_vendor_dashboard_page_id();
        return $args;
    }

    function mvx_seller_review_rating_form() {
        global $MVX;
        if (mvx_is_store_page()) {
            $MVX->template->get_template('mvx-vendor-review-form.php', array('vendor_id' => mvx_find_shop_page_vendor()));
        }
    }

    function add_mvx_rating_meta_box() {
        global $comment, $MVX;
        if (!empty($comment)) {
            if ($comment->comment_type == 'mvx_vendor_rating') {
                $screens = array('comment');
                foreach ($screens as $screen) {
                    add_meta_box(
                            'mvx_vendor_rating', __('Vendor Rating', 'multivendorx'), array($this, 'mvx_comment_vendor_rating_callback'), $screen, 'normal', 'high'
                    );
                }
            }
        }
    }

    function mvx_comment_vendor_rating_callback($comment) {
        global $MVX;
        $vendor_rating_id = get_comment_meta($comment->comment_ID, 'vendor_rating_id', true);
        $vendor = new MVX_Vendor($vendor_rating_id);
        if($vendor){
            $name = $vendor->page_title;
        }else{
            $user = new WP_User($vendor_rating_id);
            $name = $user->display_name;
        }
        ?>
        <table class="form-table">
            <tbody>			
        <?php $MVX->mvx_wp_fields->dc_generate_form_field($this->get_mvx_comment_rating_field($comment), array('in_table' => 1)); ?>
                <tr class="vendor_rating_author_wrapper">
                    <th class="vendor_rating_author_label_holder">
                        <p class="vendor_rating_author">
                            <strong><?php echo __('Vendor Name.', 'multivendorx'); ?></strong>
                        </p>
                        <label for="vendor_rating_author" class="screen-reader-text"><?php echo __('Vendor Name.', 'multivendorx'); ?></label>
                    </th>
                    <td>
        <?php echo $name; ?>
                    </td>
                </tr>
            </tbody>
        </table>
                <?php
            }

            function save_mvx_rating_meta_box($comment_data) {
                if (isset($_POST['vendor_rating']) && !empty($_POST['vendor_rating'])) {
                    if (isset($_POST['comment_ID']) && !empty($_POST['comment_ID'])) {
                        update_comment_meta($_POST['comment_ID'], 'vendor_rating', absint( $_POST['vendor_rating'] ) );
                    }
                }
                return $comment_data;
            }

            function get_mvx_comment_rating_field($comment) {
                global $MVX;
                $vendor_rating = get_comment_meta($comment->comment_ID, 'vendor_rating', true);
                $fields = apply_filters('mvx_vendor_rating_field_filter', array(
                    "vendor_rating" => array(
                        'label' => __('Vendor Rating.', 'multivendorx'),
                        'type' => 'select',
                        'desc' => __('Vendor Rating Star.', 'multivendorx'),
                        'options' => array('' => __('Please Select', 'multivendorx'), '1' => __('1 Star', 'multivendorx'), '2' => __('2 Star', 'multivendorx'), '3' => __('3 Star', 'multivendorx'), '4' => __('4 Star', 'multivendorx'), '5' => __('5 Star', 'multivendorx')),
                        'value' => $vendor_rating ? $vendor_rating : '',
                        'class' => 'user-profile-fields'
                    )
                ));
                return $fields;
            }

        }
        