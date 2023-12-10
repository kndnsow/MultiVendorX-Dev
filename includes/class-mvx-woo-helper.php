<?php

/**
 * MVX_Woo_Helper setup
 * this class contains all the WooCommerce helper methods
 * @package MultiVendorX
 * @since    3.2.3
 */
defined( 'ABSPATH' ) || exit;

final class MVX_Woo_Helper {

    /**
     * The single instance of the class.
     *
     * @var MVX_Woo_Helper
     * @since 3.2.3
     */
    protected static $_instance = null;

    /**
     * Main MVX Instance.
     *
     * Ensures only one instance of MVX is loaded or can be loaded.
     *
     * @since 3.2.3
     * @static
     * @see MVX
     *
     * @return object The MVX object.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Primary class constructor.
     *
     * @since 3.2.3
     * @access public
     */
    public function __construct() {
        add_action( 'mvx_init', array( &$this, 'init' ) );
    }

    /**
     * Cloning is forbidden.
     *
     * @since 3.0.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'multivendorx' ), '3.2.3' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 3.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'multivendorx' ), '3.2.3' );
    }

    /**
     * Save product variations data.
     *
     * @param int     $post_id
     * @param WP_Post $post
     */
    public function save_product_variations( $post_id, $data ) {
        $errors = array();
        if ( isset( $data['variable_post_id'] ) ) {
            $parent = wc_get_product( $post_id );
            $parent->set_default_attributes( $this->prepare_set_attributes( $parent->get_attributes(), 'default_attribute_', $data ) );
            $parent->save();

            $max_loop = max( array_keys( $data['variable_post_id'] ) );
            $data_store = $parent->get_data_store();
            $data_store->sort_all_product_variations( $parent->get_id() );

            for ( $i = 0; $i <= $max_loop; $i ++ ) {

                if ( ! isset( $data['variable_post_id'][$i] ) ) {
                    continue;
                }
                $variation_id = absint( $data['variable_post_id'][$i] );
                $variation = new WC_Product_Variation( $variation_id );
                $stock = null;

                // Handle stock changes.
                if ( isset( $data['variable_stock'], $data['variable_stock'][$i] ) ) {
                    if ( isset( $data['variable_original_stock'], $data['variable_original_stock'][$i] ) && wc_stock_amount( $variation->get_stock_quantity( 'edit' ) ) !== wc_stock_amount( $data['variable_original_stock'][$i] ) ) {
                        /* translators: 1: product ID 2: quantity in stock */
                        $errors[] = sprintf( __( 'The stock has not been updated because the value has changed since editing. Product %1$d has %2$d units in stock.', 'multivendorx' ), $variation->get_id(), $variation->get_stock_quantity( 'edit' ) );
                    } else {
                        $stock = wc_stock_amount( $data['variable_stock'][$i] );
                    }
                }

                $error = $variation->set_props(
                    array(
                        'status'            => isset( $data['variable_enabled'][$i] ) ? 'publish' : 'private',
                        'menu_order'        => wc_clean( $data['variation_menu_order'][$i] ),
                        'regular_price'     => wc_clean( $data['variable_regular_price'][$i] ),
                        'sale_price'        => wc_clean( $data['variable_sale_price'][$i] ),
                        'virtual'           => isset( $data['variable_is_virtual'][$i] ),
                        'downloadable'      => isset( $data['variable_is_downloadable'][$i] ),
                        'date_on_sale_from' => wc_clean( $data['variable_sale_price_dates_from'][$i] ),
                        'date_on_sale_to'   => wc_clean( $data['variable_sale_price_dates_to'][$i] ),
                        'description'       => wp_kses_post( $data['variable_description'][$i] ),
                        'download_limit'    => wc_clean( $data['variable_download_limit'][$i] ),
                        'download_expiry'   => wc_clean( $data['variable_download_expiry'][$i] ),
                        'downloads'         => $this->prepare_downloads(
                            isset( $data['_wc_variation_file_names'][$variation_id] ) ? $data['_wc_variation_file_names'][$variation_id] : array(), isset( $data['_wc_variation_file_urls'][$variation_id] ) ? $data['_wc_variation_file_urls'][$variation_id] : array(), isset( $data['_wc_variation_file_hashes'][$variation_id] ) ? $data['_wc_variation_file_hashes'][$variation_id] : array()
                        ),
                        'manage_stock'      => isset( $data['variable_manage_stock'][$i] ),
                        'stock_quantity'    => $stock,
                        'backorders'        => isset( $data['variable_backorders'], $data['variable_backorders'][$i] ) ? wc_clean( $data['variable_backorders'][$i] ) : null,
                        'stock_status'      => wc_clean( $data['variable_stock_status'][$i] ),
                        'image_id'          => wc_clean( $data['upload_image_id'][$i] ),
                        'attributes'        => $this->prepare_set_attributes( $parent->get_attributes(), 'attribute_', $data, $i ),
                        'sku'               => isset( $data['variable_sku'][$i] ) ? wc_clean( $data['variable_sku'][$i] ) : '',
                        'weight'            => isset( $data['variable_weight'][$i] ) ? wc_clean( $data['variable_weight'][$i] ) : '',
                        'length'            => isset( $data['variable_length'][$i] ) ? wc_clean( $data['variable_length'][$i] ) : '',
                        'width'             => isset( $data['variable_width'][$i] ) ? wc_clean( $data['variable_width'][$i] ) : '',
                        'height'            => isset( $data['variable_height'][$i] ) ? wc_clean( $data['variable_height'][$i] ) : '',
                        'shipping_class_id' => isset($data['variable_shipping_class'][$i]) ? wc_clean( $data['variable_shipping_class'][$i] ) : '',
                        'tax_class'         => isset( $data['variable_tax_class'][$i] ) ? wc_clean( $data['variable_tax_class'][$i] ) : null,
                    )
                );

                if ( is_wp_error( $error ) ) {
                    $errors[] = $error->get_error_message();
                }

                $variation->save();

                do_action( 'woocommerce_save_product_variation', $variation_id, $i );
            }
        }
        return $errors;
    }

