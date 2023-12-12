<?php
/**
 * MVX Marketplace Vendor Store Setup Class
 * 
 * @since  3.6
 * @author 		MultiVendorX
 */
if (!defined('ABSPATH')) {
    exit;
}

class MVX_Migrator {

	/** @var string Currenct Step */
	private $step = '';

	/** @var array Steps for the setup wizard */
	private $steps = array();

	public $is_marketplace;
	public $mvx_migration_marketplace;

	public function __construct() {
		$this->is_marketplace = $this->mvx_is_marketplace();
		if( $this->is_marketplace ) {
			if ( is_admin() ) {
				$current_page = filter_input( INPUT_GET, 'page' );
				if ( $current_page && $current_page == 'mvx-migrator' ) {
					add_action( 'init', array( &$this, 'init_migration' ) );
					add_action( 'admin_menu', array( $this, 'mvx_admin_menus' ) );
					add_action( 'admin_init', array( $this, 'mvx_migration' ) );
				}
			}
			// set cron schedule for cron job
			add_filter('cron_schedules', array($this, 'add_mvx_migrate_corn_schedule'));
			if (!wp_next_scheduled('migrate_multivendor_order_table') ) {
				wp_schedule_event(time(), 'inevery_1minute', 'migrate_multivendor_order_table');
			}
			add_action('migrate_multivendor_order_table', array($this, 'migrate_multivendor_order_table'));	
		}
	}

	public function init_migration() {
		if( $this->is_marketplace ) {
			$this->load_class( $this->is_marketplace );
			if( $this->is_marketplace == 'wcvendors' ) $this->mvx_migration_marketplace = new MVX_WCVendors();
			elseif( $this->is_marketplace == 'wcfmmarketplace' ) $this->mvx_migration_marketplace = new MVX_WCfmMarketplace();
			elseif( $this->is_marketplace == 'wcpvendors' ) $this->mvx_migration_marketplace = new MVX_WCPVendors();
			elseif( $this->is_marketplace == 'dokan' ) $this->mvx_migration_marketplace = new MVX_Dokan();
		}
	}
	// Get multivendor plugin name
	public function get_plugin_name_by_class() {
		$this->init_migration();
		if ($this->is_marketplace) {
			$marketplace_title = __( 'Multivendor X', 'multivendorx' );
			if ($this->is_marketplace == 'wcfmmarketplace') {
				$marketplace_title = __( 'WCFM Marketplace', 'multivendorx' );
			} elseif ($this->is_marketplace == 'wcvendors') {
				$marketplace_title = __( 'WCVendor Marketplace', 'multivendorx' );
			} elseif ($this->is_marketplace == 'dokan') {
				$marketplace_title = __( 'Dokan Marketplace', 'multivendorx' );
			}
		}
		return $marketplace_title;
	}

