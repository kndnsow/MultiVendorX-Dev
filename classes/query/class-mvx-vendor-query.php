<?php
/**
 * MVX_Vendor_Query class
 *
 * @package MultiVendorX
 * @since 3.5.0
 */

class MVX_Vendor_Query {
    /**
	 * Query vars, after parsing
	 *
	 * @since 3.5.0
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * User in var, after parsing
	 *
	 * @since 3.5.0
	 * @var array
	 */
	public $user_in = array();

	/**
	 * User not in var, after parsing
	 *
	 * @since 3.5.0
	 * @var array
	 */
	public $user_not_in = array();

	/**
	 * List of found vendors ids
	 *
	 * @since 3.5.0
	 * @var array
	 */
	private $results;

	/**
	 * Total number of found vendors for the current query
	 *
	 * @since 3.5.0
	 * @var int
	 */
	private $total_vendors = 0;

	/**
	 * Vendor id
	 *
	 * @since 3.5.0
	 * @var array
	 */
	private $vendor_id = 0;
    
	/**
	 * constructor.
	 *
	 * @param null|string|array $query Optional. The query variables.
	 */
	public function __construct( $query = null ) {
		if ( ! empty( $query ) ) {
			$this->prepare_query( $query );
			$this->query();
			//add_action( 'pre_user_query', array( $this, 'test_user_query' ) );
		}
    }
    
    /**
	 * Fills in missing query variables with default values.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args Query vars, as passed to `WP_User_Query`.
	 * @return array Complete query variables with undefined ones filled in with defaults.
	 */
	public static function fill_query_vars( $args ) {
		$defaults = array(
			'role'                  => 'dc_vendor',
			'offset'              	=> '',
			'number'              	=> '',
			'paged'               	=> '',
			'orderby' 				=> 'registered', 
			'order' 				=> 'ASC',
			'fields'                => 'all',
			'meta_key'				=> '',
			'meta_value'			=> '',
			'sort_by'				=> 'date',
			'sort_category'			=> '',
			'shipping_zone'			=> '',
			'shipping_location'		=> '',
			'shipping_method'		=> '',
			'distance_query'        => array(),
			'meta_query'			=> array(),
			'include'             	=> array(),
			'exclude'             	=> array(),
		);

		return wp_parse_args( $args, $defaults );
    }
    
    /**
	 * Prepare the query variables.
	 *
	 * @since 3.5.0
     */
    public function prepare_query( $query = array() ) {
		global $wpdb;

		if ( empty( $this->query_vars ) || ! empty( $query ) ) {
			$this->query_limit = null;
			$this->query_vars  = $this->fill_query_vars( $query );
		}

		/**
		 * Fires before the MVX_Vendor_Query has been parsed.
		 *
		 * The passed WP_User_Query object contains the query variables
		 *
		 * @since 3.5.0
		 *
		 * @param WP_User_Query $this The current WP_User_Query instance,
		 *                            passed by reference.
		 */
		do_action( 'mvx_pre_get_vendors', $this );

		// Ensure that query vars are filled after 'pre_get_users'.
		$this->query_vars = $this->fill_query_vars( $this->query_vars );
		
		$this->filter_vendors_by_exclude();

		if( isset( $this->query_vars['sort_by'] ) ) {
			$this->filter_vendors_sort_by();
		}

		if( ( isset( $this->query_vars['shipping_zone'] ) && !empty( $this->query_vars['shipping_zone'] ) ) || ( isset( $this->query_vars['shipping_method'] ) && !empty( $this->query_vars['shipping_method'] ) ) ) {
			$this->filter_vendors_by_shipping();
		}

		if( isset( $this->query_vars['shipping_location'] ) && !empty( $this->query_vars['shipping_location'] ) ) {
			$this->filter_vendors_by_shipping_location();
		}

        if ( isset( $this->query_vars['distance_query'] ) ) {
            $default = array(
				'lat'		=> 0,
				'lon'		=> 0,
                'unit'      => '',
                'distance'  => '',
            );
			$distance_query = wp_parse_args( $this->query_vars['distance_query'], $default );
			$this->filter_vendors_by_distance( $distance_query );
		}

		
		
		$this->query_vars['include'] = $this->user_in;
		$this->query_vars['exclude'] = $this->user_not_in;
    }

