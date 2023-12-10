<?php
/**
 * Load MVX Elementor package class.
 *
 */

use Elementor\Controls_Manager;

class MVX_Elementor {
    /**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	public static $instance = null;
        
    /**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance_mvx() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init the plugin.
	 */
	public function init() {
		
		if ( ! $this->has_dependencies() ) {
			return;
		} 
		$this->on_plugins_loaded();
	}
        
	/**
	 * Check dependencies exist.
	 *
	 * @return boolean
	 */
	public function has_dependencies() {
		return class_exists( 'MVX' ) && WC_Dependencies_Product_Vendor::elementor_pro_active_check();
	}
        
	/**
	 * Setup plugin once all other plugins are loaded.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		$is_module_active = mvx_get_option('mvx_all_active_module_list', true);
        $is_active = $is_module_active && is_array($is_module_active) && in_array('elementor', $is_module_active) ? true : false;
        if ($is_active) {
			$mvx_elementor = new MVX_Elementor( __FILE__ );
			$GLOBALS['mvx_elementor'] = $mvx_elementor;

			add_action( 'elementor/init', array( &$this, 'mvx_elementor_init' ) );
		}
	}

	public function load_class($class_name = '') {
		global $MVX;
		if ('' != $class_name && '' != $MVX->token) {
			require_once ($MVX->plugin_path . 'packages/mvx-elementor/includes/class-' . esc_attr($MVX->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}

	public function mvx_elementor_init() {
		global $MVX;
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Traits/mvx-elementor-position-controls.php';

		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Abstracts/ModuleBase.php';
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Abstracts/DataTagBase.php';
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Abstracts/TagBase.php';
		
		// store page include
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Conditions/Store.php';
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Documents/StorePage.php';
		
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Controls/DynamicHidden.php';
		require_once $MVX->plugin_path . 'packages/mvx-elementor/includes/Controls/SortableList.php';
		
		add_action( 'elementor/elements/categories_registered', [ &$this, 'mvx_categories' ] );

		// Templates
		$this->load_class( 'templates' );
		new MVX_Elementor_Templates();

		// Module
		$this->load_class( 'module' );
		new MVX_Elementor_Module();
	}

 
    public function mvx_elementor() {
		return \Elementor\Plugin::instance();
	}

	public function is_edit_or_preview_mode() {
		global $mvx_elementor;
		$is_edit_mode = $mvx_elementor->mvx_elementor()->editor->is_edit_mode();

		$is_preview_mode = $mvx_elementor->mvx_elementor()->preview->is_preview_mode();
		if ( empty( $is_edit_mode ) && empty( $is_preview_mode ) ) {
			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['editor_post_id'] ) ) {
				$is_edit_mode = true;
			} else if ( ! empty( $_REQUEST['preview'] ) && $_REQUEST['preview'] && ! empty( $_REQUEST['theme_template_id'] ) ) {
				$is_preview_mode = true;
			}
		}

		if ( $is_edit_mode || $is_preview_mode ) {
			return true;
		}

		return false;
	}

	/**
	 * Default store data for widgets
	 *
	 * @param string $prop
	 *
	 * @return mixed
	 */
	public function get_mvx_store_data( $prop = null ) {
		$this->load_class( 'store-data' );
	  	$default_store_data = new MVX_Elementor_StoreData();

		return $default_store_data->get_data( $prop );
	}

	/**
	 * Social network name mapping to elementor icon names
	 *
	 * @return array
	 */
	public function get_social_networks_map() {
			$map = [
					'fb'        => 'fab fa-facebook',
					'gplus'     => 'fab fa-google-plus',
					'twitter'   => 'fab fa-twitter',
					'linkedin'  => 'fab fa-linkedin',
					'youtube'   => 'fab fa-youtube',
					'instagram' => 'fab fa-instagram',
					'pinterest'	=> 'fab fa-pinterest'
			];

			return apply_filters( 'mvx_elementor_social_network_map', $map );
	}
	
	/**
	 * Register Elementor "MVX Marketplace" category
	 */
	function mvx_categories( $elements_manager ) {
		$elements_manager->add_category(
			'mvx-store-elements-single',
			[
				'title' => __( 'MultivendorX', 'multivendorx' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

}

MVX_Elementor::instance_mvx()->init();
