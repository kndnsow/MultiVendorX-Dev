<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class       MVX Product Class
 *
 * @version     2.2.0
 * @package     MultiVendorX
 * @author 		MultiVendorX
 */
class MVX_Plugin_Usage_Tracker {

    public $plugin_file = null;

    public function __construct($plugin_path) {
        $this->plugin_file      = $plugin_path;
        add_filter( 'plugin_action_links_dc-woocommerce-multi-vendor/dc_product_vendor.php', array($this, 'deactivate_action_links' ));
        add_action( 'admin_print_footer_scripts-plugins.php', array($this, 'deactivate_reasons_form_script' ));
        add_action( 'wp_ajax_deactivation_form_' . esc_attr( 'dc-woocommerce-multi-vendor' ), array($this, 'deactivate_reasons_form_submit' ));
        add_action( 'admin_notices', array($this, 'notice' ) );
        add_action( 'admin_notices', array($this, 'review_admin_notice'));
        add_action( 'wp_ajax_multivendorx_admin_notice_action', array($this, 'multivendorx_admin_notice_action'), 10);
        add_action( 'admin_print_footer_scripts', array($this, 'notice_script' ) );
        /**
         * Deactivation Hook
         */
        register_deactivation_hook( $this->plugin_file . 'dc_product_vendor.php', array( $this, 'deactivate_mvx_this_plugin' ) );
        add_action( 'admin_print_styles-plugins.php', array( $this, 'deactivate_reasons_form_style' ) );
    }

    public function deactivate_mvx_this_plugin() {
        /**
         * Check tracking is allowed or not.
         */
        $allow_tracking = $this->is_tracking_allowed();
        if ( ! $allow_tracking ) {
            return;
        }
        $body                     = $this->get_data();
        $body['status']           = 'Deactivated';
        $body['deactivated_date'] = time();

        // Check deactivation reason and add for insights data.
        if ( false !== get_option( 'multivendorx_deactivation_reason_' . 'dc-woocommerce-multi-vendor' ) ) {
            $body['deactivation_reason'] = get_option( 'multivendorx_deactivation_reason_' . 'dc-woocommerce-multi-vendor' );
        }
        if ( false !== get_option( 'multivendorx_deactivation_details_' . 'dc-woocommerce-multi-vendor' ) ) {
            $body['deactivation_details'] = get_option( 'multivendorx_deactivation_details_' . 'dc-woocommerce-multi-vendor' );
        }
        $this->send_data( $body );
        delete_option( 'multivendorx_deactivation_reason_' . 'dc-woocommerce-multi-vendor' );
        delete_option( 'multivendorx_deactivation_details_' . 'dc-woocommerce-multi-vendor' );
    }

    /**
     * Is tracking allowed?
     *
     * @since 1.0.0
     */
    private function is_tracking_allowed() {
        // The mvx_plugin_action_block_notice option is an array of plugins that are being tracked
        $allow_tracking = get_option( 'mvx_plugin_action_block_notice' );
        // If this plugin is in the array, then tracking is allowed
        if ( $allow_tracking && $allow_tracking == 'yes' ) {
            return true;
        }
        return false;
    }

    public function multivendorx_admin_notice_action() {
        global $current_user;
        if (isset($_POST['multivendorx_admin_notice_action_type']) && 'later' === $_POST['multivendorx_admin_notice_action_type']) {
            set_transient('multivendorx_wp_review_request', 'yes', MONTH_IN_SECONDS);
        } elseif (isset($_POST['multivendorx_admin_notice_action_type']) && 'add_review' === $_POST['multivendorx_admin_notice_action_type']) {
            $user_id = $current_user->ID;
            add_user_meta($user_id, 'multivendorx_wp_review_request', 'true', true);
        } elseif (isset($_POST['multivendorx_admin_notice_action_type']) && 'review_closed' === $_POST['multivendorx_admin_notice_action_type']) {
            set_transient('multivendorx_wp_review_request', 'yes', MONTH_IN_SECONDS);
        } elseif (isset($_POST['multivendorx_admin_notice_action_type']) && 'multivendorx_wp_billing_phone_notice' === $_POST['multivendorx_admin_notice_action_type']) {
            $user_id = $current_user->ID;
            add_user_meta($user_id, 'multivendorx_wp_billing_phone_notice', 'true', true);
        }
    }

