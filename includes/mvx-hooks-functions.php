<?php
/**
 * MVX Hooks Function
 *
 * Action/filter hooks used for MVX functions/templates.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Vendor List.
 *
 * @see mvx_vendor_list_main_wrapper()
 * @see mvx_vendor_list_main_wrapper_end()
 * @see mvx_vendor_list_map_wrapper()
 * @see mvx_vendor_list_map_wrapper_end()
 * @see mvx_vendor_list_form_wrapper()
 * @see mvx_vendor_list_form_wrapper_end()
 * @see mvx_vendor_list_map_filters()
 * @see mvx_vendor_list_display_map()
 * @see mvx_vendor_list_catalog_ordering()
 * @see mvx_vendor_list_content_wrapper()
 * @see mvx_vendor_list_content_wrapper_end()
 * @see mvx_vendor_list_pagination()
 * @see mvx_vendor_list_vendors_loop()
 */
add_action( 'mvx_before_vendor_list', 'mvx_vendor_list_main_wrapper', 5 );
add_action( 'mvx_after_vendor_list', 'mvx_vendor_list_main_wrapper_end', 20 );
add_action( 'mvx_before_vendor_list_map_section', 'mvx_vendor_list_map_wrapper', 5 );
add_action( 'mvx_after_vendor_list_map_section', 'mvx_vendor_list_map_wrapper_end', 20 );
add_action( 'mvx_after_vendor_list_map_section', 'mvx_vendor_list_form_wrapper', 5 );
add_action( 'mvx_after_vendor_list_map_section', 'mvx_vendor_list_form_wrapper_end', 15 );
add_action( 'mvx_after_vendor_list_map_section', 'mvx_vendor_list_map_filters', 10 );
add_action( 'mvx_vendor_list_map_section', 'mvx_vendor_list_display_map', 5 );
add_action( 'mvx_before_vendor_list_vendors_section', 'mvx_vendor_list_catalog_ordering', 5 );
add_action( 'mvx_before_vendor_list_vendors_section', 'mvx_vendor_list_content_wrapper', 10 );
add_action( 'mvx_after_vendor_list_vendors_section', 'mvx_vendor_list_content_wrapper_end', 10 );
add_action( 'mvx_after_vendor_list_vendors_section', 'mvx_vendor_list_pagination', 15 );
add_action( 'mvx_vendor_list_vendors_section', 'mvx_vendor_list_vendors_loop', 10 );

add_action( 'mvx_vendor_lists_vendor_top_products', 'mvx_vendor_lists_vendor_top_products' );

