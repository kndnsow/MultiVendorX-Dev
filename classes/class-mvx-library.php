<?php

/**
 * MVX Library Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Library {

    public $lib_path;
    public $lib_url;
    public $php_lib_path;
    public $php_lib_url;
    public $jquery_lib_path;
    public $jquery_lib_url;
    public $bootstrap_lib_url;
    public $jqvmap;
    public $dataTable_lib_url;
    public $vlite_lib_url;
    public $css_lib_path;
    public $css_lib_url;
    
    public function __construct() {

        global $MVX;

        $this->lib_path = $MVX->plugin_path . 'lib/';

        $this->lib_url = $MVX->plugin_url . 'lib/';

        $this->php_lib_path = $this->lib_path . 'php/';

        $this->php_lib_url = $this->lib_url . 'php/';

        $this->jquery_lib_path = $this->lib_path . 'jquery/';

        $this->jquery_lib_url = $this->lib_url . 'jquery/';

        $this->css_lib_path = $this->lib_path . 'css/';

        $this->css_lib_url = $this->lib_url . 'css/';

        $this->bootstrap_lib_url = $this->lib_url . 'bootstrap/';

        $this->jqvmap = $this->lib_url . 'jqvmap/';

        $this->dataTable_lib_url = $this->lib_url . 'dataTable/';

        $this->vlite_lib_url = $this->lib_url . 'vlite/';
    }

    /**
     * PHP WP fields Library
     */
    public function load_wp_fields() {
        require_once ($this->php_lib_path . 'class-dc-wp-fields.php');
        $DC_WP_Fields = new MVX_WP_Fields();
        return $DC_WP_Fields;
    }

    /**
     * Jquery qTip library
     */
    public function load_qtip_lib() {
        global $MVX;
        wp_enqueue_script('qtip_js', $this->jquery_lib_url . 'qtip/qtip.js', array('jquery'), $MVX->version, true);
        wp_enqueue_style('qtip_css', $this->jquery_lib_url . 'qtip/qtip.css', array(), $MVX->version);
    }

    /**
     * vLite library
     */
    public function load_vlite_lib() {
        global $MVX;
        wp_enqueue_style( 'vlite_css',  $this->vlite_lib_url . 'vlite.css', array(), $MVX->version, true );
        wp_enqueue_script( 'vlite_js', $this->vlite_lib_url . 'vlite.js', array('jquery'), $MVX->version, true );
        
        wp_enqueue_script( 'vlite_youtube_js', $this->vlite_lib_url . 'providers/youtube.js', array('jquery'), $MVX->version, true );    
    }

    /**
     * WP Media library
     */
    public function load_upload_lib() {
        global $MVX;
        wp_enqueue_media();
        wp_enqueue_script('upload_js', $this->jquery_lib_url . 'upload/media-upload.js', array('jquery'), $MVX->version, true);
        wp_enqueue_style('upload_css', $this->jquery_lib_url . 'upload/media-upload.css', array(), $MVX->version);
    }

    /**
     * WP Media library
     */
    public function load_frontend_upload_lib() {
        global $MVX;
        wp_enqueue_media();
        wp_enqueue_script('frontend_upload_js', $this->lib_url . 'upload/media-upload.js', array('jquery'), $MVX->version, true);
        wp_localize_script('frontend_upload_js', 'media_upload_params', array('media_title' => __('Choose Media', 'multivendorx')));
        wp_enqueue_style('upload_css', $this->lib_url . 'upload/media-upload.css', array(), $MVX->version);
    }

    /**
     * WP Media library for dashboard
     */
    public function load_dashboard_upload_lib() {
        global $MVX;
        wp_enqueue_media();
        wp_enqueue_style( 'imgareaselect' );
        wp_enqueue_script('frontend_dash_upload_js', $this->jquery_lib_url . 'upload/frontend-media-upload.js', array('jquery', 'imgareaselect'), $MVX->version, true);
        $enableCrop = false;
        if(wp_image_editor_supports()) $enableCrop = true;
        $image_script_params = array('enableCrop' => $enableCrop, 'default_logo_ratio' => array(100, 100), 'cover_ratio' => array(1200, 390), 'canSkipCrop' => false);
        wp_localize_script( 'frontend_dash_upload_js', 'frontend_dash_upload_script_params', apply_filters( 'mvx_frontend_dash_upload_script_params', $image_script_params) );
    }

    /**
     * Jquery Accordian library
     */
    public function load_accordian_lib() {
        global $MVX;
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_style('accordian_css', $this->jquery_lib_url . 'accordian.css', array(), $MVX->version);
    }

    /**
     * Select2 library
     */
    public function load_select2_lib() {
        global $MVX;
        wp_enqueue_script('select2_js', $this->lib_url . 'select2/select2.js', array('jquery'), $MVX->version, true);
        wp_enqueue_style('select2_css', $this->lib_url . 'select2/select2.css', array(), $MVX->version);
    }

    /**
     * WP ColorPicker library
     */
    public function load_colorpicker_lib() {
        global $MVX;
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('colorpicker_init', $this->jquery_lib_url . 'colorpicker/colorpicker.js', array('jquery', 'wp-color-picker'), $MVX->version, true);
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Jquery style library
     */
    public function load_jquery_style_lib() {
        global $wp_scripts;
        if (!wp_style_is('jquery-ui-style', 'registered')) {
            $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';
            // wp_register_style('jquery-ui-style', $this->jquery_lib_url . 'style_lib.css', array(), $jquery_version);
        }
    }

    public function load_bootstrap_style_lib() {
        wp_register_style('mvx-bootstrap-style', $this->bootstrap_lib_url . 'css/bootstrap.min.css', array(), '4.6.0');
        wp_enqueue_style('mvx-bootstrap-style');
    }

    public function load_bootstrap_script_lib() {
        wp_register_script('mvx-bootstrap-script', $this->bootstrap_lib_url . 'js/bootstrap.bundle.min.js', array('jquery'), '4.6.0');
        if (!defined('MVX_UNLOAD_BOOTSTRAP_LIB')) {
            wp_enqueue_script('mvx-bootstrap-script');
        }
    }

    /**
     * Google Map API
     */
    public function load_gmap_api() {
        $api_key = get_mvx_vendor_settings('google_api_key');
        $protocol = is_ssl() ? 'https' : 'http';
        if ($api_key) {
            $mvx_gmaps_url = apply_filters('mvx_google_maps_api_url', array(
                            'protocol' => $protocol,
                            'url_base' => '://maps.googleapis.com/maps/api/js?',
                            'url_data' => http_build_query(apply_filters('mvx_google_maps_api_args', array(
                                                    'libraries' => 'places',
                                                    'key'       => $api_key,
                                                )
                                            ), '', '&amp;'
					),
				), $api_key
			);
            wp_register_script('mvx-gmaps-api', implode( '', $mvx_gmaps_url ), array('jquery'));
            wp_enqueue_script('mvx-gmaps-api');
        }
    }

    /**
     * dataTable library
     */
    public function load_dataTable_lib() {
        global $MVX;
        wp_register_style('mvx-datatable-bs-style', $this->dataTable_lib_url . 'dataTables.bootstrap.min.css');
        wp_register_style('mvx-datatable-fhb-style', $this->dataTable_lib_url . 'fixedHeader.bootstrap.min.css');
        wp_register_style('mvx-datatable-rb-style', $this->dataTable_lib_url . 'responsive.bootstrap.min.css');
        wp_register_script('mvx-datatable-bs-script', $this->dataTable_lib_url . 'dataTables.bootstrap.min.js', array('jquery'));
        wp_register_script('mvx-datatable-fh-script', $this->dataTable_lib_url . 'dataTables.fixedHeader.min.js', array('jquery'));
        wp_register_script('mvx-datatable-resp-script', $this->dataTable_lib_url . 'dataTables.responsive.min.js', array('jquery'));
        wp_register_script('mvx-datatable-rb-script', $this->dataTable_lib_url . 'responsive.bootstrap.min.js', array('jquery'));
        wp_register_script('mvx-datatable-script', $this->dataTable_lib_url . 'jquery.dataTables.min.js', array('jquery'));
        wp_enqueue_style('mvx-datatable-bs-style');
        wp_enqueue_style('mvx-datatable-fhb-style');
        wp_enqueue_style('mvx-datatable-rb-style');
        wp_enqueue_script('mvx-datatable-script');
        wp_enqueue_script('mvx-datatable-bs-script');
        wp_enqueue_script('mvx-datatable-fh-script');
        wp_enqueue_script('mvx-datatable-resp-script');
        wp_enqueue_script('mvx-datatable-rb-script');
        wp_add_inline_script('mvx-datatable-script', 'jQuery(document).ready(function($){
          $.fn.dataTable.ext.errMode = "none";
        });');
    }

    /**
     * jqvmap library
     */
    public function load_jqvmap_script_lib() {
        wp_register_style('mvx-jqvmap-style', $this->jqvmap . 'jqvmap.min.css', array(), '1.5.1');
        wp_register_script('mvx-vmap-script', $this->jqvmap . 'jquery.vmap.min.js', true, '1.5.1');
        wp_register_script('mvx-vmap-world-script', $this->jqvmap . 'maps/jquery.vmap.world.min.js', true, '1.5.1');
        wp_enqueue_style('mvx-jqvmap-style');
        wp_enqueue_script('mvx-vmap-script');
        wp_enqueue_script('mvx-vmap-world-script');
        do_action('mvx_jqvmap_enqueue_scripts');
    }
    
    /**
     * Stripe Library
     */
    public function stripe_library() {
        if(!class_exists("Stripe\Stripe")) {
            require_once( $this->lib_path . 'Stripe/init.php' );
        }
    }
    
    /**
     * jQuery serializejson Library
     */
    public function load_jquery_serializejson_library() {
        $suffix = defined( 'MVX_SCRIPT_DEBUG' ) && MVX_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('mvx-serializejson', $this->lib_url . 'jquery-serializejson/jquery.serializejson' . $suffix . '.js', array('jquery'), '2.8.1');
    }
    
    /**
     * Load tabs Library
     */
    public function load_tabs_library() {
        wp_enqueue_script( 'mvx-tabs', $this->lib_url . 'tabs/tabs.js', array( 'jquery' ) );
    }

    /**
     * Load mapbox Library
     */
    public function load_mapbox_api() {
        global $MVX;
        $frontend_assets_path = $MVX->plugin_url . 'assets/frontend/';
        $frontend_assets_path = str_replace(array('http:', 'https:'), '', $frontend_assets_path);
        ?>
        <script src="<?php echo $frontend_assets_path . 'js/mapbox/mapboc-gl1.js' ?>"></script>
        <script src="<?php echo $frontend_assets_path . 'js/mapbox/mapboc-gl2.js' ?>"></script>
        <script src="<?php echo $frontend_assets_path . 'js/mapbox/mapboc-gl2.js' ?>"></script>
        <script src="<?php echo $frontend_assets_path . 'js/mapbox/mapboc-gl3.js' ?>"></script>

        <link href="<?php echo $frontend_assets_path . 'css/mapbox/googleapis.css' ?>" rel="stylesheet" />
        <link href="<?php echo $frontend_assets_path . 'css/mapbox/mapbox-gl.css' ?>" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo $frontend_assets_path . 'css/mapbox/mapbox-gl-geocoder.css' ?>" type="text/css">
        <?php
    }
}
