<?php

class MVX_Settings {

    /**
     * Start up
     */
    public function __construct() {
        // Admin menu
        add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
        add_filter( 'admin_body_class', array( $this, 'mvx_add_loading_classes' ) );
        add_action('admin_head', array($this, 'mvx_submenu_count'));
    }

    /**
     * Add options page
     */
    public function add_settings_page() {
        global $submenu;
        $slug = 'mvx';
        $dashboard = add_menu_page( __( 'MultiVendorX', 'multivendorx' ), __( 'MultiVendorX', 'multivendorx' ), 'manage_woocommerce', $slug, [ $this, 'mvx_modules_callback' ],  'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g fill="#a7aaad" fill-rule="nonzero"><path d="M10.8,0H9.5C8,0,6.7,1.3,6.7,2.8V4C6.9,4,7,4,7.2,4.1V2.8c0-1.3,1-2.3,2.3-2.3h1.3
        c1.3,0,2.3,1,2.3,2.3v1.7c0.2,0,0.3,0,0.5,0V2.8C13.6,1.3,12.3,0,10.8,0z"/><path d="M16.8,4.4C7.6,6.8,3.7,1.9,1.8,4.9c-1.1,1.7,0.3,7.6,1.2,13c2,1.3,4.4,2.1,7,2.1
        c2.7,0,5.2-0.8,7.3-2.3C18.6,12.3,19.5,3.7,16.8,4.4z M6.7,10.3V9.9h0.7v0.4v0.2H6.7V10.3z M5.6,8.9h0.3v0.3H5.6V8.9z M3.9,9.2h0.6
        v0.6H3.9V9.2z M5,10.8H4.3v-0.6H5V10.8z M5.1,9.9H4.7V9.5h0.3V9.9z M5.3,9.1H4.9V8.7h0.5V9.1z M5.4,9.4h0.7v0.7H5.4V9.4z
         M14.3,16.3h-0.6v-0.6h0.6V16.3z M13.9,15.4v-0.3h0.3v0.3H13.9z M11.7,14.2l1.4,1.6h-0.6v1h1v-0.6l0.8,0.9h-2.4l-1.6-1.7l-1.6,1.7
        H6.3l2.5-3l-2.2-2.6V11H6.1l-0.5-0.6h0.9v0.4h1.2v-0.4h0.3l2.3,2.6L13,9.8c0.4-0.5,1-0.8,1.7-0.8h1.4L11.7,14.2z M13.1,15.5V15h0.5
        v0.5H13.1z M13.2,16.1v0.5h-0.5v-0.5H13.2z M5.9,11.8v-0.5h0.2h0.3v0.3v0.2H5.9z"/></g></svg>'), 50 );

        if ( current_user_can( 'manage_woocommerce' ) ) {

            $submenu[ $slug ][] = [ __( 'Dashboard', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=dashboard' ];

            $submenu[ $slug ][] = [ __( 'Work Board', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=work-board&name=activity-reminder' ];

            $submenu[ $slug ][] = [ __( 'Modules', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=modules' ];

            $submenu[ $slug ][] = [ __( 'Vendors', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=vendor' ];
            
            $submenu[ $slug ][] = [ __( 'Payments', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=payment&name=payment-masspay' ];

            $submenu[ $slug ][] = [ __( 'Commissions', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=commission' ];

            $submenu[ $slug ][] = [ __( 'Settings', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=settings&name=settings-general' ];

            $submenu[ $slug ][] = [ __( 'Analytics', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=analytics&name=admin-overview' ];
            
            $submenu[ $slug ][] = [ __( 'Status and Tools', 'multivendorx' ), 'manage_woocommerce', 'admin.php?page=' . $slug . '#&submenu=status-tools&name=database-tools' ];

            $submenu = apply_filters('mvx_backend_submenu_list', $submenu, $slug);

            $submenu[ $slug ][] = [ __( '<div id="help-and-support">Help & Support</div>', 'multivendorx' ), 'manage_woocommerce', 'https://multivendorx.com/' ];

            add_submenu_page( null, __( 'Transaction Details', 'multivendorx' ), __( 'Transaction Details', 'multivendorx' ), 'manage_woocommerce', 'mvx-transaction-details', array( $this, 'mvx_transaction_details' ) );
        }
    }

    public function mvx_add_loading_classes( $classes ) {
        $screen = get_current_screen();
        $page_details = array('toplevel_page_mvx');
        if ( in_array($screen->id, $page_details)) {
            $classes .= ' mvx-page';
        }
        return $classes;
    }

    public function mvx_submenu_count() {
        global $submenu;
        $screen = get_current_screen();
        $page_details = array('toplevel_page_mvx');
        if (isset($submenu['mvx']) && in_array($screen->id, $page_details)) {
            if (apply_filters('mvx_submenu_show_necesarry_count', true) && current_user_can('manage_woocommerce') ) {
                foreach ($submenu['mvx'] as $key => $menu_item) {
                    if (isset($menu_item[0]) && strpos($menu_item[0], 'Commission') !== false) {
                        $order_count = isset( mvx_count_commission()->unpaid ) ? mvx_count_commission()->unpaid : 0;
                        $submenu['mvx'][$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $order_count . '"><span class="processing-count">' . number_format_i18n($order_count) . '</span></span>';
                    }
                    if (isset($menu_item[0]) && strpos($menu_item[0], 'Work Board') !== false) {
                        $workboard_list_count = mvx_count_wordboard_list() ? mvx_count_wordboard_list() : 0;
                        $submenu['mvx'][$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $workboard_list_count . '"><span class="processing-count">' . number_format_i18n($workboard_list_count) . '</span></span>';
                    }
                }
            }
        }

        ?>
        <script>
            jQuery(document).ready( function($) {   
                $('#help-and-support').parent().attr('target','_blank');  
            });
        </script>
        <?php
    }

    public function mvx_modules_callback() {
        echo '<div id="mvx-admin-dashboard"></div>';
    }

    public function mvx_transaction_details() {
        global $MVX;
        ?>
        <div class="wrap blank-wrap"><h3><?php esc_html_e( 'Transaction Details', 'multivendorx' ); ?></h3></div>
        <div class="wrap mvx-settings-wrap panel-body">
            <?php
            $_is_trans_details_page = isset( $_REQUEST['page'] ) ? wc_clean($_REQUEST['page']) : '';
            $trans_id = isset( $_REQUEST['trans_id'] ) ? absint( $_REQUEST['trans_id'] ) : 0;
            if ( $_is_trans_details_page == 'mvx-transaction-details' && $trans_id != 0 ) {
                $transaction = get_post( $trans_id );
                $transaction_post_types = array('mvx_transaction', 'wcmp_transaction');
                if ( isset( $transaction->post_type ) && in_array( $transaction->post_type, $transaction_post_types) ) {
                    $vendor = get_mvx_vendor_by_term( $transaction->post_author ) ? get_mvx_vendor_by_term( $transaction->post_author ) : get_mvx_vendor( $transaction->post_author );
                    $commission_details = $MVX->transaction->get_transaction_item_details( $trans_id );
                    ?>
                    <table class="widefat fixed striped">
                        <?php
                        if ( ! empty( $commission_details['header'] ) ) {
                            echo '<thead><tr>';
                            foreach ( $commission_details['header'] as $header_val ) {
                                echo '<th>' . $header_val . '</th>';
                            }
                            echo '</tr></thead>';
                        }
                        echo '<tbody>';
                        if ( ! empty( $commission_details['body'] ) ) {

                            foreach ( $commission_details['body'] as $commission_detail ) {
                                echo '<tr>';
                                foreach ( $commission_detail as $details ) {
                                    foreach ( $details as $detail_key => $detail ) {
                                        echo '<td>' . $detail . '</td>';
                                    }
                                }
                                echo '</tr>';
                            }
                        }
                        if ( $totals = $MVX->transaction->get_transaction_item_totals( $trans_id, $vendor ) ) {
                            foreach ( $totals as $total ) {
                                echo '<tr><td colspan="3" >' . $total['label'] . '</td><td>' . $total['value'] . '</td></tr>';
                            }
                        }
                        echo '</tbody>';
                        ?>
                    </table>
                <?php } else { ?>
                    <p class="mvx-headding-wrapper"><?php echo __( 'Unfortunately transaction details are not found. You may try again later.', 'multivendorx' ); ?></p>
                <?php }
            } else {
                ?>
                <p class="mvx-headding-wrapper"><?php echo __( 'Unfortunately transaction details are not found. You may try again later.', 'multivendorx' ); ?></p> 
            <?php } ?>
        </div>
        <?php
    }
    
}