	public function load_class($class_name = '') {
		global $MVX;
        if ('' != $class_name) {
            require_once ($MVX->plugin_path . 'classes/migration/class-' . esc_attr($MVX->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
	}

	function add_mvx_migrate_corn_schedule($schedules) {
	    $schedules['inevery_1minute'] = array(
	            'interval' => 5*60, // in seconds
	            'display'  => __( 'Every 5 minute', 'multivendorx' )
	    );
	    return $schedules;
	}

	function migrate_multivendor_order_table() {
		$this->init_migration();
		$this->mvx_migration_marketplace->store_order_migrate();
	}

	/**
	 * Add admin menus/screens.
	 */
	public function mvx_admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'mvx-migrator', '' );
	}
	
	/**
	 * Show the setup wizard.
	 */
	public function mvx_migration() {
		if ( filter_input(INPUT_GET, 'page') != 'mvx-migrator') {
			return;
		}
		
		$default_steps = array(
			'introduction-migration' => array(
				'name' => __('Introduction', 'multivendorx' ),
				'view' => array($this, 'mvx_migration_introduction'),
				'handler' => '',
			),
			'store-process' => array(
				'name' => __('Processing', 'multivendorx'),
				'view' => array($this, 'mvx_migration_store_process'),
				'handler' => ''
			),
			'next_steps' => array(
				'name' => __('Complete!', 'multivendorx'),
				'view' => array($this, 'mvx_migration_complete'),
				'handler' => '',
			),
		);
		
		$this->steps = apply_filters('mvx_migration_steps', $default_steps);
		$current_step = filter_input(INPUT_GET, 'step');
		$this->step = $current_step ? sanitize_key($current_step) : current(array_keys($this->steps));
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script('jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array('jquery'), '2.70', true);
		
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION);
		wp_enqueue_style( 'wc-setup', WC()->plugin_url() . '/assets/css/wc-setup.css', array('dashicons', 'install'), WC_VERSION);

		if (!empty($_POST['save_step']) && isset($this->steps[$this->step]['handler'])) {
				call_user_func($this->steps[$this->step]['handler'], $this);
		}

		ob_start();
		$this->mvx_migration_header();
		$this->mvx_migration_steps();
		$this->mvx_migration_content();
		$this->mvx_migration_footer();
		exit();
	}

	/**
	 * Get the URL for the next step's screen.
	 * @param string step   slug (default: current step)
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 * @since 1.0.0
	 */
	public function get_next_step_link($step = '') {
		if (!$step) {
			$step = $this->step;
		}

		$keys = array_keys($this->steps);
		if (end($keys) === $step) {
			return admin_url();
		}

		$step_index = array_search($step, $keys);
		if (false === $step_index) {
			return '';
		}

		return add_query_arg('step', $keys[$step_index + 1]);
	}

	/**
	 * Setup Wizard Header.
	 */
	public function mvx_migration_header() {
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?php esc_html_e('MVX Marketplace Migration', 'multivendorx'); ?></title>
				<?php wp_print_scripts('jquery-blockui'); ?>
				<?php
				?>
				<?php do_action('admin_print_scripts'); ?>
				<?php do_action('admin_print_styles'); ?>
				<style type="text/css">
					.wc-setup-steps {
						justify-content: center;
					}
				</style>
			</head>
			<body class="wc-setup wp-core-ui">

			 <h1 id="wc-logo"><a target="_blank" href="<?php echo site_url(); ?>"><img src="<?php echo trailingslashit(plugins_url('multivendorx')); ?>assets/images/widget-multivendorX.svg" alt="<?php echo get_bloginfo('title'); ?>" /></a></h1>
			<?php
	}

	/**
	 * Output the steps.
	 */
	public function mvx_migration_steps() {
		$ouput_steps = $this->steps;
		array_shift($ouput_steps);
		?>
		<ol class="wc-setup-steps">
			<?php foreach ($ouput_steps as $step_key => $step) : ?>
			  <li class="<?php
					if ($step_key === $this->step) {
							echo 'active';
					} elseif (array_search($this->step, array_keys($this->steps)) > array_search($step_key, array_keys($this->steps))) {
							echo 'done';
					}
					?>">
					<?php echo esc_html($step['name']); ?>
				</li>
		<?php endforeach; ?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function mvx_migration_content() {
		echo '<div class="wc-setup-content">';
		call_user_func($this->steps[$this->step]['view'], $this);
		echo '</div>';
	}

	/**
	 * Introduction step.
	 */
	public function mvx_migration_introduction() {
		$this->mvx_migration_first_step( $this->get_next_step_link() );
	}

	public function mvx_migration_first_step ($next_step_link) {
		?>
        <div class="woocommerce-message woocommerce-tracker" style="text-align: center">
       	 <h1><strong><?php esc_html_e( 'Import Marketplace Settings', 'multivendorx' ); ?></strong></h1>
       	 <?php esc_html_e( 'You can import Marketplace settings from the following plugins : ', 'multivendorx' ); ?>
       	</div>
        <div class="wc-setup-next-steps wc-setup-next-line">
        	<div class="wc-setup-next-steps-first">
	        	<h2><?php esc_html_e( 'Input Data From :', 'multivendorx' ); ?></h2>
	        </div>
	        <div class="multivendor-select-content">
				<button class="current-multivendor-accordion"><?php echo $this->get_plugin_name_by_class(); ?></button>
		    	<div class="multivendor-button-panel multivendor-button-panel-background">
		    		<ul class="wc-wizard-payment-gateways">
		    			<li class="wc-wizard-service-item wc-wizard-gateway">
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Marketplace Vendors ', 'multivendorx' ); ?>
		    				</div>
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Marketplace Products', 'multivendorx' ); ?>
		    				</div>
		    			</li>
		    			<li class="wc-wizard-service-item wc-wizard-gateway">
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Vendor Orders', 'multivendorx' ); ?>
		    				</div>
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Vendor Store Details', 'multivendorx' ); ?>
		    				</div>
		    				
		    			</li>
		    			<li class="wc-wizard-service-item wc-wizard-gateway">
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Product Commissions', 'multivendorx' ); ?>
		    				</div>
		    				<div class="wc-wizard-migrator-name">
		    					<?php esc_html_e( 'Vendor Commissions', 'multivendorx' ); ?>
		    				</div>
		    			</li>
		    			<p class="mvx-multivendor-choose-content">
		    				<?php esc_html_e( 'Import settings and meta data from the '. $this->get_plugin_name_by_class() .' plugin. The process may take a few minutes if you have a large number of posts or pages Learn more about the import process here.
		    				'. $this->get_plugin_name_by_class() .' plugin will be disabled automatically moving forward to avoid conflicts. ', 'multivendorx' ); ?> <strong><?php esc_html_e( 'It is thus recommended to import the data you need now.', 'multivendorx' ); ?></strong>
		    			</p>
		    		</ul>
		    	</div>
	    	</div>
	    </div>
        <script>
        	var multivendo_button_value = document.getElementsByClassName("current-multivendor-accordion");
        	var i;
        	for (i = 0; i < multivendo_button_value.length; i++) {
        		multivendo_button_value[i].addEventListener("click", function() {
        			this.classList.toggle("active");
        			var panel = this.nextElementSibling;
        			if (panel.style.maxHeight) {
        				panel.style.maxHeight = null;
        			} else {
        				panel.style.maxHeight = panel.scrollHeight + "px";
        			} 
        		});
        	}
        </script>
        <style>
        	.current-multivendor-accordion {
        		background-color: #eee;
        		color: #444;
        		cursor: pointer;
        		padding: 18px;
        		width: 100%;
        		border: none;
        		text-align: left;
        		outline: none;
        		font-size: 15px;
        		transition: 0.4s;
        	}
        	.current-multivendor-accordion:after {
        		content: '\002B';
        		color: #777;
        		font-weight: bold;
        		float: right;
        		margin-left: 5px;
        	}
        	.active:after {
        		content: "\2212";
        	}
        	.multivendor-button-panel {
        		padding: 0 18px;
        		background-color: white;
        		max-height: 0;
        		overflow: hidden;
        		transition: max-height 0.2s ease-out;
        	}
        	.multivendor-select-content {
        		float: right;
        		width: 150%;
        		box-sizing: border-box;
        	}
        	.wc-setup-next-line {
        		display: flex;
        		justify-content: center;
        	}
        	.multivendor-button-panel-background {
        		background-color: #f8f9fa;
        	}
        	.mvx-multivendor-choose-content {
        		width: 100%;
        		color: #888;
        		font-size: 13px;
        		font-style: italic;
        		margin-top: 20px;
        	}
        	.wc-wizard-migrator-name {
        		min-width: 160px;
        		padding: 2em 0;
        		display: -webkit-box;
        	}
		</style>

		<p><?php printf( __('Before we start please read this carefully -', 'multivendorx') ); ?></p>
		<ul>
			<li><?php esc_html_e("You have already kept backup of your site's Database.", 'multivendorx'); ?></li>
			<li><?php esc_html_e("Disabled caching and optimize plugins.", 'multivendorx'); ?></li>
			<li><?php esc_html_e("Your previous multi-vendor plugin installed and activated.", 'multivendorx'); ?></li>
			<li><?php esc_html_e("Multivendor X installed and activated.", 'multivendorx'); ?></li>
			<li><?php esc_html_e("You are using a non-interrupted internet connection.", 'multivendorx'); ?></li>
		</ul>
		<p style="color: #ff9900;  font-weight: 400;">*<?php printf( __("This migration tool only for migrating vendors as well as product, marketplace orders will not migrated using this. Orders will migrated using cron schedule. Which will run in every five minute.", 'multivendorx') ); ?></p>
		<p style="color: #ff4500;  font-weight: 400;">**<?php printf( __("Vendor's shipping setting will not migrate as Multivendor X vendor shipping totally different than your previous multi-vendor plugin!", 'multivendorx') ); ?></p>
		<p style="color: #ff4500;  font-weight: 400;">**<?php printf( __("Deleted orders or deleted product's orders will not be migrated!", 'multivendorx') ); ?></p>
		<p style="color: #bb0000;  font-weight: 600;">***<?php printf( __('Never close this browser tab when migration is running.', 'multivendorx') ); ?></p>
		<p><?php printf( __('Thanks for reading. Welcome aboard!! Congratulations on being part of the MVX Family!!', 'multivendorx') ); ?></p>
		<p class="wc-setup-actions step">
			<a href="<?php echo esc_url($next_step_link); ?>" class="button-primary button button-large button-next"><?php esc_html_e("Start Import", 'multivendorx'); ?></a>
			<a href="<?php echo esc_url(admin_url('admin.php?page=vendors')); ?>" class="button button-large"><?php esc_html_e("Skip, Don't Import Now", 'multivendorx'); ?></a>
		</p>
		<?php
	}

	/**
	 * Store Processing content
	 */
	public function mvx_migration_store_process() {
		$this->mvx_migration_third_step($this->get_next_step_link());
	}

	public function mvx_migration_third_step($get_next_step_link) {
		$this->init_migration();
		$processed_vendors = absint( mvx_get_option( '_mvx_migrated_vendor_count', 0 ) );
		$migration_step = mvx_get_option( '_mvx_migration_step', '' );
		$marketplace_get_vendors = $this->mvx_migration_marketplace->get_marketplace_vendor();
		if( !empty( $marketplace_get_vendors ) ) {
			foreach( $marketplace_get_vendors as $vendor_id => $marketplace_vendor ) {
				?>
				<h1><?php printf( __("Migrating Store: #%s", 'multivendorx'), $vendor_id. ' ' . $marketplace_vendor->ID ); ?></h1>
				<script>
				  jQuery(document).ready(function($) {
				  		$('.wc-setup-content').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
				  });
				</script>
				<style>
				 .processing_box{padding: 0px 20px 20px 20px; border:1px solid #ccc; border-radius: 5px; margin-bottom: 20px;}
				 .processing_message{}
				 .processing_box_icon{font-size:15px;margin-right:10px;color:#111;}
				 .processing_box_status{font-size:25px;margin-left:10px;color:#00798b;}
				</style>
				<div class="processing_box processing_box_setting">
				  <?php if( !$migration_step ) { ?>
						<p class="setting_process processing_message" style=""><span class="fa fa-gear processing_box_icon"></span><?php printf( __('Store setting migrating ....', 'multivendorx') ); ?></p>
						<?php 
						$setting_complete = $this->store_setting_migrate(); 
						if( $setting_complete ) {
							$migration_step = 'setting';
							mvx_update_option( '_mvx_migration_step', 'setting' );
						?>
							<style>
								.setting_process{display:none;}
							</style>
							<p class="setting_complete processing_message" style=""><span class="fa fa-gear processing_box_icon"></span><?php printf( __('Store setting migration complete', 'multivendorx') ); ?><span class="fa fa-check-square-o processing_box_status"></span></p>
						<?php } ?>
					<?php } else { ?>
						<p class="setting_complete processing_message" style=""><span class="fa fa-gear processing_box_icon"></span><?php printf( __('Store setting migration complete', 'multivendorx') ); ?><span class="fa fa-check-square-o processing_box_status"></span></p>
					<?php } ?>
				</div>
				<div class="processing_box processing_box_product">
				  <?php if( $migration_step == 'setting' ) { ?>
						<p class="product_process processing_message" style=""><span class="fa fa-cube processing_box_icon"></span><?php printf( __('Store product and vendor migrating ....', 'multivendorx') ); ?></p>
						<?php 
						$product_complete = $this->mvx_migration_marketplace->store_product_vendor_migrate($marketplace_vendor->ID); 
						if( $product_complete ) {
							$migration_step = 'product';
							mvx_update_option( '_mvx_migration_step', 'product' );
						?>
							<style>
								.product_process{display:none;}
							</style>
							<p class="product_complete processing_message" style=""><span class="fa fa-cube processing_box_icon"></span><?php printf( __('Store product migration complete', 'multivendorx') ); ?><span class="fa fa-check-square-o processing_box_status"></span></p>
						<?php } ?>
					<?php } else { ?>
						<p class="product_complete processing_message" style=""><span class="fa fa-cube processing_box_icon"></span><?php printf( __('Store product migration complete', 'multivendorx') ); ?><span class="fa fa-check-square-o processing_box_status"></span></p>
					<?php } ?>
				</div>
				
				<?php
				$processed_vendors++;
				mvx_update_option( '_mvx_migrated_vendor_count', $processed_vendors );
				mvx_update_option( '_mvx_migrated_last_vendor', $vendor_id );
				mvx_update_option( '_mvx_migration_step', '' );
				?>
				<script>
				setTimeout(function() {
				  window.location = window.location.href;
				}, 1000);
				</script>
				<?php
			}
		} else {
			wp_safe_redirect($get_next_step_link);
		}
	}
	
	public function store_setting_migrate() {
		return true;
	}

	/**
	 * Ready to go content
	 */
	public function mvx_migration_complete() {
		global $MVX;
		include_once($MVX->plugin_path . '/admin/class-mvx-admin-setup-wizard.php');
		$admin_setup_widget = new MVX_Admin_Setup_Wizard();
		$admin_setup_widget->mvx_setup_ready();
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function mvx_migration_footer() {
			?></body>
			<?php do_action('admin_footer'); ?>
		</html>
		<?php
	}

	function mvx_get_products_by_vendor( $vendor_id = 0, $post_status = 'any', $custom_args = array() ) {
		$vendor_product_list = array();

		if( !$vendor_id ) return $vendor_product_list;
		$vendor_id = absint( $vendor_id );

		if( $post_status == 'any' ) { $post_status = array('draft', 'pending', 'publish', 'private'); }

		$post_count = 9999;
		$post_loop_offset = 0;
		$products_arr = array(0);
		while( $post_loop_offset < $post_count ) {
			$args = array(
				'posts_per_page'   => apply_filters( 'mvx_break_loop_offset', 100 ),
				'offset'           => $post_loop_offset,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'product',
				'post_status'      => $post_status,
				'suppress_filters' => 0,
				'fields'           => 'ids'
			);
			$args = array_merge( $args, $custom_args );
			$is_marketplace = $this->mvx_is_marketplace();
			if( $is_marketplace ) {
				if( $is_marketplace == 'wcpvendors' ) {
					$args['tax_query'][] = array(
						'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
						'field' => 'term_id',
						'terms' => $vendor_id,
					);
				} elseif( $is_marketplace == 'wcvendors' ) {
					$args['author'] = $vendor_id;
				} elseif( $is_marketplace == 'dokan' ) {
					$args['author'] = $vendor_id;
				} elseif( $is_marketplace == 'wcfmmarketplace' ) {
					$args['author'] = $vendor_id;
				}
			}
			$args = apply_filters( 'mvx_products_by_vendor_args', $args );

			$vendor_products = get_posts($args);
			if( !empty( $vendor_products ) ) {
				foreach( $vendor_products as $vendor_product ) {
					$vendor_product_list[$vendor_product] = get_post( $vendor_product );
				}
				$post_loop_offset += apply_filters( 'mvx_break_loop_offset', 100 );
			} else {
				break;
			}
		}
		return $vendor_product_list;
	}

	public function mvx_is_marketplace() {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
		$is_marketplace = false;

		// WCfM Multivendor Marketplace Check
		if( !$is_marketplace )
		$is_marketplace = ( in_array( 'wc-multivendor-marketplace/wc-multivendor-marketplace.php', $active_plugins ) || array_key_exists( 'wc-multivendor-marketplace/wc-multivendor-marketplace.php', $active_plugins ) || class_exists( 'WCFMmp' ) ) ? 'wcfmmarketplace' : false;

		// WC Vendors Check
		if( !$is_marketplace )
			$is_marketplace = ( in_array( 'wc-vendors/class-wc-vendors.php', $active_plugins ) || array_key_exists( 'wc-vendors/class-wc-vendors.php', $active_plugins ) || class_exists( 'WC_Vendors' ) ) ? 'wcvendors' : false;

		// WC Product Vendors Check
		if( !$is_marketplace )
			$is_marketplace = ( in_array( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) || array_key_exists( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) ) ? 'wcpvendors' : false;

		// Dokan Lite Check
		if( !$is_marketplace )
			$is_marketplace = ( in_array( 'dokan-lite/dokan.php', $active_plugins ) || array_key_exists( 'dokan-lite/dokan.php', $active_plugins ) || class_exists( 'WeDevs_Dokan' ) ) ? 'dokan' : false;

		return $is_marketplace;
	}

}