    public function review_admin_notice() {
        global $current_user;
        $user_id = $current_user->ID;
        if (false === ( $multivendorx_wp_review_request = get_transient('multivendorx_wp_review_request') ) && !get_user_meta($user_id, 'multivendorx_wp_review_request')) :
            ?>
            <div class="multivendorx_wp_review_request notice notice-info is-dismissible">
                <h3 style="margin: 10px 0;"><?php _e('MultiVendorX', 'multivendorx'); ?></h3>
                <p><?php esc_html_e('Hey, We would like to thank you for using our plugin. We would appreciate it if you could take a moment to drop a quick review that will inspire us to keep going.', 'multivendorx'); ?></p>
                <p>
                    <a class="button button-secondary" style="color:#333; border-color:#ccc; background:#efefef;" data-type="later">Remind me later</a>
                    <a class="button button-primary" data-type="add_review">Review now</a>
                </p>
            </div>
        <?php endif; ?>
        <script type="text/javascript">
            (function ($) {
                "use strict";
                var data_obj = {
                    action: 'multivendorx_admin_notice_action',
                    multivendorx_admin_notice_action_type: ''
                };
                $(document).on('click', '.multivendorx_wp_review_request a.button', function (e) {
                    e.preventDefault();
                    var elm = $(this);
                    var btn_type = elm.attr('data-type');
                    if (btn_type == 'add_review') {
                        window.open('https://wordpress.org/support/plugin/dc-woocommerce-multi-vendor/reviews/#new-post');
                    }
                    elm.parents('.multivendorx_wp_review_request').hide();

                    data_obj['multivendorx_admin_notice_action_type'] = btn_type;
                    $.ajax({
                        url: ajaxurl,
                        data: data_obj,
                        type: 'POST'
                    });

                }).on('click', '.multivendorx_wp_review_request .notice-dismiss', function (e) {
                    e.preventDefault();
                    var elm = $(this);
                    elm.parents('.multivendorx_wp_review_request').hide();
                    data_obj['multivendorx_admin_notice_action_type'] = 'review_closed';
                    $.ajax({
                        url: ajaxurl,
                        data: data_obj,
                        type: 'POST'
                    });

                }).on('click', '.multivendorx_wp_billing_phone_notice .notice-dismiss', function (e) {
                    e.preventDefault();
                    var elm = $(this);
                    elm.parents('.multivendorx_wp_billing_phone_notice').hide();
                    data_obj['multivendorx_admin_notice_action_type'] = 'multivendorx_wp_billing_phone_notice';
                    $.ajax({
                        url: ajaxurl,
                        data: data_obj,
                        type: 'POST'
                    });

                });
            })(jQuery);
        </script>
        <?php
    }

    public function deactivate_action_links( $links ) {
        if ( isset( $links['deactivate'] ) ) {
            $deactivation_link = $links['deactivate'];
            /**
             * Change the default deactivate button link.
             */
            $deactivation_link   = str_replace( '<a ', '<div class="multivendor-xs-goodbye-form-wrapper-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '"><div class="multivendor-xs-goodbye-form-bg"></div><span class="multivendor-xs-goodbye-form" id="multivendor-xs-goodbye-form"></span></div><a onclick="javascript:event.preventDefault();" id="multivendor-xs-goodbye-link-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '" ', $deactivation_link );
            $links['deactivate'] = $deactivation_link;
        }
        $links['write_review'] = '<a href="https://wordpress.org/support/plugin/dc-woocommerce-multi-vendor/reviews/#new-post">' . __('Write a Review', 'multivendorx') . '</a>';
        if (apply_filters('is_mvx_pro_plugin_inactive', true)) {
            $links['go_pro'] = '<a href="https://multivendorx.com/pricing/" class="mvx-pro-plugin">' . __('Get MultiVendorX Pro', 'multivendorx') . '</a>';
        }
        return $links;
    }

