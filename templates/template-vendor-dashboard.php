<?php
/**
 * The template for displaying vendor dashboard content
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/template-vebdor-dashboard.php.
 *
 * HOWEVER, on occasion Multivendor X will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version 3.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}
global $MVX;
$dashboard_scheme = 'mvx-color-scheme-'.get_mvx_vendor_settings('vendor_color_scheme_picker', 'seller_dashbaord', 'outer_space_blue');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class($dashboard_scheme); ?>>
        
        <?php while (have_posts()) : the_post(); ?>
            <div id="wrapper" class="mvx-wrapper">
                <?php the_content(); ?>
            </div>
            <?php
        endwhile;
        wp_reset_query();
        
        wp_footer();
        ?>
    </body>
</html>
