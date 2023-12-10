<?php
/**
 * MVX Vendor Product Search Widget
 *
 * @author    Multivendor X
 * @category  Widgets
 * @package MultiVendorX/Widgets
 * @version   3.5.4
 * @extends   WC_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget product search class.
 */
class MVX_Widget_Vendor_Product_Search extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'mvx-vproduct-search woocommerce widget_vproduct_search';
		$this->widget_description = __( 'A search form for vendor store products search.', 'multivendorx' );
		$this->widget_id          = 'mvx_vendor_product_search';
		$this->widget_name        = __( 'MVX: Vendor Product Search', 'multivendorx' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'multivendorx' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
        global $MVX;

        if ( !mvx_is_store_page() ) return;

        $this->widget_start( $args, $instance );
        
        ob_start();

		do_action( 'mvx_widget_before_vendor_product_search_form' );

		$MVX->template->get_template('widget/vendor-product-searchform.php');

        $form = apply_filters( 'mvx_widget_vendor_product_search_form', ob_get_clean() );
        
        echo $form;

		$this->widget_end( $args );
	}
}
