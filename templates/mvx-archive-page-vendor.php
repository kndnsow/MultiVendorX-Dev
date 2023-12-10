<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: mvx_before_main_content.
 *
 */

do_action( 'mvx_before_main_content' );

global $MVX;

?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'mvx_show_page_title', true ) ) : ?>
		<div class="woocommerce-products-header__title page-title"><?php is_tax($MVX->taxonomy->taxonomy_name) ? woocommerce_page_title() : print(get_user_meta( mvx_find_shop_page_vendor(), '_vendor_page_title', true )); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * Hook: mvx_archive_description.
	 *
	 */
	do_action( 'mvx_archive_description' );
	?>
</header>
<?php

/**
 * Hook: mvx_store_tab_contents.
 *
 * Output mvx store widget
 */

do_action( 'mvx_store_tab_widget_contents' );


/**
 * Hook: mvx_after_main_content.
 *
 */
do_action( 'mvx_after_main_content' );

/**
 * Hook: mvx_sidebar.
 *
 */
// deprecated since version 3.0.0 with no alternative available
// do_action( 'mvx_sidebar' );

get_footer( 'shop' );