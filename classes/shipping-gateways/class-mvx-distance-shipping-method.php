<?php
/**
 * MVX Shipping Gateway for shipping by Distance
 *
 * Plugin Shipping Gateway
 *
 * @author 		MultiVendorX
 * @version     3.7
 */
if (!defined('ABSPATH')) {
    exit;
}

class MVX_Shipping_By_Distance extends WC_Shipping_Method {
  /**
  * Constructor for your shipping class
  *
  * @access public
  *
  * @return void
  */
  public function __construct() {
    $this->id                 = 'mvx_product_shipping_by_distance';
    $this->method_title       = __( 'MVX Shipping by Distance', 'multivendorx' );
    $this->method_description = __( 'Enable vendors to set marketplace shipping by distance range', 'multivendorx' );

    $this->enabled      = $this->get_option( 'enabled' );
    $this->title        = $this->get_option( 'title' );
    $this->tax_status   = $this->get_option( 'tax_status' );
    
    if( !$this->title ) $this->title = __( 'Shipping Cost', 'multivendorx' );

    $this->init();

    add_filter( 'woocommerce_package_rates', array(&$this, 'mvx_hide_admin_shipping' ), 100, 2 );
  }


  /**
  * Init your settings
  *
  * @access public
  * @return void
  */
  function init() {
     // Load the settings API
     $this->init_form_fields();
     $this->init_settings();

     // Save settings in admin if you have any defined
     add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }
  
  public function calculate_shipping( $package = array() ) {
    global $MVX;
    
    if( !apply_filters( 'mvx_is_allow_store_shipping', true ) ) return; 

    $radius_unit   = 'km';

    $amount = 0.0;

    if ( ! $this->is_method_enabled() ) {
       return;
    }
    $vendor_id = isset($package['vendor_id']) ? $package['vendor_id'] : '';
    $vendor = get_mvx_vendor($vendor_id);
    if ( !self::is_shipping_enabled_for_seller( $vendor_id ) ) {
      return;
    }
    
    $products = $package['contents'];
    $mvx_user_location     = isset( $package['mvx_user_location'] ) ? $package['mvx_user_location'] : '';
    $mvx_user_location_lat = isset( $package['mvx_user_location_lat'] ) ? $package['mvx_user_location_lat'] : '';
    $mvx_user_location_lng = isset( $package['mvx_user_location_lng'] ) ? $package['mvx_user_location_lng'] : '';
    
    if ( !$mvx_user_location ) {
        return;
    }
    
    $store_location = get_user_meta($vendor_id, '_store_location', true) ? get_user_meta($vendor_id, '_store_location', true) : '';
    $store_lat = get_user_meta($vendor_id, '_store_lat', true) ? get_user_meta($vendor_id, '_store_lat', true) : 0;
    $store_lng = get_user_meta($vendor_id, '_store_lng', true) ? get_user_meta($vendor_id, '_store_lng', true) : 0;
     if (!$store_location) {
      return;
     }

    $distance = mvx_get_latlng_distance($mvx_user_location_lat, $mvx_user_location_lng, $store_lat, $store_lng, 'k');
    
    if( !$distance ) {
        return;
    }
        
    $mvx_shipping_by_distance = get_user_meta( $vendor_id, '_mvx_shipping_by_distance', true );
                 
    $mvx_free_shipping_amount = isset($mvx_shipping_by_distance['_free_shipping_amount']) ? $mvx_shipping_by_distance['_free_shipping_amount'] : '';
    $mvx_free_shipping_amount = apply_filters( 'mvx_free_shipping_minimum_order_amount', $mvx_free_shipping_amount, $vendor_id );
    
    $mvx_shipping_by_distance_rates = get_user_meta( $vendor_id, '_mvx_shipping_by_distance_rates', true );
    
    $max_distance = isset($mvx_shipping_by_distance['_max_distance']) ? $mvx_shipping_by_distance['_max_distance'] : '';
    
    if( $max_distance && ( $distance > $max_distance ) ) {
        wc_add_notice( __( 'Some cart item(s) are not deliverable to your location.', 'multivendorx' ), "error" );
        return;
    }
    
    $default_cost = isset($mvx_shipping_by_distance['_default_cost']) ? $mvx_shipping_by_distance['_default_cost'] : 0;
    
     if ( $products ) {
              $amount = $this->calculate_per_seller( $products, $distance, $default_cost, $mvx_shipping_by_distance_rates, $mvx_free_shipping_amount );
    
             $tax_rate  = ( $this->tax_status == 'none' ) ? false : '';
             $tax_rate  = apply_filters( 'mvx_is_apply_tax_on_shipping_rates', $tax_rate );
             
             if( !$amount ) {
                 $this->title = __('Free Shipping', 'multivendorx');
             }
    
             $rate = array(
                     'id'    => $this->id . ':1',
                     'label' => $this->title,
                     'cost'  => $amount,
                     'taxes' => $tax_rate
             );
             
             // Register the rate
             $this->add_rate( $rate );
             
             // Local Pickup Method Check
             $local_pickup_cost = isset($mvx_shipping_by_distance['_local_pickup_cost']) ? $mvx_shipping_by_distance['_local_pickup_cost'] : '';
             if( $local_pickup_cost ) {
                $address = '';
                $address .= $vendor->address_1 . ' ';
                $address .= $vendor->address_2;
                $rate = array(
                         'id'    => 'local_pickup:1',
                         'label' => apply_filters( 'mvx_local_pickup_shipping_option_label', __('Pickup from Store', 'multivendorx') . ' ('. $address .')', $vendor_id ),
                         'cost'  => $local_pickup_cost,
                         'taxes' => $tax_rate
                 );
        
                 // Register the rate
                 $this->add_rate( $rate );
             }
             
             // Free Shipping Method Check
             /*if( $amount ) {
                 $amount = $this->calculate_per_seller( $products, $distance, $default_cost, $mvx_shipping_by_distance_rates, $mvx_free_shipping_amount, true );
                 
                 if( !$amount ) {
                     $rate = array(
                             'id'    => 'free_shipping:1',
                             'label' => __('Free Shipping', 'multivendorx'),
                             'cost'  => $amount,
                             'taxes' => $tax_rate
                     );
            
                     // Register the rate
                     $this->add_rate( $rate );
                 }
             }*/
         }
  }
  