    /**
	 * Execute the query, with the current variables.
	 *
	 * @since 3.5.0
	 */
	public function query() {
		/**
		 * Fires before the WP_User_Query has been parsed.
		 *
		 * The passed WP_User_Query object contains the query variables
		 *
		 * @since 3.5.0
		 *
		 * @param WP_User_Query $this The current WP_User_Query instance,
		 *                            passed by reference.
		 */
		$query = apply_filters( 'pre_get_mvx_vendors_query', $this->query_vars, $this );
		$user_query = new WP_User_Query( $query );
		if( $user_query->get_results() ) {
			foreach ( $user_query->get_results() as $key => $user ) {
				$this->vendor_id = $user->ID;
				$vendor_data = new stdClass();
				$vendor_data->ID = $this->vendor_id;
				$vendor_data->term_id = self::get_meta( '_vendor_term_id' );
				$vendor_data->store = self::get_store_data();
				$vendor_data->data = $user->data;
				$vendor_data->caps = $user->caps;
				$vendor_data->cap_key = $user->cap_key;
				$vendor_data->roles = $user->roles;
				$vendor_data->allcaps = $user->allcaps;
				$vendor_data->filter = $user->filter;
				$this->results[$key] = $vendor_data;
			}
		}
    }

    /**
	 * Return the list of vendors.
	 *
	 * @since 3.5.0
	 *
	 * @return array Array of results.
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of vendors for the current query.
	 *
	 * @since 3.5.0
	 *
	 * @return int Number of total vendors.
	 */
	public function get_total() {
		$this->total_vendors = ( $this->results ) ? count( $this->results ) : 0;
	}

	/**
	 * Return vendor meta value.
	 *
	 * @since 3.5.0
	 *
	 * @return array/string result.
	 */
	private function get_meta( $key ) {
		return ( $key ) ? get_user_meta( $this->vendor_id, $key, true ) : false;
	}

	/**
	 * Return vendor store data value.
	 *
	 * @since 3.5.0
	 *
	 * @return object result.
	 */
	private function get_store_data() {
		$store_data = new stdClass();
		$store_data->name = self::get_meta( '_vendor_page_title' );
		$store_data->slug = self::get_meta( '_vendor_page_slug' );
		$store_data->description = self::get_meta( '_vendor_description' );
		$store_data->image_id = self::get_meta( '_vendor_image' );
		$store_data->banner_id = self::get_meta( '_vendor_banner' );
		$store_data->profile_image_id = self::get_meta( '_vendor_profile_image' );
		$store_data->store_address = array(
			'address_1'		=> self::get_meta( '_vendor_address_1' ),
			'address_2'		=> self::get_meta( '_vendor_address_2' ),
			'city'			=> self::get_meta( '_vendor_city' ),
			'state'			=> self::get_meta( '_vendor_state' ),
			'state_code'	=> self::get_meta( '_vendor_state_code' ),
			'country'		=> self::get_meta( '_vendor_country' ),
			'country_code'	=> self::get_meta( '_vendor_country_code' ),
			'postcode'		=> self::get_meta( '_vendor_postcode' ),
			'phone'			=> self::get_meta( '_vendor_phone' ),
		);
		$store_data->store_location = array(
			'latitude' => self::get_meta( '_store_lat' ),
			'longitude' => self::get_meta( '_store_lng' ),
			'address' => self::get_meta( '_store_location' ),
			'address_data' => self::get_meta( '_store_address_components' ),
		);
		$store_data->gmt_offset = self::get_meta( 'gmt_offset' );
		$store_data->payment_mode = self::get_meta( '_vendor_payment_mode' );
		$store_data->timezone_string = self::get_meta( 'timezone_string' );
		$store_data->shipping_class_id = self::get_meta( 'shipping_class_id' );
		return apply_filters( 'mvx_vendor_query_result_stores', $store_data, $this->vendor_id );
	}
	