    public function deactivate_reasons_form_script() {
        $form              = $this->deactivation_reasons();
        $html              = '<div class="multivendor-xs-goodbye-form-head"><strong>' . esc_html( $form['heading'] ) . '</strong></div>';
        $html             .= '<div class="multivendor-xs-goodbye-form-body"><p class="multivendor-xs-goodbye-form-caption">' . esc_html( $form['body'] ) . '</p>';
        if ( is_array( $form['options'] ) ) {
            $html .= '<div id="multivendor-xs-goodbye-options" class="multivendor-xs-goodbye-options"><ul>';
            foreach ( $form['options'] as $option ) {
                if ( is_array( $option ) ) {
                    $id    = strtolower( str_replace( ' ', '_', esc_attr( $option['label'] ) ) );
                    $id    = $id . '_' . 'dc-woocommerce-multi-vendor';
                    $html .= '<li class="has-goodbye-extra">';
                    $html .= '<input type="radio" name="multivendor-xs-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '-goodbye-options" id="' . esc_attr( $id ) . '" value="' . esc_attr( $option['label'] ) . '">';
                    $html .= '<div><label for="' . esc_attr( $id ) . '">' . esc_attr( $option['label'] ) . '</label>';
                    if ( isset( $option['extra_field'] ) && ! isset( $option['type'] ) ) {
                        $html .= '<input type="text" style="display: none" name="' . esc_attr( $id ) . '" id="' . str_replace( ' ', '', esc_attr( $option['extra_field'] ) ) . '" placeholder="' . esc_attr( $option['extra_field'] ) . '">';
                    }
                    if ( isset( $option['extra_field'] ) && isset( $option['type'] ) ) {
                        $html .= '<' . $option['type'] . ' style="display: none" type="text" name="' . esc_attr( $id ) . '" id="' . str_replace( ' ', '', esc_attr( $option['extra_field'] ) ) . '" placeholder="' . esc_attr( $option['extra_field'] ) . '"></' . $option['type'] . '>';
                    }
                    $html .= '</div></li>';
                } else {
                    $id    = strtolower( str_replace( ' ', '_', esc_attr( $option ) ) );
                    $id    = $id . '_' . 'dc-woocommerce-multi-vendor';
                    $html .= '<li><input type="radio" name="multivendor-xs-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '-goodbye-options" id="' . esc_attr( $id ) . '" value="' . esc_attr( $option ) . '"> <label for="' . esc_attr( $id ) . '">' . esc_attr( $option ) . '</label></li>';
                }
            }
            $html .= '</ul></div><!-- .multivendor-xs-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '-goodbye-options -->';
            $html .= '<h6 class="mvx-collecting-area">' . __( 'We collect non-sensitive diagnostic data and plugin usage information along with your feedback. This will help us to offer you a better and improved product.', 'multivendorx' ) . '</h6>';
        }
        $html .= '</div><!-- .multivendor-xs-goodbye-form-body -->';
        $html .= '<p class="deactivating-spinner"><span class="spinner"></span> ' . __( 'Submitting form', 'multivendorx' ) . '</p>';

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $("#multivendor-xs-goodbye-link-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>").on("click",function(){
                    // We'll send the user to this deactivation link when they've completed or dismissed the form
                    var url = document.getElementById("multivendor-xs-goodbye-link-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>");
                    $('body').toggleClass('multivendor-xs-form-active-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>');
                    $(".multivendor-xs-goodbye-form-wrapper-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?> #multivendor-xs-goodbye-form").fadeIn();
                    $(".multivendor-xs-goodbye-form-wrapper-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?> #multivendor-xs-goodbye-form").html( '<?php echo $html; ?>' + '<div class="multivendor-xs-goodbye-form-footer"><div class="multivendor-xs-goodbye-form-buttons"><a id="multivendor-xs-submit-form-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>" class="multivendor-xs-submit-btn" href="#"><?php esc_html_e( 'Submit and Deactivate', 'multivendor-x' ); ?> </a>&nbsp;<a class="multivenddorxn-deactivate-btn" href="'+url+'"><?php esc_html_e( 'Skip & Deactivate', 'multivendor-x' ); ?></a></div></div>');
                    $('#multivendor-xs-submit-form-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>').on('click', function(e){
                        // As soon as we click, the body of the form should disappear
                        $("#multivendor-xs-goodbye-form-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?> .multivendor-xs-goodbye-form-body").fadeOut();
                        $("#multivendor-xs-goodbye-form-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?> .multivendor-xs-goodbye-form-footer").fadeOut();
                        // Fade in spinner
                        $("#multivendor-xs-goodbye-form-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?> .deactivating-spinner").fadeIn();
                        e.preventDefault();
                        var checkedInput = $("input[name='multivendor-xs-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>-goodbye-options']:checked"),
                            checkedInputVal, details;
                        if( checkedInput.length > 0 ) {
                            checkedInputVal = checkedInput.val();
                            details = $('input[name="'+ checkedInput[0].id +'"], textarea[name="'+ checkedInput[0].id +'"]').val();
                        }

                        if( typeof details === 'undefined' ) {
                            details = '';
                        }
                        if( typeof checkedInputVal === 'undefined' ) {
                            checkedInputVal = 'No Reason';
                        }

                        var data = {
                            'action': 'deactivation_form_<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>',
                            'values': checkedInputVal,
                            'details': details,
                            'security': "<?php echo wp_create_nonce( 'multivendorx_deactivation_nonce' ); ?>",
                            'dataType': "json"
                        }

                        $.post(
                            ajaxurl,
                            data,
                            function(response){
                                // Redirect to original deactivation URL
                                window.location.href = url;
                            }
                        );
                    });
                    $('#multivendor-xs-goodbye-options > ul ').on('click', 'li label, li > input', function( e ){
                        var parent = $(this).parents('li');
                        parent.siblings().find('label').next('input, textarea').css('display', 'none');
                        parent.find('label').next('input, textarea').css('display', 'block');
                    });
                    // If we click outside the form, the form will close
                    $('.multivendor-xs-goodbye-form-bg').on('click',function(){
                        $("#multivendor-xs-goodbye-form").fadeOut();
                        $('body').removeClass('multivendor-xs-form-active-<?php echo esc_attr( 'dc-woocommerce-multi-vendor' ); ?>');
                    });
                });
            });
        </script>

        <?php
    }

    public function deactivation_reasons() {
        $form            = array();
        $form['heading'] = __( 'Sorry to see you go', 'multivendor-x' );
        $form['body']    = __( 'Before you deactivate the plugin, would you quickly give us your reason for doing so?', 'multivendor-x' );

        $form['options'] = array(
            __( 'I no longer need the plugin', 'multivendor-x' ),
            [
                'label'       => __( 'I found a better plugin', 'multivendor-x' ),
                'extra_field' => __( 'Please share which plugin', 'multivendor-x' ),
            ],
            __( "I couldn't get the plugin to work", 'multivendor-x' ),
            __( 'It\'s a temporary deactivation', 'multivendor-x' ),
            [
                'label'       => __( 'Other', 'multivendor-x' ),
                'extra_field' => __( 'Please share the reason', 'multivendor-x' ),
                'type'        => 'textarea',
            ],
        );
        return apply_filters( 'multivendorx_form_text_' . 'dc-woocommerce-multi-vendor', $form );
    }

    public function deactivate_reasons_form_submit() {
        check_ajax_referer( 'multivendorx_deactivation_nonce', 'security' );
        if ( isset( $_POST['values'] ) ) {
            $values = sanitize_text_field( $_POST['values'] );
            update_option( 'multivendorx_deactivation_reason_' . 'dc-woocommerce-multi-vendor', $values, 'no' );
        }
        if ( isset( $_POST['details'] ) ) {
            $details = sanitize_text_field( $_POST['details'] );
            update_option( 'multivendorx_deactivation_details_' . 'dc-woocommerce-multi-vendor', $details, 'no' );
        }
        echo 'success';
        //deactivation mail sent
        $current_user_id = get_current_user_id();
        $email = WC()->mailer()->emails['WC_Email_Plugin_Deactivated_Mail'];
        $email->trigger($current_user_id);
        wp_die();
    }

    public function notice_script() {
        echo "<script type='text/javascript'>jQuery('.multivenddorxn-" . esc_attr( 'dc-woocommerce-multi-vendor' ) . "-collect').on('click', function(e) {e.preventDefault();jQuery('.multivenddorxn-data').slideToggle('fast');});</script>";
    }

    public function notice() {
        /**
         * Return if notice is not set.
         */

        $notice_options      = [
            'consent_button_text'   => __( 'What we collect.', 'multivendorx' ),
            'yes'                   => __( 'Sure, I\'d like to help', 'multivendorx' ),
            'no'                    => __( 'No Thanks.', 'multivendorx' ),
            'notice'                => __('Want to help make <strong>MultiVendorX</strong> even more awesome? You can get a <strong>10% discount coupon</strong> for Premium extensions if you allow us to track the usage.', 'multivendorx'),
            'extra_notice'          => __('We collect non-sensitive diagnostic data and plugin usage information.
            Your site URL, WordPress & PHP version, plugins & themes and email address to send you the
            discount coupon. This data lets us make sure this plugin always stays compatible with the most
            popular plugins and themes. No spam, I promise.', 'multivendorx'),
        ];

        if ( ! isset( $notice_options['notice'] ) ) {
            return;
        }
        /**
         * Check is allowed or blocked for notice.
         */
        $block_notice = get_option( 'mvx_plugin_action_block_notice' );
        if ( !empty($block_notice) ) {
            return;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        //$this->has_notice = true;

        $url_yes = add_query_arg( [
            'plugin'        => 'dc-woocommerce-multi-vendor',
            'plugin_action' => 'yes',
            ]
        );
        $url_no  = add_query_arg( array(
            'plugin'        => 'dc-woocommerce-multi-vendor',
            'plugin_action' => 'no',
            )
        );

        // Decide on notice text
        $notice_text       = $notice_options['notice'] . ' <a href="#" class="multivenddorxn-' . esc_attr( 'dc-woocommerce-multi-vendor' ) . '-collect" >' . $notice_options['consent_button_text'] . '</a>';
        $extra_notice_text = $notice_options['extra_notice'];

        ?>

        <div class="notice notice-success">
            <p><?php echo wp_kses_post( $notice_text ); ?></p>
            <div class="multivenddorxn-data" style="display: none;">
                <p><?php echo wp_kses_post( $extra_notice_text ); ?></p>
            </div>
            <p>
                <a href="<?php echo esc_url( $url_yes ); ?>" class="button-primary">
                    <?php echo esc_html( $notice_options['yes'] ); ?>
                </a>&nbsp;
                <a href="<?php echo esc_url( $url_no ); ?>" class="button-secondary">
                    <?php echo esc_html( $notice_options['no'] ); ?>
                </a>
            </p>
        </div>

        <?php

        if (isset($_GET['plugin_action'])) {
            update_option('mvx_plugin_action_block_notice', $_GET['plugin_action']);
            if ($_GET['plugin_action'] == 'yes') {
                $body                     = $this->get_data();
                $body['status']           = 'Deactivated';
                $body['deactivated_date'] = time();
                $this->send_data( $body );
                // create coupon
                $current_user = wp_get_current_user();
                $user_name = $current_user->display_name;
                $user_email_id = $current_user->user_email;
                $data['name'] = $user_name;
                $data['email'] = $user_email_id;
                $this->create_coupon_for_discount( $data );

                //After share site information sent a mail
                $current_user_id = get_current_user_id();
                $email = WC()->mailer()->emails['WC_Email_Send_Site_Information'];
                $email->trigger($current_user_id);
            }
        }
    }

    public function create_coupon_for_discount( $data = array(), $args = array() ) {
        if ( empty( $data ) ) {
            return;
        }
        $args = wp_parse_args( $args, array(
            'method'      => 'POST',
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'body'        => $data,
            'user-agent'  => 'PUT/1.0.0; ' . get_bloginfo( 'url' ),
            )
        );
        $endpoint = 'https://multivendorx.com/wp-json/mvx_thirdparty/v1/coupon_create_for_pro';
        $request = wp_remote_post( esc_url( $endpoint ), $args );
        if ( is_wp_error( $request ) || ( isset( $request['response'], $request['response']['code'] ) && $request['response']['code'] != 200 ) ) {
            return new \WP_Error( 500, 'Something went wrong.' );
        }
        return $request;
    }

    public function get_data() {
        $body = array(
            'plugin_slug'   => sanitize_text_field( 'dc-woocommerce-multi-vendor' ),
            'url'           => get_bloginfo( 'url' ),
            'site_name'     => get_bloginfo( 'name' ),
            'site_version'  => get_bloginfo( 'version' ),
            'site_language' => get_bloginfo( 'language' ),
            'charset'       => get_bloginfo( 'charset' ),
            'plugin_version' => MVX_PLUGIN_VERSION,
            'php_version'   => phpversion(),
            'multisite'     => is_multisite(),
            'file_location' => __FILE__,
        );

        // Collect the email if the correct option has been set
        if ( ! function_exists( 'wp_get_current_user' ) ) {
            include ABSPATH . 'wp-includes/pluggable.php';
        }
        $current_user = wp_get_current_user();
        $email        = $current_user->user_email;
        $admin_email_from_settings = get_option('admin_email', true) ? get_option('admin_email', true) : '';
        $one_user_email = '';
        $one_user = get_userdata(1);
        if ($one_user) {
            $one_user_email = get_userdata(1)->data->user_email;
        }

        $body['email'] = $email . ','. $one_user_email . ',' . $admin_email_from_settings;
        $body['server']           = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';

        /**
         * Collect all active and inactive plugins
         */
        if ( ! function_exists( 'get_plugins' ) ) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }
        $plugins        = array_keys( get_plugins() );
        $active_plugins = is_network_admin() ? array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) : get_option( 'active_plugins', array() );
        foreach ( $plugins as $key => $plugin ) {
            if ( in_array( $plugin, $active_plugins ) ) {
                unset( $plugins[ $key ] );
            }
        }
        $body['active_plugins']   = serialize($active_plugins);
        $body['inactive_plugins'] = serialize($plugins);
        // pro details added
        $body['pro_version'] = defined('MVX_PRO_PLUGIN_VERSION') ? MVX_PRO_PLUGIN_VERSION : '';
        $pro_key_details = get_option('wc_am_client_143434', true) ? get_option('wc_am_client_143434', true) : array();
        $pro_key_status  = get_option('wc_am_client_143434_activated', true) ? get_option('wc_am_client_143434_activated', true) : array();
        if (!empty($pro_key_details)) {
            $body['api_key'] = isset($pro_key_details['wc_am_client_143434_api_key']) ? $pro_key_details['wc_am_client_143434_api_key'] : '';
            $body['product_id'] = isset($pro_key_details['wc_am_client_143434_product_id']) ? $pro_key_details['wc_am_client_143434_product_id'] : '';
            $body[' pro_status'] = $pro_key_status ? $pro_key_status : '';
        }
        
        /**
         * Text Direction.
         */
        $body['text_direction'] = ( function_exists( 'is_rtl' ) ? ( is_rtl() ? 'RTL' : 'LTR' ) : 'NOT SET' );
        /**
         * Get Our Plugin Data.
         *
         * @since 3.0.0
         */
        $plugin = $this->plugin_data();
        if ( empty( $plugin ) ) {
            $body['message'] .= __( 'We can\'t detect any plugin information. This is most probably because you have not included the code in the plugin main file.', 'plugin-usage-tracker' );
            $body['status']   = 'NOT FOUND';
        } else {
            if ( isset( $plugin['Name'] ) ) {
                $body['plugin'] = sanitize_text_field( $plugin['Name'] );
            }
            if ( isset( $plugin['Version'] ) ) {
                $body['version'] = sanitize_text_field( $plugin['Version'] );
            }
            $body['status'] = 'Active';
        }

        /**
         * Get active theme name and version
         *
         * @since 3.0.0
         */
        $theme = wp_get_theme();
        if ( $theme->Name ) {
            $body['theme'] = sanitize_text_field( $theme->Name );
        }
        if ( $theme->Version ) {
            $body['theme_version'] = sanitize_text_field( $theme->Version );
        }
        return $body;
    }

    public function plugin_data() {
        if ( ! function_exists( 'get_plugin_data' ) ) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }
        $plugin = get_plugin_data( $this->plugin_file . 'dc_product_vendor.php' );
        return $plugin;
    }

    /**
     * Send the data to insights.
     *
     * @since 3.0.0
     */
    public function send_data( $body ) {
        /**
         * Get SITE ID
         */
        $site_id_key       = "multivendorx_dc-woocommerce-multi-vendor_site_id";
        $site_id           = get_option( $site_id_key, false );
        $failed_data       = [];
        $site_url          = get_bloginfo( 'url' );
        $original_site_url = get_option( "multivendorx_dc-woocommerce-multi-vendor_original_url", false );
        if ( $original_site_url === false ) {
            $site_id = false;
        }
        /**
         * Send Initial Data to API
         */

        if ( $site_id == false && $original_site_url === false ) {
            if ( isset( $_SERVER['REMOTE_ADDR'] ) && ! empty( $_SERVER['REMOTE_ADDR'] && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' ) ) {
                $country_request = wp_remote_get( 'http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR'] . '?fields=country' );
                if ( ! is_wp_error( $country_request ) && $country_request['response']['code'] == 200 ) {
                    $ip_data         = json_decode( $country_request['body'] );
                    $body['country'] = isset( $ip_data->country ) ? $ip_data->country : 'NOT SET';
                }
            }
            if ($body['country'] == 'NOT SET') {
                //$body['country']    =  get_option('woocommerce_default_country');
            }

            $body['plugin_slug'] = 'dc-woocommerce-multi-vendor';
            $body['url']         = $site_url;

            $request = $this->remote_post( $body );
            if ( ! is_wp_error( $request ) && $request['response']['code'] == 200 ) {
                $retrieved_body = json_decode( wp_remote_retrieve_body( $request ), true );
                if ( is_array( $retrieved_body ) && isset( $retrieved_body['siteId'] ) ) {
                    update_option( $site_id_key, $retrieved_body['siteId'], 'no' );
                    update_option( "multivendorx_dc-woocommerce-multi-vendor_original_url", $site_url, 'no' );
                    update_option( "multivendorx_dc-woocommerce-multi-vendor_{$retrieved_body['siteId']}", $body, 'no' );
                }
            } else {
                $failed_data = $body;
            }
        }

        if ( isset( $request ) && is_wp_error( $request ) ) {
            return $request;
        }

        if ( isset( $request ) ) {
            return true;
        }
        return false;
    }

        /**
     * WP_REMOTE_POST method responsible for send data to the API_URL
     *
     * @param array $data
     * @param array $args
     * @return void
     */
    protected function remote_post( $data = array(), $args = array() ) {
        if ( empty( $data ) ) {
            return;
        }

        $args = wp_parse_args( $args, array(
            'method'      => 'POST',
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'body'        => $data,
            'user-agent'  => 'PUT/1.0.0; ' . get_bloginfo( 'url' ),
            )
        );
        $endpoint = 'https://multivendorx.com/wp-json/mvx_thirdparty/v1/users_database_update';;
        $request = wp_remote_post( esc_url( $endpoint ), $args );
        if ( is_wp_error( $request ) || ( isset( $request['response'], $request['response']['code'] ) && $request['response']['code'] != 200 ) ) {
            return new \WP_Error( 500, 'Something went wrong.' );
        }

        return $request;
    }

        /**
     * Deactivate Reasons Form.
     * This form will appears when user wants to deactivate the plugin to send you deactivated reasons.
     *
     * @since 3.0.0
     */
    public function deactivate_reasons_form_style() {
        ?>
        <style>
            .multivendor-xs-form-active-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-bg {
                background: rgba(0, 0, 0, .8);
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 9;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor {
                position: relative;
                display: none;
            }

            .multivendor-xs-form-active-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor {
                display: flex !important;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                justify-content: center;
                align-items: center;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form {
                display: none;
            }

            .multivendor-xs-form-active-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form {
                position: relative !important;
                width: 550px;
                max-width: 80%;
                background: #fff;
                box-shadow: 2px 8px 23px 3px rgba(0, 0, 0, .2);
                border-radius: 3px;
                white-space: normal;
                overflow: hidden;
                display: block;
                z-index: 999999;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-head {
                background: #fff;
                color: #495157;
                padding: 18px;
                box-shadow: 0 0 8px rgba(0, 0, 0, .1);
                font-size: 15px;
            }
            .mvx-collecting-area {
                font-weight: 500;
                background: #eee;
                padding: 1.5rem;
                border: 0.06rem solid #d1d4d9;
                border-radius: 0.25rem;
                text-align: center;
                font-size: 0.875rem;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form .multivendor-xs-goodbye-form-head strong {
                font-size: 15px;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body {
                padding: 8px 18px;
                color: #333;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body label {
                padding-left: 5px;
                color: #6d7882;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body .multivendor-xs-goodbye-form-caption {
                font-weight: 500;
                font-size: 15px;
                color: #495157;
                line-height: 1.4;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body #multivendor-xs-goodbye-options {
                padding-top: 5px;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body #multivendor-xs-goodbye-options ul>li {
                margin-bottom: 15px;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body #multivendor-xs-goodbye-options ul>li>div {
                display: inline;
                padding-left: 3px;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body #multivendor-xs-goodbye-options ul>li>div>input,
            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-body #multivendor-xs-goodbye-options ul>li>div>textarea {
                margin: 10px 18px;
                padding: 8px;
                width: 80%;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .deactivating-spinner {
                display: none;
                padding-bottom: 20px !important;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .deactivating-spinner .spinner {
                float: none;
                margin: 4px 4px 0 18px;
                vertical-align: bottom;
                visibility: visible;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer {
                padding: 8px 18px;
                margin-bottom: 15px;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer>.multivendor-xs-goodbye-form-buttons {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer .multivendor-xs-submit-btn {
                background-image: linear-gradient(-28deg, #db4b54, #af3b74, #3f1473);
                -webkit-border-radius: 3px;
                border-radius: 3px;
                color: #f9f8fb;
                line-height: 1;
                padding: 15px 20px;
                font-size: 13px;
                font-weight: 500;
            }
            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer .multivendor-xs-submit-btn:hover {
                background-image: linear-gradient(110deg, #db4b54, #af3b74, #3f1473);

            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer .multivenddorxn-deactivate-btn {
                font-size: 13px;
                color: #a4afb7;
                background: none;
                float: right;
                padding-right: 10px;
                width: auto;
            }

            .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .test {}

            a.mvx-pro-plugin {
                font-weight: 700;
                background: linear-gradient(110deg, rgb(63, 20, 115) 0%, 25%, rgb(175 59 116) 50%, 75%, rgb(219 75 84) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            a.mvx-pro-plugin:hover {
                background: #3f1473;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            @media screen and (max-width: 768px) {
                .multivendor-xs-goodbye-form-wrapper-dc-woocommerce-multi-vendor .multivendor-xs-goodbye-form-footer>.multivendor-xs-goodbye-form-buttons {
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 2rem;
                }
            }
        </style>
        <?php
    }
}