  /**
  * Checking is gateway enabled or not
  *
  * @return boolean [description]
  */
  public function is_method_enabled() {
     return $this->enabled == 'yes';
  }

  /**
  * Initialise Gateway Settings Form Fields
  *
  * @access public
  * @return void
  */
  function init_form_fields() {

     $this->form_fields = array(
      'enabled' => array(
          'title'         => __( 'Enable/Disable', 'multivendorx' ),
          'type'          => 'checkbox',
          'label'         => __( 'Enable Shipping', 'multivendorx' ),
          'default'       => 'yes'
      ),
      'title' => array(
          'title'         => __( 'Method Title', 'multivendorx' ),
          'type'          => 'text',
          'description'   => __( 'This controls the title which the user sees during checkout.', 'multivendorx' ),
          'default'       => __( 'Shipping Cost', 'multivendorx' ),
          'desc_tip'      => true,
      ),
      'tax_status' => array(
          'title'         => __( 'Tax Status', 'multivendorx' ),
          'type'          => 'select',
          'default'       => 'taxable',
          'options'       => array(
              'taxable'   => __( 'Taxable', 'multivendorx' ),
              'none'      => _x( 'None', 'Tax status', 'multivendorx' )
          ),
      ),

     );
  }
  
  /**
  * Check if shipping for this product is enabled
  *
  * @param  integet  $product_id
  *
  * @return boolean
  */
  public static function is_shipping_enabled_for_seller( $vendor_id = 0 ) {
    $vendor_shipping_options = get_user_meta($vendor_id, 'vendor_shipping_options', true) ? get_user_meta($vendor_id, 'vendor_shipping_options', true) : '';
    if ( mvx_is_module_active('distance-shipping') && $vendor_shipping_options && $vendor_shipping_options == 'distance_by_shipping') {
      return true;
    }
    return false;
  }
  
