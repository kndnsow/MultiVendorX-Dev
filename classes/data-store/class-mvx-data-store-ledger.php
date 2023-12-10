<?php
/**
 * Class MVX_Ledger_Data_Store file.
 *
 * @package MultiVendorX\Classes\Data Stores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MVX Ledger Data Store.
 *
 * @version  3.4.0
 */
class MVX_Ledger_Data_Store {
    
    public function __construct( ) {
        
    }

    /**
     * Method to create a row data.
     *
     * @since 3.4.0
     * @param $data array .
     */
    public function create( $data = array() ) {
        global $wpdb;
        $data = apply_filters( 'mvx_ledger_data_store_before_create', $data );
        $wpdb->insert( $wpdb->prefix . 'mvx_vendor_ledger', $data );
        return $wpdb->insert_id;
    }

    /**
     * Update data in the database.
     *
     * @since 3.4.0
     * @param $id integer
     * @param $data array
     */
    public function update( $id, $data ) {
        global $wpdb;
        if ( $id ) {
            $wpdb->update( $wpdb->prefix . 'mvx_vendor_ledger', $data, array( 'id' => $id ) );
        }
        do_action( 'mvx_ledger_data_store_after_update', $id, $data );
    }
    
    /**
     * get data in the database.
     *
     * @since 3.4.0
     * @param $args array
     * @param $where string
     */
    public function get_ledger( $args = array(), $where = '', $requestData = array() ) {
        global $wpdb;
        $where_sql = implode(' AND ', array_map(
                        function ($v, $k) {
                    return sprintf("%s = '%s'", $k, $v);
                }, $args, array_keys($args)
        ));
        if( $where ){
            $where_sql .= ' AND '. $where;
        } 
        if( $requestData ){ // datatable requests
            if( isset( $requestData['from_date'] ) && isset( $requestData['to_date'] ) ) {
                $from = $requestData['from_date'].' 00:00:00';
                $to = $requestData['to_date'].' 23:59:59';
                $where_sql .= " AND created BETWEEN '{$from}' AND '{$to}' ";
            }
        }
        $where_sql = apply_filters('mvx_data_store_ledger_get_ledger_where_sql', $where_sql, $args, $where, $requestData );
        $order_sql = apply_filters('mvx_data_store_ledger_get_ledger_order_sql', 'ORDER BY created DESC', $args, $where, $requestData );
        $get_ledger_sql = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mvx_vendor_ledger WHERE " . wp_unslash(esc_sql($where_sql) ) . wp_unslash(esc_sql($order_sql) ) );
        return apply_filters('mvx_data_store_ledger_get_ledger_sql_query', $get_ledger_sql, $args, $where, $requestData );
    }
	
}
