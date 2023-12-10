<?php
/**
 * MVX Shipping Gateway for shipping by country
 *
 * Plugin Shipping Gateway
 *
 * @author    Multivendor X
 * @version   3.8
 */
if (!defined('ABSPATH')) {
    exit;
}

class MVX_Shipping_By_Country extends WC_Shipping_Method {
  /**
  * Constructor for your shipping class
  *
  * @access public
  *
  * @return void
  */
  public function __construct() {
    $this->id                 = 'mvx_product_shipping_by_country';
    $this->method_title       = __( 'MVX Shipping by Country', 'multivendorx' );
    $this->method_description = __( 'Enable vendors to set marketplace shipping per country', 'multivendorx' );

    $this->enabled      = $this->get_option( 'enabled' );
    $this->title        = $this->get_option( 'title' );
    $this->tax_status   = $this->get_option( 'tax_status' );
    
    if( !$this->title ) $this->title = __( 'Shipping Cost', 'multivendorx' );

    $this->init();
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
             'default'       => __( 'Regular Shipping', 'multivendorx' ),
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
  * calculate_shipping function.
  *
  * @access public
  *
  * @param mixed $package
  *
  * @return void
  */
  public function calculate_shipping( $package = array() ) {
   
    if( !apply_filters( 'mvx_is_allow_store_shipping', true ) ) return; 
    
    $products = $package['contents'];
    $destination_country = isset( $package['destination']['country'] ) ? $package['destination']['country'] : '';
    $destination_state = isset( $package['destination']['state'] ) ? $package['destination']['state'] : '';

    $amount = 0.0;

    if ( ! $this->is_method_enabled() ) {
       return;
    }
    $vendor_id = isset($package['vendor_id']) ? $package['vendor_id'] : '';
    $vendor = get_mvx_vendor($vendor_id);
    if ( !self::is_shipping_enabled_for_seller( $vendor_id ) ) {
       return;
    }

    if( apply_filters('hide_country_shiping_default_zero_cost', false ) || apply_filters('mvx_hide_country_shiping_default_zero_cost', false ) ) {
      $mvx_state_rates   = get_user_meta( $vendor_id, '_mvx_state_rates', true );
      $mvx_country_rates = get_user_meta( $vendor_id, '_mvx_country_rates', true );
      if ( isset( $mvx_state_rates[$destination_country] ) ) { 
        if( !array_key_exists( $destination_state, $mvx_state_rates[$destination_country] ) &&
            !array_key_exists( 'everywhere', $mvx_state_rates[$destination_country] ) ) {
          return;
        }
      } else {
        if( !array_key_exists( $destination_country, $mvx_country_rates ) && 
            !array_key_exists( 'everywhere', $mvx_country_rates ) ) {
          return;
        }
      }
    }

     if ( $products ) {
       $amount = $this->calculate_per_seller( $products, $destination_country, $destination_state );

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
       $mvx_shipping_by_country = get_user_meta( $vendor_id, '_mvx_shipping_by_country', true );
       //$enable_local_pickup = isset($mvx_shipping_by_country['_enable_local_pickup']) ? 'yes' : '';
       $local_pickup_cost = isset($mvx_shipping_by_country['_local_pickup_cost']) ? $mvx_shipping_by_country['_local_pickup_cost'] : '';
       if( $local_pickup_cost ) {
         $address = '';
          $address .= $vendor->address_1 . ' ';
          $address .= $vendor->address_2;
         $rate = array(
             'id'    => 'local_pickup:1',
             'label' => apply_filters( 'mvx_local_pickup_shipping_option_label', __('Pickup from Store', 'multivendorx')  . ' ('.$address.')', $vendor_id ),
             'cost'  => $local_pickup_cost,
             'taxes' => $tax_rate
         );
    
         // Register the rate
         $this->add_rate( $rate );
       }
       
       // Free Shipping Method Check
       if( $amount ) {
         $amount = $this->calculate_per_seller( $products, $destination_country, $destination_state, true );
         
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
       }
     }
  }


  /**
  * Check if shipping for this product is enabled
  *
  * @param  integet  $product_id
  *
  * @return boolean
  */
  public static function is_shipping_enabled_for_seller( $vendor_id ) {
    $vendor_shipping_options = get_user_meta($vendor_id, 'vendor_shipping_options', true) ? get_user_meta($vendor_id, 'vendor_shipping_options', true) : '';
    if ( mvx_is_module_active('country-shipping') && $vendor_shipping_options && $vendor_shipping_options == 'shipping_by_country') {
      return true;
    }
    return false;
  }

  /**
  * Check if seller has any shipping enable product in this order
  *
  * @since  3.8
  *
  * @param  array $products
  *
  * @return boolean
  */
  public function has_shipping_enabled_product( $products ) {
    foreach ( $products as $product ) {
        if ( !self::is_product_disable_shipping( $product['product_id'] ) ) {
            return true;
        }
    }

    return false;
  }


  /**
  * Get product shipping costs
  *
  * @param  integer $product_id
  *
  * @return array
  */
  public static function get_seller_country_shipping_costs( $vendor_id ) {
    $country_cost = get_user_meta( $vendor_id, '_mvx_country_rates', true );
    $country_cost = is_array( $country_cost ) ? $country_cost : array();

    return $country_cost;
  }


  /**
  * Calculate shipping per seller
  *
  * @param  array $products
  * @param  array $destination
  *
  * @return float
  */
  public function calculate_per_seller( $products, $destination_country, $destination_state, $is_consider_free_threshold = false  ) {
     $amount = 0.0;
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

         $mvx_shipping_by_country = get_user_meta( $vendor_id, '_mvx_shipping_by_country', true );
         
         $mvx_free_shipping_amount = isset($mvx_shipping_by_country['_free_shipping_amount']) ? $mvx_shipping_by_country['_free_shipping_amount'] : '';
         $mvx_free_shipping_amount = apply_filters( 'mvx_free_shipping_minimum_order_amount', $mvx_free_shipping_amount, $vendor_id );

         $default_shipping_price     = isset( $mvx_shipping_by_country['_mvx_shipping_type_price'] ) ? $mvx_shipping_by_country['_mvx_shipping_type_price'] : 0;
         $default_shipping_add_price = isset( $mvx_shipping_by_country['_mvx_additional_product'] ) ? $mvx_shipping_by_country['_mvx_additional_product'] : 0;

         $downloadable_count  = 0;
         $products_total_cost = 0;
         foreach ( $products as $product ) {
           
           if ( isset( $product['variation_id'] ) ) {
               $is_virtual      = get_post_meta( $product['variation_id'], '_virtual', true );
               $is_downloadable = get_post_meta( $product['variation_id'], '_downloadable', true );
           } else {
               $is_virtual      = get_post_meta( $product['product_id'], '_virtual', true );
               $is_downloadable = get_post_meta( $product['product_id'], '_downloadable', true );
           }

           if ( ( $is_virtual == 'yes' ) || ( $is_downloadable == 'yes' ) ) {
               $downloadable_count++;
               continue;
           }

           if ( get_post_meta( $product['product_id'], '_overwrite_shipping', true ) == 'yes' ) {
               $default_shipping_qty_price = get_post_meta( $product['product_id'], '_additional_qty', true );
               $price[ $vendor_id ]['addition_price'][] = get_post_meta( $product['product_id'], '_additional_price', true );
           } else {
               $default_shipping_qty_price =  isset( $mvx_shipping_by_country['_mvx_additional_qty'] ) ? $mvx_shipping_by_country['_mvx_additional_qty'] : 0;
               $price[ $vendor_id ]['addition_price'][] = 0;
           }

           $price[ $vendor_id ]['default'] = floatval( $default_shipping_price );

           if ( $product['quantity'] > 1 ) {
               $price[ $vendor_id ]['qty'][] = ( ( $product['quantity'] - 1 ) * floatval( $default_shipping_qty_price ) );
           } else {
               $price[ $vendor_id ]['qty'][] = 0;
           }
           
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
           return apply_filters( 'mvx_shipping_country_calculate_amount', 0, $price, $products, $destination_country, $destination_state );
         }

         if ( count( $products ) > 1 ) {
           $price[ $vendor_id ]['add_product'] =  floatval( $default_shipping_add_price ) * ( count( $products) - ( 1 + $downloadable_count ) );
         } else {
           $price[ $vendor_id ]['add_product'] = 0;
         }

         $mvx_country_rates = get_user_meta( $vendor_id, '_mvx_country_rates', true ) ? get_user_meta( $vendor_id, '_mvx_country_rates', true ) : array();
         $mvx_state_rates   = get_user_meta( $vendor_id, '_mvx_state_rates', true ) ? get_user_meta( $vendor_id, '_mvx_state_rates', true ) : array();
         
         if ( isset( $mvx_state_rates[$destination_country] ) ) {
           if ( $destination_state && array_key_exists( $destination_state, $mvx_state_rates[$destination_country] ) ) {
             if ( isset( $mvx_state_rates[$destination_country][$destination_state] ) ) {
               $price[$vendor_id]['state_rates'] = floatval( $mvx_state_rates[$destination_country][$destination_state] );
             } else {
               $price[$vendor_id]['state_rates'] = ( isset( $mvx_country_rates[$destination_country] ) ) ? floatval( $mvx_country_rates[$destination_country] ) : 0;
             }
           } elseif ( array_key_exists( 'everywhere', $mvx_state_rates[$destination_country] ) ) {
             $price[$vendor_id]['state_rates'] = ( isset( $mvx_state_rates[$destination_country]['everywhere'] ) ) ? floatval( $mvx_state_rates[$destination_country]['everywhere'] ) : 0;
           } elseif ( array_key_exists( $destination_country, $mvx_country_rates ) ) {
             $price[$vendor_id]['state_rates'] = ( isset( $mvx_country_rates[$destination_country] ) ) ? floatval( $mvx_country_rates[$destination_country] ) : 0;
           } else {
             $price[$vendor_id]['state_rates'] = 0;
           }
         } else {
           if ( !array_key_exists( $destination_country, $mvx_country_rates ) ) {
             $price[$vendor_id]['state_rates'] = isset( $mvx_country_rates['everywhere'] ) ? floatval( $mvx_country_rates['everywhere'] ) : 0;
           } else {
             $price[$vendor_id]['state_rates'] = ( isset( $mvx_country_rates[$destination_country] ) ) ? floatval( $mvx_country_rates[$destination_country] ) : 0;
           }
         }
       }
     }
     if ( !empty( $price ) ) {
       foreach ( $price as $s_id => $value ) {
         $amount = $amount + ( ( isset( $value['addition_price'] ) ? array_sum( $value['addition_price'] ) : 0 ) +  ( isset($value['default'] ) ? $value['default'] : 0 ) + ( isset( $value['qty'] ) ? array_sum( $value['qty'] ) : 0 ) + $value['add_product'] + ( isset( $value['state_rates'] ) ? $value['state_rates'] : 0 ) );
       }
     }
     
     return apply_filters( 'mvx_shipping_country_calculate_amount', $amount, $price, $products, $destination_country, $destination_state );
  }
}