  /**
  * Calculate shipping per seller
  *
  * @param  array $products
  * @param  array $destination
  *
  * @return float
  */
  public function calculate_per_seller( $products = '', $total_distance = 0, $default_cost = 0, $mvx_shipping_by_distance_rates = array(), $mvx_free_shipping_amount = '', $is_consider_free_threshold = false ) {
    $amount = !empty( $default_cost ) ? $default_cost : 0;
    $price = array();

    $seller_products = array();

    foreach ( $products as $product ) {
      $vendor_id                     = get_post_field( 'post_author', $product['product_id'] );
      $seller_products[$vendor_id][] = $product;
    }

    if ( $seller_products ) {
      foreach ( $seller_products as $vendor_id => $products ) {

        if ( !self::is_shipping_enabled_for_seller( $vendor_id ) ) {
          continue;
        }

        $products_total_cost = 0;
        foreach ( $products as $product ) {
          $line_subtotal      = (float) $product['line_subtotal'];
          $line_total         = (float) $product['line_total'];
          $discount_total     = $line_subtotal - $line_total;
          $line_subtotal_tax  = (float) $product['line_subtotal_tax'];
          $line_total_tax     = (float) $product['line_tax'];
          $discount_tax_total = $line_subtotal_tax - $line_total_tax;

          if( apply_filters( 'mvx_free_shipping_threshold_consider_tax', true ) ) {
            $total = $line_subtotal + $line_subtotal_tax;
          } else {
            $total = $line_subtotal;
          }

          if ( WC()->cart->display_prices_including_tax() ) {
            $products_total_cost += round( $total - ( $discount_total + $discount_tax_total ), wc_get_price_decimals() );
          } else {
            $products_total_cost += round( $total - $discount_total, wc_get_price_decimals() );
          }
        }

        if( $is_consider_free_threshold && $mvx_free_shipping_amount && ( $mvx_free_shipping_amount <= $products_total_cost ) ) {
          return apply_filters( 'mvx_shipping_distance_calculate_amount', 0, $products, $total_distance, $default_cost, $mvx_shipping_by_distance_rates );
        }
      }
    }

    $matched_rule_distance = 0;
    $selected_rule['price'] = 0;

    foreach ( $mvx_shipping_by_distance_rates as $each_distance_rule ) {
      $rule_distance = $each_distance_rule['mvx_distance_unit'];
      $rule = isset($each_distance_rule['mvx_distance_rule']) ? $each_distance_rule['mvx_distance_rule']['value'] : '';
      $rule_price = isset( $each_distance_rule['mvx_distance_price'] ) ? $each_distance_rule['mvx_distance_price'] : 0;

      if( ( $rule == 'up_to' ) && ( (float)$total_distance <= (float)$rule_distance ) && ( !$matched_rule_distance || ( (float)$rule_distance <= (float)$matched_rule_distance ) ) ) {
        $matched_rule_distance = $rule_distance;
        $selected_rule = array( 'price' => $rule_price );
      } elseif( ( $rule == 'more_than' ) && ( (float)$total_distance > (float)$rule_distance ) && ( !$matched_rule_distance || ( (float)$rule_distance >= (float)$matched_rule_distance ) ) ) {
        $matched_rule_distance = $rule_distance;
        $selected_rule = array( 'price' => $rule_price );
      }
    }

    if( !empty( $selected_rule['price'] ) ) {
      $amount += $selected_rule['price'];
    } 

    return apply_filters( 'mvx_shipping_distance_calculate_amount', $amount, $products, $total_distance, $default_cost, $mvx_shipping_by_distance_rates );
  }

  /**
   * Hide Admin Shipping If vendor Shipping is available callback
   * @since 3.7
   * @param array $rates
   * @return array
   */
  public function mvx_hide_admin_shipping( $rates, $package ) {
    $free_shipping_available = false;
    $mvx_shipping = array();
    if( apply_filters( 'mvx_is_allow_hide_admin_shipping_for_vendor_shipping', true ) && isset( $package['vendor_id'] ) ) {
      if ($rates) {
        foreach ( $rates as $rate_id => $rate ) {
          if ( 'mvx_product_shipping_by_distance' === $rate->method_id ) {
            $id = explode(":", $rate_id, 2);
            $id = $id[0];
            if($id === 'free_shipping') {
              $free_shipping_available = apply_filters( 'mvx_is_allow_hide_other_shipping_if_free', true );
            }
            $mvx_shipping[ $rate_id ] = $rate;  
          }
        }
      }
      if($free_shipping_available) {
        foreach ( $mvx_shipping as $rate_id => $rate ) { 
          $id = explode(":", $rate_id, 2);
          $id = $id[0];
          if( !in_array( $id, array( 'free_shipping', 'local_pickup' ) ) ) {
            unset($mvx_shipping[$rate_id]);
          }
        }
      }

      if( apply_filters( 'mvx_is_allow_admin_shipping_if_no_vendor_shipping', false ) ) {
        $rates = array();
      }
    }
    return ! empty( $mvx_shipping ) ? $mvx_shipping : $rates;
  }
  
}