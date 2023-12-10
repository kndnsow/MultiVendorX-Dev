<?php

/**
 * Add Downloadable file template
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/views/html-product-download.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<tr>
	<td class="sort"><span class="sortable-icon"></span></td>
	<td class="file_name">
		<input type="text" class="input_text form-control" placeholder="<?php esc_attr_e( 'File name', 'multivendorx' ); ?>" name="_wc_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" />
		<input type="hidden" name="_wc_file_hashes[]" value="<?php echo esc_attr( $key ); ?>" />
	</td>
	<td class="file_url"><input type="text" class="input_text form-control" placeholder="<?php esc_attr_e( 'http://', 'multivendorx' ); ?>" name="_wc_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
	<td class="file_url_choose"><a href="#" class="button upload_file_button" data-choose="<?php esc_attr_e( 'Choose file', 'multivendorx' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'multivendorx' ); ?>" title="<?php echo esc_html__( 'Choose file', 'multivendorx' ); ?>"><i class="mvx-font ico-upload-image-icon"></i></a></td>
	<td><a href="#" class="delete" title="<?php esc_html_e( 'Delete', 'multivendorx' ); ?>"><i class="mvx-font ico-delete-icon"></i></a></td>
</tr>