    /**
     * Prepare product attributes for save.
     *
     * @param array $data
     *
     * @return array
     */
    public function prepare_attributes( $attributes ) {
        // Attributes
        $product_attributes = array();
        if ( ! empty( $attributes ) ) {
            foreach ( $attributes as $attribute ) {
                if ( ! empty( $attribute['name'] ) ) {
                    $attribute_id = 0;
                    $attribute_name = wc_clean( $attribute['name'] );
                    $attribute_position = wc_clean( $attribute['position'] );
                    $attribute_visible = isset( $attribute['visibility'] );
                    $attribute_variation = isset( $attribute['variation'] );

                    if ( isset( $attribute['tax_name'] ) ) {
                        $attribute_id = wc_attribute_taxonomy_id_by_name( $attribute['tax_name'] );
                    }

                    if( isset($attribute['value']) ) {
                        $options = is_array( $attribute['value'] ) ? $attribute['value'] : stripslashes( $attribute['value'] );
                    } else {
                        $options ='';
                    }

                    if ( is_array( $options ) ) {
                        // Term ids sent as array.
                        $options = wp_parse_id_list( $options );
                    } else {
                        // Terms or text sent in textarea.
                        $options = 0 < $attribute_id ? wc_sanitize_textarea( wc_sanitize_term_text_based( $options ) ) : wc_sanitize_textarea( $options );
                        $options = wc_get_text_attributes( $options );
                    }

                    if ( empty( $options ) ) {
                        continue;
                    }

                    $attribute = new WC_Product_Attribute();
                    $attribute->set_id( $attribute_id );
                    $attribute->set_name( $attribute_name );
                    $attribute->set_options( $options );
                    $attribute->set_position( $attribute_position );
                    $attribute->set_visible( $attribute_visible );
                    $attribute->set_variation( $attribute_variation );
                    $product_attributes[] = $attribute;
                }
            }
        }
        return $product_attributes;
    }

    /**
     * Prepare product attributes for a specific variation or defaults.
     *
     * @param  array  $all_attributes
     * @param  string $key_prefix
     * @param  int    $index
     * @return array
     */
    public function prepare_set_attributes( $all_attributes, $key_prefix = 'attribute_', $data = '', $index = null ) {
        $attributes = array();

        if ( $all_attributes ) {
            foreach ( $all_attributes as $attribute ) {
                if ( $attribute->get_variation() ) {
                    $attribute_key = sanitize_title( $attribute->get_name() );

                    if ( ! is_null( $index ) ) {
                        $value = isset( $data[$key_prefix . $attribute_key][$index] ) ? wp_unslash( $data[$key_prefix . $attribute_key][$index] ) : '';
                    } else {
                        $value = isset( $data[$key_prefix . $attribute_key] ) ? wp_unslash( $data[$key_prefix . $attribute_key] ) : '';
                    }

                    if ( $attribute->is_taxonomy() ) {
                        // Don't use wc_clean as it destroys sanitized characters.
                        $value = sanitize_title( $value );
                    } else {
                        $value = html_entity_decode( wc_clean( $value ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
                    }

                    $attributes[$attribute_key] = $value;
                }
            }
        }

        return $attributes;
    }

    /**
     * Prepare downloads for save.
     *
     * @param array $file_names
     * @param array $file_urls
     * @param array $file_hashes
     *
     * @return array
     */
    private function prepare_downloads( $file_names, $file_urls, $file_hashes ) {
        $downloads = array();

        if ( ! empty( $file_urls ) ) {
            $file_url_size = sizeof( $file_urls );

            for ( $i = 0; $i < $file_url_size; $i ++ ) {
                if ( ! empty( $file_urls[$i] ) ) {
                    $downloads[] = array(
                        'name'        => wc_clean( $file_names[$i] ),
                        'file'        => wp_unslash( trim( $file_urls[$i] ) ),
                        'download_id' => wc_clean( $file_hashes[$i] ),
                    );
                }
            }
        }
        return $downloads;
    }

}

if ( ! function_exists( 'mvx_woo' ) ) {

    function mvx_woo() {
        return MVX_Woo_Helper::instance();
    }

}