<?php

if (!defined('ABSPATH'))
    exit;

/**
 * MVX Shipping Class
 *
 * @version		3.2.2
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Shipping_Gateway {

    /**
     * Initialize shipping.
     */
    public function __construct() {
        
        add_action('woocommerce_shipping_init', array(&$this, 'load_shipping_methods'));
        add_filter('woocommerce_shipping_methods', array(&$this, 'add_shipping_methods'));
        add_filter( 'woocommerce_cart_shipping_packages', array(&$this, 'add_vendor_id_to_package'), 16);
        
        // Load vendor shipping methods configure fields
        add_action('mvx_vendor_shipping_free_shipping_configure_form_fields', array($this, 'free_shipping_configure_form_fields'), 10, 2);
        add_action('mvx_vendor_shipping_flat_rate_configure_form_fields', array($this, 'flat_rate_configure_form_fields'), 10, 2);
        add_action('mvx_vendor_shipping_local_pickup_configure_form_fields', array($this, 'local_pickup_configure_form_fields'), 10, 2);
    }
    
    /**
     * Loads shipping zones & methods.
     * 
     */
    public function load_shipping_methods() {
        self::load_class( 'shipping-zone', 'helpers' );
        self::load_class( 'shipping-method' );
        self::load_class( 'distance-shipping-method' );
        self::load_class( 'country-shipping-method' );
    }
    /**
     * MVX Shipping methods register themselves by returning their main class name through the woocommerce_shipping_methods filter.
     *
     * @return array
     */
    public function add_shipping_methods($methods) {
        $methods['mvx_vendor_shipping'] = 'MVX_Vendor_Shipping_Method';
        $methods['mvx_vendor_distance_shipping'] = 'MVX_Shipping_By_Distance';
        $methods['mvx_vendor_country_shipping'] = 'MVX_Shipping_By_Country';    
        return apply_filters( 'mvx_vendor_shipping_method_init', $methods );
    }

    public function add_vendor_id_to_package($packages) {
        foreach ($packages as $key => $package) {
            $packages[$key]['vendor_id'] = ($key) ? $key : $package['user']['ID']; // $key is the vendor_id
        }
        return $packages;
    }
    
    /**
     * CLass Loader
     *
     * @access public
     * @param mixed $class_name
     * @param mixed $dir
     * @return void
     */
    public static function load_class($class_name = '', $dir = '') {
        global $MVX;
        if ('' != $class_name && defined( 'MVX_PLUGIN_TOKEN' ) ) {
            $token = MVX_PLUGIN_TOKEN;
            if($dir)
                require_once ('shipping-gateways/' . trailingslashit( $dir ) . 'class-' . esc_attr($token) . '-' . esc_attr($class_name) . '.php');
            else
                require_once ('shipping-gateways/class-' . esc_attr($token) . '-' . esc_attr($class_name) . '.php');
        }
    }
    
    
    public function free_shipping_configure_form_fields( $shipping_method, $postdata ){
        ?>
        <div id="wrapper-<?php echo $shipping_method['id'] ?>">
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Method Title', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="method_title_fs" class="form-control" type="text" name="title" value="<?php echo isset($shipping_method['title']) ? $shipping_method['title'] : ''; ?>" placeholder="<?php esc_attr_e( 'Enter method title', 'multivendorx' ); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Minimum order amount for free shipping', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="minimum_order_amount_fs" class="form-control" type="text" name="min_amount" value="<?php echo isset($shipping_method['settings']['min_amount']) ? $shipping_method['settings']['min_amount'] : ''; ?>" placeholder="<?php esc_attr_e( '0.00', 'multivendorx' ); ?>">
                </div>
            </div>
            <input type="hidden" id="method_description_fs" name="description" value="<?php echo isset($shipping_method['settings']['description']) ? $shipping_method['settings']['description'] : ''; ?>" />
            <input type="hidden" id="method_cost_fs" name="cost" value="0" />
            <input type="hidden" id="method_tax_status_fs" name="tax_status" value="none" />
        </div>
        <?php
    }
    
    public function flat_rate_configure_form_fields( $shipping_method, $postdata ){
        $is_method_taxable_array = array(
            'none'      => __( 'None', 'multivendorx' ),
            'taxable'   => __( 'Taxable' , 'multivendorx' )
        );

        $calculation_type = array(
            'class' => __( 'Per class: Charge shipping for each shipping class individually', 'multivendorx' ),
            'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'multivendorx' ),
        );
        ?>
        <div id="wrapper-<?php echo $shipping_method['id'] ?>">
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Method Title', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="method_title_fr" class="form-control" type="text" name="title" value="<?php echo isset($shipping_method['title']) ? $shipping_method['title'] : ''; ?>" placeholder="<?php esc_attr_e( 'Enter method title', 'multivendorx' ); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Cost', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="method_cost_fr" class="form-control" type="text" name="cost" value="<?php echo isset($shipping_method['settings']['cost']) ? $shipping_method['settings']['cost'] : ''; ?>" placeholder="<?php esc_attr_e( '0.00', 'multivendorx' ); ?>">
                </div>
            </div>
            <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                <div class="form-group">
                    <label for="" class="control-label"><?php _e( 'Tax Status', 'multivendorx' ); ?></label>
                    <div class="col-md-9 col-sm-9">
                        <select id="method_tax_status_fr" class="form-control" name="tax_status">
                            <?php foreach( $is_method_taxable_array as $key => $value ) { 
                                $selected = '';
                                if (isset($shipping_method['settings']['tax_status']) && $shipping_method['settings']['tax_status'] == $key) {
                                   $selected = 'selected="selected"';
                                }?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <input type="hidden" id="method_description_fr" name="description" value="<?php echo isset($shipping_method['settings']['description']) ? $shipping_method['settings']['description'] : ''; ?>" />
            <?php
            if (!apply_filters( 'hide_vendor_shipping_classes', false )) { ?>
                <div class="mvx_shipping_classes">
                    <hr>
                    <h2><?php _e('Shipping Class Cost', 'multivendorx'); ?></h2> 
                    <div class="description mb-15"><?php _e('These costs can be optionally entered based on the shipping class set per product (This cost will be added with the shipping cost above).', 'multivendorx'); ?></div>
                    <?php

                    // $shipping_classes =  WC()->shipping->get_shipping_classes();
                    $shipping_classes =  get_vendor_shipping_classes();

                    if(empty($shipping_classes)) {
                        echo '<div class="no_shipping_classes">' . __("No Shipping Classes set by Admin", 'multivendorx') . '</div>';
                    } else {
                        foreach ($shipping_classes as $shipping_class ) {
                            ?>
                            <div class="form-group">
                                <label for="" class="control-label"><?php printf( __( 'Cost of Shipping Class: "%s"', 'multivendorx' ), $shipping_class->name ); ?></label>
                                <div class="col-md-9 col-sm-9">
                                    <input id="<?php echo $shipping_class->slug; ?>" class="form-control sc_vals" type="text" name="class_cost_<?php echo $shipping_class->term_id; ?>" value='<?php echo isset($shipping_method['settings']['class_cost_'.$shipping_class->term_id]) ? $shipping_method['settings']['class_cost_'.$shipping_class->term_id] : ''; ?>' placeholder="<?php esc_attr_e( 'N/A', 'multivendorx' ); ?>" data-shipping_class_id="<?php echo $shipping_class->term_id; ?>">
                                    <div class="description"><?php _e( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'multivendorx' ) . '<br/><br/>' . _e( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'multivendorx' ); ?></div>
                                </div>
                            </div>
                            <?php 
                        }
                        ?>
                        <div class="form-group">
                            <label for="" class="control-label"><?php _e( 'Calculation type', 'multivendorx' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <select id="calculation_type" class="form-control" name="calculation_type">
                                    <?php foreach( $calculation_type as $key => $value ) { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            <?php } ?>
        </div> 
        <?php
    }
    
    public function local_pickup_configure_form_fields( $shipping_method, $postdata ){
        $is_method_taxable_array = array(
            'none'      => __( 'None', 'multivendorx' ),
            'taxable'   => __( 'Taxable' , 'multivendorx' )
        );

        ?>
        <div id="wrapper-<?php echo $shipping_method['id'] ?>">
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Method Title', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="method_title_lp" class="form-control" type="text" name="title" value="<?php echo isset($shipping_method['title']) ? $shipping_method['title'] : ''; ?>" placeholder="<?php esc_attr_e( 'Enter method title', 'multivendorx' ); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label"><?php _e( 'Cost', 'multivendorx' ); ?></label>
                <div class="col-md-9 col-sm-9">
                    <input id="method_cost_lp" class="form-control" type="text" name="cost" value="<?php echo isset($shipping_method['settings']['cost']) ? $shipping_method['settings']['cost'] : ''; ?>" placeholder="<?php esc_attr_e( '0.00', 'multivendorx' ); ?>">
                </div>
            </div>
            <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                <div class="form-group">
                    <label for="" class="control-label"><?php _e( 'Tax Status', 'multivendorx' ); ?></label>
                    <div class="col-md-9 col-sm-9">
                        <select id="method_tax_status_lp" class="form-control" name="tax_status">
                            <?php foreach( $is_method_taxable_array as $key => $value ) { ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <input type="hidden" id="method_description_lp" name="description" value="<?php echo isset($shipping_method['settings']['description']) ? $shipping_method['settings']['description'] : ''; ?>" />
        </div> 
        <?php
    }

}