	/**
	 * Filters vendor by distance.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_by_distance( $distance_query ) {
		global $wpdb;
		extract( $distance_query );
		$distance = ( round( $distance, 2 ) );
		$multiplier = ( strtoupper( $unit ) === 'K' ) ? 1.609344 : 1;
		$search_results = $wpdb->get_results(
			// phpcs:disable
			$wpdb->prepare( "SELECT DISTINCT users.ID, ((ACOS(SIN(%d * PI() / 180) * SIN(latitude.meta_value * PI() / 180) + COS(%d * PI() / 180) * COS(latitude.meta_value * PI() / 180) * COS((%d - longitude.meta_value) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * %d) AS distance FROM {$wpdb->users} users 
			INNER JOIN {$wpdb->usermeta} latitude on latitude.user_id = users.ID and latitude.meta_key = '_store_lat' 
			INNER JOIN {$wpdb->usermeta} longitude on longitude.user_id = users.ID and longitude.meta_key = '_store_lng' 
			HAVING distance <= %f ", $lat, $lat, $lon, $multiplier, $distance)
		);
		
		if( $search_results ) {
			$this->user_in = array_merge( wp_list_pluck( $search_results, 'ID' ), $this->user_in );
		}
	}

	/**
	 * Filters vendors by shipping.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_by_shipping() { 
		global $wpdb;
		$zone_id = $this->query_vars['shipping_zone'];
		$method_id = $this->query_vars['shipping_method'];
		
		$where = array();
		if( $zone_id ) {
			$where[] = "zone_id = $zone_id";
		}
		if( $method_id ) {
			$where[] = "method_id = '".$method_id."'";
		}
		// Enabled only
		$where[] = "is_enabled = 1";
		$where_sql = implode( " AND ", $where );
		$search_results = $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:disable
			"SELECT vendor_id 
			FROM {$wpdb->prefix}mvx_shipping_zone_methods
			WHERE %s", $where_sql
			)
		);
		
		if( $search_results ) {
			$vendors = array_unique( wp_list_pluck( $search_results, 'vendor_id' ) );
			$this->user_in = array_merge( $vendors, $this->user_in );
		}
	}

	/**
	 * Filters vendors by shipping location.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_by_shipping_location() { 
		global $wpdb;
		$zone_id = isset( $this->query_vars['shipping_zone'] ) ? $this->query_vars['shipping_zone'] : false;
		$shipping_location = $this->query_vars['shipping_location'];
		
		$where = array();
		if( $zone_id ) {
			$where[] = "zone_id = $zone_id";
		}
		if( $shipping_location ) {
			$where[] = "location_code = '".$shipping_location."'";
		}
		
		$where_sql = implode( " AND ", $where );
		$search_results = $wpdb->get_results(
			// phpcs:disable
			"SELECT vendor_id 
			FROM {$wpdb->prefix}mvx_shipping_zone_locations
			WHERE " . wp_unslash(esc_sql( $where_sql ) ) . ""
		);
		
		if( $search_results ) {
			$vendors = array_unique( wp_list_pluck( $search_results, 'vendor_id' ) );
			$this->user_in = array_merge( $vendors, $this->user_in );
		}
	}

	/**
	 * Filters vendor by distance.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_sort_by() {
		switch ( $this->query_vars['sort_by'] ) {
			case 'name':
				$this->query_vars['meta_key'] = '_vendor_page_title';
				$this->query_vars['orderby'] = 'meta_value';
				break;
			case 'category':
				$this->filter_vendors_by_product_category();
				break;

			default:
				$this->query_vars['orderby'] = 'registered';
				$this->query_vars['order'] = 'ASC';
				break;
		}
	}

	/**
	 * Filters vendor by exclude.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_by_exclude() {
		$this->user_not_in = array_merge( wp_list_pluck(mvx_get_all_blocked_vendors(), 'id'), $this->user_not_in );
	}

	/**
	 * Filters vendor by product category.
	 *
	 * @since 3.5.0
	 *
	 * @return array Query vars.
	 */
	public function filter_vendors_by_product_category() {
		global $wpdb;
		$vendor_ids = get_mvx_vendors( 
			array( 'exclude' => wp_list_pluck(mvx_get_all_blocked_vendors(), 'id') ),
			'ids'
		);
		$vendor_ids = implode( ',', $vendor_ids );
		$category_id = isset( $this->query_vars['sort_category'] ) ? $this->query_vars['sort_category'] : 0;
		
		$search_results = $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:disable
			"SELECT posts.post_author 
			FROM {$wpdb->posts} posts
			LEFT JOIN {$wpdb->prefix}term_relationships t_rel ON (posts.ID = t_rel.object_id)
			LEFT JOIN {$wpdb->prefix}term_taxonomy t_tax ON (t_rel.term_taxonomy_id = t_tax.term_taxonomy_id)
			WHERE t_tax.term_id IN ( %s) 
			AND posts.post_author IN ( %s )", $category_id, $vendor_ids
			)
		);
		if( $search_results ) {
			$vendors = array_unique( wp_list_pluck( $search_results, 'post_author' ) );
			$this->user_in = array_merge( $vendors, $this->user_in );
		}
	}

}