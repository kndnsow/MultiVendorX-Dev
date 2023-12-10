<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/widget/vendor-info.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     0.0.1
 */

global $MVX;
?>

<h4 class="vendor-info-page-title"><?php echo esc_html($vendor->page_title); ?> </h4>
<?php 
	$description = strip_tags($vendor->description);
	if (strlen($description) > 250) {
		// truncate string
		$stringCut = substr($description, 0, 250);

		// make sure it ends in a word so assassinate doesn't become ass...
		$description = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
	}
?>
<p class="vendor-info-description"><?php echo esc_html($description); ?> </p>
<p class="vendor-info-shop-link">
	<a href="<?php echo esc_url( $vendor->permalink ); ?>" title="<?php echo sprintf( __( 'More Products from %1$s', 'multivendorx' ), $vendor->page_title ); ?>">
		<?php echo sprintf( __( 'More Products from %1$s', 'multivendorx' ), $vendor->page_title );?>
	</a>
</p>