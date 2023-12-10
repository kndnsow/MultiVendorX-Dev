<?php
/**
 * MVX Vendor Quick Info Widget
 *
 * @author    Multivendor X
 * @category  Widgets
 * @package MultiVendorX/Widgets
 * @version   2.2.0
 * @extends   WP_Widget
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class DC_Widget_Quick_Info_Widget extends WP_Widget {

    public $response = array();

    /**
     * Construct
     */
    function __construct() {
        global $MVX, $wp_version;

        // Widget variable settings
        $this->widget_idbase = 'dc-vendor-quick-info';
        $this->widget_title = __('MVX: Contact Vendor', 'multivendorx');
        $this->widget_description = __('Adds a contact form on vendor\'s shop page so that customers can contact vendor directly( Admin will also get a copy of the same ).', 'multivendorx');
        $this->widget_cssclass = 'widget_mvx_quick_info';

        // Widget settings
        $widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);

        // Widget control settings
        $control_ops = array('width' => 250, 'height' => 350, 'id_base' => $this->widget_idbase);

        // Mail Syatem
        $this->response = array(
            0 => array(
                'message' => __('Unable to send email. Please try again.', 'multivendorx'),
                'class' => 'error'
            ),
            1 => array(
                'message' => __('Email sent successfully.', 'multivendorx'),
                'class' => 'message'
            ),
        );

        add_action('init', array($this, 'send_mail'), 20);

        // Create the widget
        if ($wp_version >= 4.3) {
            parent::__construct($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        } else {
            $this->WP_Widget($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        }
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget($args, $instance) {
        global $MVX, $woocommerce, $post;

        extract($args, EXTR_SKIP);
        $vendor_id = false;
        $vendor = false;
        // Only show current vendor widget when showing a vendor's product(s)
        $show_widget = false;
        if (is_singular('product')) {
            $vendor = get_mvx_product_vendors($post->ID);
            if ($vendor) {
                $show_widget = true;
            }
        }

        if (mvx_is_store_page()) {
            $show_widget = true;
        }

        $hide_from_guests = isset($instance['hide_from_guests']) ? $instance['hide_from_guests'] : false;
        if ($hide_from_guests) {
            $show_widget = is_user_logged_in();
        }

        if ($show_widget) {
            if (mvx_is_store_page()) {
                $vendor_id = mvx_find_shop_page_vendor();
                if ($vendor_id) {
                    $vendor = get_mvx_vendor($vendor_id);
                }
            }
            $args = array(
                'instance' => $instance,
                'vendor' => isset($vendor) ? $vendor : false,
                'current_user' => wp_get_current_user(),
                'widget' => $this,
                'post'  =>  is_singular('product') ? $post : ''
            );

            // Before widget (defined by themes)
            echo $before_widget;

            // Set up widget title
            if (isset($instance['title'])) {
                $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            } else {
                $title = false;
            }
            // Display the widget title if one was input (before and after defined by themes).
            if ($title) {
                echo $before_title . $title . $after_title;
            }

            // Action for plugins/themes to hook onto
            do_action($this->widget_cssclass . '_top');

            $MVX->template->get_template('widget/quick-info.php', $args);

            // Action for plugins/themes to hook onto
            do_action($this->widget_cssclass . '_bottom');

            // After widget (defined by themes).
            echo $after_widget;
        }
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['description'] = isset($new_instance['description']) ? strip_tags($new_instance['description']) : '';
        $instance['hide_from_guests'] = isset($new_instance['hide_from_guests']) ? $new_instance['hide_from_guests'] : false;
        $instance['enable_google_recaptcha'] = isset($new_instance['enable_google_recaptcha']) ? $new_instance['enable_google_recaptcha'] : false;
        $instance['google_recaptcha_type'] = isset($new_instance['google_recaptcha_type']) ? $new_instance['google_recaptcha_type'] : 'v2';
        $instance['recaptcha_v2_scripts'] = isset($new_instance['recaptcha_v2_scripts']) ? $new_instance['recaptcha_v2_scripts'] : '';
        $instance['recaptcha_v3_sitekey'] = isset($new_instance['recaptcha_v3_sitekey']) ? $new_instance['recaptcha_v3_sitekey'] : '';
        $instance['recaptcha_v3_secretkey'] = isset($new_instance['recaptcha_v3_secretkey']) ? $new_instance['recaptcha_v3_secretkey'] : '';
        $instance['submit_label'] = isset($new_instance['submit_label']) ? strip_tags($new_instance['submit_label']) : __('Submit', 'multivendorx');
        return $instance;
    }

    /**
     * The form on the widget control in the widget administration area
     * @since  1.0.0
     * @param  array $instance The settings for this instance.
     * @return void
     */
    function form($instance) {
        global $MVX;
        $defaults = array(
            'title' => __('Quick Info', 'multivendorx'),
            'description' => __('Do you need more information? Write to us!', 'multivendorx'),
            'hide_from_guests' => '',
            'enable_google_recaptcha' => false,
            'google_recaptcha_type' => 'v2',
            'recaptcha_v2_scripts' => '',
            'recaptcha_v3_sitekey' => '',
            'recaptcha_v3_secretkey' => '',
            'submit_label' => __('Submit', 'multivendorx'),
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'multivendorx') ?>:
                <input type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description', 'multivendorx') ?>:
                <input type="text" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" value="<?php echo esc_attr($instance['description']); ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('submit_label')); ?>"><?php esc_html_e('Submit Button Label Text', 'multivendorx') ?>:
                <input type="text" id="<?php echo esc_attr($this->get_field_id('submit_label')); ?>" name="<?php echo esc_attr($this->get_field_name('submit_label')); ?>" value="<?php echo esc_attr($instance['submit_label']); ?>" class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('hide_from_guests')); ?>"><?php esc_html_e('Hide from guests', 'multivendorx') ?>:
                <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('hide_from_guests')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_from_guests')); ?>" value="1" <?php checked($instance['hide_from_guests'], 1, true) ?> class="widefat" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('enable_google_recaptcha')); ?>"><?php esc_html_e('Enable Google Recaptcha', 'multivendorx') ?>:
                <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('enable_google_recaptcha')); ?>" name="<?php echo esc_attr($this->get_field_name('enable_google_recaptcha')); ?>" value="1" <?php checked($instance['enable_google_recaptcha'], 1, true) ?> class="mvx-widget-enable-grecaptcha widefat" />
            </label>
        </p>
        <p class="mvx-widget-vquick-info-captcha-type">
            <label for="<?php echo esc_attr($this->get_field_id('google_recaptcha_type')); ?>"><?php esc_html_e('Google Recaptcha Type', 'multivendorx') ?>:
                <select id="<?php echo esc_attr($this->get_field_id('google_recaptcha_type')); ?>" name="<?php echo esc_attr($this->get_field_name('google_recaptcha_type')); ?>" >
                    <option value="v2" <?php selected( $instance['google_recaptcha_type'], 'v2' ); ?>><?php esc_html_e( 'reCAPTCHA v2', 'multivendorx' ); ?></option>
                    <option value="v3" <?php selected( $instance['google_recaptcha_type'], 'v3' ); ?>><?php esc_html_e( 'reCAPTCHA v3', 'multivendorx' ); ?></option>
                </select>
            </label>
        </p>
        <p class="mvx-widget-vquick-info-captcha-wrap v2">
            <label for="<?php echo esc_attr($this->get_field_id('recaptcha_v2_scripts')); ?>"><?php esc_html_e('Recaptcha Script', 'multivendorx') ?>:
                <textarea id="<?php echo esc_attr($this->get_field_id('recaptcha_v2_scripts')); ?>" name="<?php echo esc_attr($this->get_field_name('recaptcha_v2_scripts')); ?>" class="widefat" rows="3">
                    <?php echo wp_kses_post($instance['recaptcha_v2_scripts']); ?>
                </textarea> 
            </label>
        </p>
        <p class="mvx-widget-vquick-info-captcha-wrap v3">
            <label for="<?php echo esc_attr($this->get_field_id('recaptcha_v3_sitekey')); ?>"><?php esc_html_e('Site key', 'multivendorx') ?>:
                <input type="text" id="<?php echo esc_attr($this->get_field_id('recaptcha_v3_sitekey')); ?>" name="<?php echo esc_attr($this->get_field_name('recaptcha_v3_sitekey')); ?>" value="<?php echo esc_attr($instance['recaptcha_v3_sitekey']); ?>" class="widefat" />
            </label>
        </p>
        <p class="mvx-widget-vquick-info-captcha-wrap v3">
            <label for="<?php echo esc_attr($this->get_field_id('recaptcha_v3_secretkey')); ?>"><?php esc_html_e('Secret key', 'multivendorx') ?>:
                <input type="text" id="<?php echo esc_attr($this->get_field_id('recaptcha_v3_secretkey')); ?>" name="<?php echo esc_attr($this->get_field_name('recaptcha_v3_secretkey')); ?>" value="<?php echo esc_attr($instance['recaptcha_v3_secretkey']); ?>" class="widefat" />
            </label>
        </p>
        <?php
    }

    /**
     * Send the quick info form mail
     *
     * @since 1.0
     * @return void
     * @author 		MultiVendorX
     */
    function send_mail() {
        if ($this->check_form()) {

            /* === Sanitize Form Value === */
            $vendor = get_mvx_vendor(absint($_POST['quick_info']['vendor_id']));
            
            $mail = WC()->mailer()->emails['WC_Email_Vendor_Contact_Widget'];
            $result = $mail->trigger( $vendor, wc_clean($_POST['quick_info']) );
            if( $result ){
                wc_add_notice(__('Email sent successfully.', 'multivendorx'), 'success');
            }else{
                wc_add_notice(__('Unable to send email. Please try again.', 'multivendorx'), 'error');
            }
            wp_redirect(wc_clean($_POST['_wp_http_referer']));
            exit;
        }
    }

    /**
     * Check form information
     *
     * @return bool
     */
    function check_form() {
        if( isset( $_POST['enable_recaptcha'] ) ){
    
            if( isset( $_POST['recaptcha_type'] ) && $_POST['recaptcha_type'] == 'v2' ){
                if ( isset( $_POST['g-recaptcha-response'] ) && empty( $_POST['g-recaptcha-response'] ) ) {
                    wc_add_notice(__( 'Please Verify Recaptcha', 'multivendorx' ), 'error' );
                    return false;
                }
            }elseif( isset( $_POST['recaptcha_type'] ) && $_POST['recaptcha_type'] == 'v3' ) {
                $recaptcha_secret = isset( $_POST['recaptchav3_secretkey'] ) ? wc_clean($_POST['recaptchav3_secretkey']) : '';
                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                $recaptcha_response = isset( $_POST['recaptchav3_response']) ? wc_clean($_POST['recaptchav3_response']) : '';
    
                $recaptcha = file_get_contents( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response );
                $recaptcha = json_decode( $recaptcha );
    
                if ( !$recaptcha->success || $recaptcha->score < 0.5 ) {
                    wc_add_notice(__( 'Please Verify Recaptcha', 'multivendorx' ), 'error' );
                    return false;
                }
            }
        }
        
        return
                !empty($_POST['dc_vendor_quick_info_submitted']) &&
                isset($_POST['dc_vendor_quick_info_submitted']) &&
                wp_verify_nonce($_POST['dc_vendor_quick_info_submitted'], 'dc_vendor_quick_info_submitted') &&
                isset($_POST['quick_info']) &&
                !empty($_POST['quick_info']) && 
                isset($_POST['quick_info']['email']) &&
                !empty($_POST['quick_info']['email']) &&
                isset($_POST['quick_info']['message']) &&
                !empty($_POST['quick_info']['message']) &&
                isset($_POST['quick_info']['vendor_id']) &&
                !empty($_POST['quick_info']['vendor_id']) &&
                empty($_POST['quick_info']['spam']);
    }

}