if ( ! function_exists( 'mvx_vendor_list_main_wrapper' ) ) {

	/**
	 * Get vendor list main wrapper template.
	 */
	function mvx_vendor_list_main_wrapper() {
		// Mapbox design switcher
		mvx_mapbox_design_switcher();
        echo apply_filters( 'mvx_vendor_list_main_wrapper_start', '<div id="mvx-store-conatiner">' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_main_wrapper_end' ) ) {

	/**
	 * Get vendor list main wrapper end template.
	 */
	function mvx_vendor_list_main_wrapper_end() {
        echo apply_filters( 'mvx_vendor_list_main_wrapper_end', '</div>' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_map_wrapper' ) ) {

	/**
	 * Get vendor list map wrapper template.
	 */
	function mvx_vendor_list_map_wrapper() {
        echo apply_filters( 'mvx_vendor_list_map_wrapper_start', '<div class="mvx-store-locator-wrap">' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_form_wrapper' ) ) {

	/**
	 * Get vendor list form wrapper template.
	 */
	function mvx_vendor_list_form_wrapper() {
        echo apply_filters( 'mvx_vendor_list_form_wrapper_start', '<form name="vendor_list_form" method="post">' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_map_filters' ) ) {

	/**
	 * Get vendor list map filters template.
	 */
	function mvx_vendor_list_map_filters() {
        global $MVX;
        if ( !apply_filters( 'mvx_vendor_list_enable_store_locator_map', true ) ) return;
		$MVX->template->get_template('shortcode/vendor-list/map-filters.php');
	}
}

if ( ! function_exists( 'mvx_vendor_list_form_wrapper_end' ) ) {

	/**
	 * Get vendor list form wrapper end template.
	 */
	function mvx_vendor_list_form_wrapper_end() {
        echo apply_filters( 'mvx_vendor_list_form_wrapper_end', '</form>' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_map_wrapper_end' ) ) {

	/**
	 * Get vendor list map wrapper end template.
	 */
	function mvx_vendor_list_map_wrapper_end() {
        echo apply_filters( 'mvx_vendor_list_map_wrapper_end', '</div>' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_display_map' ) ) {

	/**
	 * Get vendor list map template.
	 */
	function mvx_vendor_list_display_map() {
        global $MVX;
        if ( !apply_filters( 'mvx_vendor_list_enable_store_locator_map', true ) ) return;
		$MVX->template->get_template('shortcode/vendor-list/map.php');
	}
}

if ( ! function_exists( 'mvx_vendor_list_catalog_ordering' ) ) {

	/**
	 * Get vendor list catalog ordering template.
	 */
	function mvx_vendor_list_catalog_ordering() {
        global $MVX;
        if ( !apply_filters( 'mvx_vendor_list_enable_store_locator_map', true ) ) return;
		$MVX->template->get_template('shortcode/vendor-list/catalog-ordering.php');
	}
}

if ( ! function_exists( 'mvx_vendor_list_paging_info' ) ) {

	/**
	 * Get vendor list paging information.
	 */
	function mvx_vendor_list_paging_info() {
        global $MVX, $vendor_list;
        extract($vendor_list);
        if ( $vendor_total <= $per_page || -1 === $per_page ) {
            /* translators: %d: total results */
            printf( _n( 'Viewing the single vendor', 'Viewing all %d vendors', $vendor_total, 'multivendorx' ), $vendor_total );
        } else {
            $first = ( $per_page * $current ) - $per_page + 1;
            if(!apply_filters('mvx_vendor_list_ignore_pagination', false)) {
                $last  = min( $vendor_total, $per_page * $current );
            }else{
                $last  = $vendor_total;
            }
            /* translators: 1: first result 2: last result 3: total results */
            printf( _nx( 'Viewing the single vendor', 'Viewing %1$d&ndash;%2$d of %3$d vendors', $vendor_total, 'with first and last result', 'multivendorx' ), $first, $last, $vendor_total );
        }
	}
}

if ( ! function_exists( 'mvx_vendor_list_order_sort' ) ) {

	/**
	 * Get vendor list order sort template.
	 */
	function mvx_vendor_list_order_sort() {
        global $MVX;
        $MVX->template->get_template('shortcode/vendor-list/order-sort.php');
	}
}

if ( ! function_exists( 'mvx_vendor_list_content_wrapper' ) ) {

	/**
	 * Get vendor list content wrapper template.
	 */
	function mvx_vendor_list_content_wrapper() {
		$default_column = apply_filters( 'mvx_vendor_list_row_default_column', 3 );
        echo apply_filters( 'mvx_vendor_list_content_wrapper_start', '<div class="mvx-store-list-wrap list-' . $default_column . '">' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_content_wrapper_end' ) ) {

	/**
	 * Get vendor list content wrapper end template.
	 */
	function mvx_vendor_list_content_wrapper_end() {
		echo apply_filters( 'mvx_vendor_list_content_wrapper_end', '</div>' );
	}
}

if ( ! function_exists( 'mvx_vendor_list_vendors_loop' ) ) {

	/**
	 * Get vendor list vendor loop section.
	 */
	function mvx_vendor_list_vendors_loop() {
		global $MVX, $vendor_list;
		if ( $vendor_list['vendors'] ) {
            foreach ( $vendor_list['vendors'] as $vendor_id ) {
        		$MVX->template->get_template(
					'shortcode/vendor-list/content-vendor.php', 
					array( 'vendor_id' => $vendor_id )
				);
			}
		} else {
			mvx_no_vendors_found();
		}
	}
}

if ( ! function_exists( 'mvx_no_vendors_found' ) ) {

	/**
	 * No vendors found section.
	 */
	function mvx_no_vendors_found() {
		echo apply_filters( 'mvx_no_vendors_found', __('No vendor found!', 'multivendorx') );
	}
}

if ( ! function_exists( 'mvx_vendor_list_pagination' ) ) {

	/**
	 * Get vendor list pagination templates.
	 */
	function mvx_vendor_list_pagination() {
		global $MVX, $vendor_list;
		extract( $vendor_list );

		if(!apply_filters('mvx_vendor_list_ignore_pagination', false)) : ?>
			<div class="mvx-pagination">
				<?php
					echo paginate_links( apply_filters( 'mvx_vendor_list_pagination_args', array( 
							'base'         => $base,
							'format'       => $format,
							'add_args'     => false,
							'current'      => max( 1, $current ),
							'total'        => $total,
							'prev_text'    => 'Prev',
							'next_text'    => 'Next',
							'type'         => 'list',
							'end_size'     => 3,
							'mid_size'     => 3,
					) ) );
			?>
			</div>
		<?php endif; 
	}
}

if ( ! function_exists( 'mvx_vendor_lists_vendor_top_products' ) ) {

	/**
	 * Get vendor top products.
	 */
	function mvx_vendor_lists_vendor_top_products( $vendor ) {
		if( !$vendor ) return false;
		if( !get_transient( 'mvx_vendorlist_top_products_' . $vendor->id ) ) {
			$top_products = $vendor->get_top_rated_products( array( 'posts_per_page' => 3 ) );
			if( !$top_products ) return false;
			set_transient( 'mvx_vendorlist_top_products_' . $vendor->id, $top_products );
		}
		$top3_products = apply_filters('mvx_vendor_top_product_list_suffle', true) ? $vendor->get_top_rated_products( array( 'posts_per_page' => 3 ) ) : get_transient( 'mvx_vendorlist_top_products_' . $vendor->id );
		if( !$top3_products ) return false;
		$html = '<div class="mvx-headline">
                <div class="mvx-topProduct">' .__( 'Top Products' , 'multivendorx' ) . '</div>
            </div>';
		$html .= '<div class="mvx-productImg">';

		foreach( $top3_products as $product ) {
			if ( !wc_get_product($product->ID) ) return;
			$top_product_image = get_the_post_thumbnail_url( $product->ID ) ? '<a href="'. get_permalink($product->ID) .'"><img class="img-fluid" src="' . get_the_post_thumbnail_url( $product->ID ) . ' "  ></a>' : '<a href="'. get_permalink($product->ID) .'"><img class="img-fluid" src="' . WC()->plugin_url() . '/assets/images/placeholder-attachment.png' . ' " ></a>';

    		$html .= '<div class="gray">'. $top_product_image .'</div>';
		}
		
		$html .= '</div>';
		echo $html;
	}
}
