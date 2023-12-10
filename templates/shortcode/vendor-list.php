<?php
/**
 * The template for displaying vendor lists
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/vendor-list.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.5.8
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $MVX, $vendor_list;
?>

<?php
/**
 * Hook: mvx_before_vendor_list.
 *
 * @hooked mvx_vendor_list_main_wrapper - 5
 */
do_action( 'mvx_before_vendor_list' );
?>

<?php
/**
 * Hook: mvx_before_vendor_list_map_section.
 * 
 * @hooked mvx_vendor_list_map_wrapper - 5
 */
do_action( 'mvx_before_vendor_list_map_section' );
?>

<?php
/**
 * Hook: mvx_vendor_list_map_section.
 * 
 * @hooked mvx_vendor_list_display_map - 5
 */
do_action( 'mvx_vendor_list_map_section' );
?>

<?php
/**
 * Hook: mvx_after_vendor_list_map_section.
 * 
 * @hooked mvx_vendor_list_form_wrapper - 5
 * @hooked mvx_vendor_list_map_filters - 10
 * @hooked mvx_vendor_list_form_wrapper_end - 15
 * @hooked mvx_vendor_list_map_wrapper_end - 20
 */
do_action( 'mvx_after_vendor_list_map_section' );
?>

<?php
/**
 * Hook: mvx_before_vendor_list_vendors_section.
 *
 * @hooked mvx_vendor_list_catalog_ordering - 5
 * @hooked mvx_vendor_list_content_wrapper - 10
 */
do_action( 'mvx_before_vendor_list_vendors_section' );
?>

<?php
/**
 * Hook: mvx_vendor_list_vendors_section.
 *
 * @hooked mvx_vendor_list_vendors_loop - 10
 */
do_action( 'mvx_vendor_list_vendors_section' );
?>

<?php
/**
 * Hook: mvx_after_vendor_list_vendors_section.
 *
 * @hooked mvx_vendor_list_content_wrapper_end - 10
 * @hooked mvx_vendor_list_pagination - 15
 */
do_action( 'mvx_after_vendor_list_vendors_section' );
?>

<?php 
/**
 * Hook: mvx_after_vendor_list.
 *
 * @hooked mvx_vendor_list_main_wrapper_end - 5
 */
do_action( 'mvx_after_vendor_list' );