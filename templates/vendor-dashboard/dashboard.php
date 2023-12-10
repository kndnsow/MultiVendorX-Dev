<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.0.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
?>
<div class="col-md-12">
    <?php do_action('mvx_dashboard_widget', 'full'); ?>
</div>

<div class="col-md-8">
    <?php do_action('mvx_dashboard_widget', 'normal'); ?>
</div>

<div class="col-md-4">
    <?php do_action('mvx_dashboard_widget', 'side'); ?>
</div>