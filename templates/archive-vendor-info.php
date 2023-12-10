<?php
/**
 * The template for displaying archive vendor info
 *
 * Override this template by copying it to yourtheme/MultiVendorX/archive-vendor-info.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.7
 */
global $MVX;
$vendor = get_mvx_vendor($vendor_id);
$vendor_hide_address = apply_filters('mvx_vendor_store_header_hide_store_address', get_user_meta($vendor_id, '_vendor_hide_address', true), $vendor->id);
$vendor_hide_phone = apply_filters('mvx_vendor_store_header_hide_store_phone', get_user_meta($vendor_id, '_vendor_hide_phone', true), $vendor->id);
$vendor_hide_email = apply_filters('mvx_vendor_store_header_hide_store_email', get_user_meta($vendor_id, '_vendor_hide_email', true), $vendor->id);
$template_class = get_mvx_vendor_settings('mvx_vendor_shop_template', 'store', 'template1');
$template_class = apply_filters('can_vendor_edit_shop_template', false) && get_user_meta($vendor_id, '_shop_template', true) ? get_user_meta($vendor_id, '_shop_template', true) : $template_class;
$vendor_hide_description = apply_filters('mvx_vendor_store_header_hide_description', get_user_meta($vendor_id, '_vendor_hide_description', true), $vendor->id);

$vendor_fb_profile = get_user_meta($vendor_id, '_vendor_fb_profile', true);
$vendor_twitter_profile = get_user_meta($vendor_id, '_vendor_twitter_profile', true);
$vendor_linkdin_profile = get_user_meta($vendor_id, '_vendor_linkdin_profile', true);
$vendor_google_plus_profile = get_user_meta($vendor_id, '_vendor_google_plus_profile', true);
$vendor_youtube = get_user_meta($vendor_id, '_vendor_youtube', true);
$vendor_instagram = get_user_meta($vendor_id, '_vendor_instagram', true);
$vendor_pinterest_profile = get_user_meta($vendor_id, '_vendor_pinterest_profile', true);

// Follow code
$mvx_customer_follow_vendor = get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) : array();
$vendor_lists = !empty($mvx_customer_follow_vendor) ? wp_list_pluck( $mvx_customer_follow_vendor, 'user_id' ) : array();
$follow_status = in_array($vendor_id, $vendor_lists) ? __( 'Unfollow', 'multivendorx' ) : __( 'Follow', 'multivendorx' );
$follow_status_key = in_array($vendor_id, $vendor_lists) ? 'Unfollow' : 'Follow';

if ( $template_class == 'template3') { ?>
<div class='mvx_bannersec_start mvx-theme01'>
    <div class="mvx-banner-wrap">
        <?php $MVX->template->get_template('archive-vendor-banner.php', array('banner' => $banner , 'vendor_banner_type' => $vendor->banner_type, 'vendor_video' => $vendor->video, 'slider_image_gallery' => $vendor->slider)); ?>
        <div class='mvx-banner-area'>
            <div class='mvx-bannerright'>
                <div class="socialicn-area">
                    <div class="mvx_social_profile">
                    <?php if ($vendor_fb_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_fb_profile); ?>"><i class="mvx-font ico-facebook-icon"></i></a><?php } ?>
                    <?php if ($vendor_twitter_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_twitter_profile); ?>"><i class="mvx-font ico-twitter-icon"></i></a><?php } ?>
                    <?php if ($vendor_linkdin_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_linkdin_profile); ?>"><i class="mvx-font ico-linkedin-icon"></i></a><?php } ?>
                    <?php if ($vendor_google_plus_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_google_plus_profile); ?>"><i class="mvx-font ico-google-plus-icon"></i></a><?php } ?>
                    <?php if ($vendor_youtube) { ?> <a target="_blank" href="<?php echo esc_url($vendor_youtube); ?>"><i class="mvx-font ico-youtube-icon"></i></a><?php } ?>
                    <?php if ($vendor_instagram) { ?> <a target="_blank" href="<?php echo esc_url($vendor_instagram); ?>"><i class="mvx-font ico-instagram-icon"></i></a><?php } ?>
                    <?php if ($vendor_pinterest_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_pinterest_profile); ?>"><i class="mvx-font ico-pinterest-icon"></i></a><?php } ?>
                    <?php do_action( 'mvx_vendor_store_header_social_link', $vendor_id ); ?>
                    </div>
                </div>
                <div class='mvx-butn-area'>
                    <?php do_action( 'mvx_additional_button_at_banner' ); ?>
                </div>
            </div>
        </div>

        <div class='mvx-banner-below'>
            <div class='mvx-profile-area'>
                <img src='<?php echo esc_attr($profile); ?>' class='mvx-profile-imgcls' />
            </div>
            <div>
                <div class="mvx-banner-middle">
                    <div class="mvx-heading"><?php echo esc_html($vendor->page_title) ?></div>
                    <!-- Follow button will be added here -->
                    <?php if (mvx_is_module_active('follow-store')) { ?>
                    <button type="button" class="mvx-butn <?php echo is_user_logged_in() ? 'mvx-stroke-butn' : ''; ?>" data-vendor_id=<?php echo esc_attr($vendor_id); ?> data-status=<?php echo esc_attr($follow_status_key); ?> ><span></span><?php echo is_user_logged_in() ? esc_attr($follow_status) : esc_html_e('You must log in to follow', 'multivendorx'); ?></button>
                    <?php } ?>
                </div>
                <div class="mvx-contact-deatil">
                    
                    <?php if (!empty($location) && $vendor_hide_address != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-location-icon"></i></span><?php echo esc_html($location); ?></p><?php } ?>

                    <?php if (!empty($mobile) && $vendor_hide_phone != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-call-icon"></i></span><?php echo apply_filters('vendor_shop_page_contact', $mobile, $vendor_id); ?></p><?php } ?>

                    <?php if (!empty($email) && $vendor_hide_email != 'Enable') { ?>
                    <p class="mvx-address"><a href="mailto:<?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?>" class="mvx_vendor_detail"><i class="mvx-font ico-mail-icon"></i><?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?></a></p><?php } ?>

                    <?php
                    if (apply_filters('is_vendor_add_external_url_field', true, $vendor->id)) {
                        $external_store_url = get_user_meta($vendor_id, '_vendor_external_store_url', true);
                        $external_store_label = get_user_meta($vendor_id, '_vendor_external_store_label', true);
                        if (empty($external_store_label))
                            $external_store_label = __('External Store URL', 'multivendorx');
                        if (isset($external_store_url) && !empty($external_store_url)) {
                            ?><p class="external_store_url"><label><a target="_blank" href="<?php echo apply_filters('vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id); ?>"><?php echo esc_html($external_store_label); ?></a></label></p><?php
                            }
                        }
                        ?>
                    <?php do_action('mvx_after_vendor_information',$vendor_id);?>   
                </div>

                <?php if (!$vendor_hide_description && !empty($description)) { ?>                
                    <div class="description_data"> 
                        <?php echo wp_kses_post(htmlspecialchars_decode( wpautop( $description ), ENT_QUOTES )); ?>
                    </div>
                <?php } ?>
            </div>

            <div class="mvx_vendor_rating">
                <?php
                if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
                    if (mvx_is_store_page()) {
                        $vendor_term_id = get_user_meta( mvx_find_shop_page_vendor(), '_vendor_term_id', true );
                        $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);
                        $MVX->template->get_template('review/rating.php', array('rating_val_array' => $rating_val_array));
                    }
                }
                ?>      
            </div>  

        </div>

    </div>
</div>
<?php } elseif ( $template_class == 'template1' ) {
    ?>
    <div class='mvx_bannersec_start mvx-theme02'>
        
        <div class="mvx-banner-wrap">
        <?php $MVX->template->get_template('archive-vendor-banner.php', array('banner' => $banner , 'vendor_banner_type' => $vendor->banner_type, 'vendor_video' => $vendor->video, 'slider_image_gallery' => $vendor->slider)); ?>
        <div class='mvx-banner-area'>
            <div class='mvx-bannerleft'>
                <div class='mvx-profile-area'>
                    <img src='<?php echo esc_attr($profile); ?>' class='mvx-profile-imgcls' />
                </div>
                <div class="mvx-heading"><?php echo esc_html($vendor->page_title); ?></div>
                
                <div class="mvx_vendor_rating">
                    <?php
                    if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
                        if (mvx_is_store_page()) {
                            $vendor_term_id = get_user_meta( mvx_find_shop_page_vendor(), '_vendor_term_id', true );
                            $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);
                            $MVX->template->get_template('review/rating.php', array('rating_val_array' => $rating_val_array));
                        }
                    }
                    ?>      
                </div>
                    <?php if (!empty($location) && $vendor_hide_address != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-location-icon"></i></span><?php echo esc_html($location); ?></p><?php } ?>

                <div class="mvx-contact-deatil">
                    
                    <?php if (!empty($mobile) && $vendor_hide_phone != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-call-icon"></i></span><?php echo esc_html(apply_filters('vendor_shop_page_contact', $mobile, $vendor_id)); ?></p><?php } ?>

                    <?php if (!empty($email) && $vendor_hide_email != 'Enable') { ?>
                    <p class="mvx-address"><a href="mailto:<?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?>" class="mvx_vendor_detail"><i class="mvx-font ico-mail-icon"></i><?php echo esc_html(apply_filters('vendor_shop_page_email', $email, $vendor_id)); ?></a></p><?php } ?>
                    <?php
                    if (apply_filters('is_vendor_add_external_url_field', true, $vendor->id)) {
                        $external_store_url = get_user_meta($vendor_id, '_vendor_external_store_url', true);
                        $external_store_label = get_user_meta($vendor_id, '_vendor_external_store_label', true);
                        if (empty($external_store_label))
                            $external_store_label = __('External Store URL', 'multivendorx');
                        if (isset($external_store_url) && !empty($external_store_url)) {
                            ?><p class="external_store_url"><label><a target="_blank" href="<?php echo esc_attr(apply_filters('vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id)); ?>"><?php echo esc_html($external_store_label); ?></a></label></p><?php
                            }
                        }
                        ?>
                    <?php do_action('mvx_after_vendor_information',$vendor_id);?>   
                </div>
            </div>
            <div class='mvx-bannerright'>
                <div class="socialicn-area">
                    <div class="mvx_social_profile">
                    <?php if ($vendor_fb_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_fb_profile); ?>"><i class="mvx-font ico-facebook-icon"></i></a><?php } ?>
                    <?php if ($vendor_twitter_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_twitter_profile); ?>"><i class="mvx-font ico-twitter-icon"></i></a><?php } ?>
                    <?php if ($vendor_linkdin_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_linkdin_profile); ?>"><i class="mvx-font ico-linkedin-icon"></i></a><?php } ?>
                    <?php if ($vendor_google_plus_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_google_plus_profile); ?>"><i class="mvx-font ico-google-plus-icon"></i></a><?php } ?>
                    <?php if ($vendor_youtube) { ?> <a target="_blank" href="<?php echo esc_url($vendor_youtube); ?>"><i class="mvx-font ico-youtube-icon"></i></a><?php } ?>
                    <?php if ($vendor_instagram) { ?> <a target="_blank" href="<?php echo esc_url($vendor_instagram); ?>"><i class="mvx-font ico-instagram-icon"></i></a><?php } ?>
                    <?php if ($vendor_pinterest_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_pinterest_profile); ?>"><i class="mvx-font ico-pinterest-icon"></i></a><?php } ?>
                    <?php do_action( 'mvx_vendor_store_header_social_link', $vendor_id ); ?>
                    </div>
                </div>
                <div class='mvx-butn-area'>
                    <!-- Follow button will be added here -->
                    <?php if (mvx_is_module_active('follow-store')) { ?>
                    <button type="button" class="mvx-butn <?php echo is_user_logged_in() ? 'mvx-stroke-butn' : ''; ?>" data-vendor_id=<?php echo esc_attr($vendor_id); ?> data-status=<?php echo esc_attr($follow_status_key); ?> ><span></span><?php echo is_user_logged_in() ? esc_attr($follow_status) : esc_html_e('You must log in to follow', 'multivendorx'); ?></button>
                    <?php } ?>
                    <?php do_action( 'mvx_additional_button_at_banner' ); ?>
                </div>
            </div>

        </div>
        </div>
        <?php if (!$vendor_hide_description && !empty($description)) { ?>                
            <div class="description_data">
                <?php echo wp_kses_post(htmlspecialchars_decode( wpautop( $description ), ENT_QUOTES )); ?>
            </div>
        <?php } ?>
    </div>
<?php } elseif ( $template_class == 'template2' ) {
    ?>
    <div class='mvx_bannersec_start mvx-theme03'>
        <div class="mvx-banner-wrap">
            <?php if($banner != '') { ?>
                <?php $MVX->template->get_template('archive-vendor-banner.php', array('banner' => $banner , 'vendor_banner_type' => $vendor->banner_type, 'vendor_video' => $vendor->video, 'slider_image_gallery' => $vendor->slider)); ?>
            <?php } ?>
            <div class='mvx-banner-area'>
                <div class='mvx-bannerright'>
                    <div class="socialicn-area">
                        <div class="mvx_social_profile">
                        <?php if ($vendor_fb_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_fb_profile); ?>"><i class="mvx-font ico-facebook-icon"></i></a><?php } ?>
                        <?php if ($vendor_twitter_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_twitter_profile); ?>"><i class="mvx-font ico-twitter-icon"></i></a><?php } ?>
                        <?php if ($vendor_linkdin_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_linkdin_profile); ?>"><i class="mvx-font ico-linkedin-icon"></i></a><?php } ?>
                        <?php if ($vendor_google_plus_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_google_plus_profile); ?>"><i class="mvx-font ico-google-plus-icon"></i></a><?php } ?>
                        <?php if ($vendor_youtube) { ?> <a target="_blank" href="<?php echo esc_url($vendor_youtube); ?>"><i class="mvx-font ico-youtube-icon"></i></a><?php } ?>
                        <?php if ($vendor_instagram) { ?> <a target="_blank" href="<?php echo esc_url($vendor_instagram); ?>"><i class="mvx-font ico-instagram-icon"></i></a><?php } ?>
                        <?php if ($vendor_pinterest_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_pinterest_profile); ?>"><i class="mvx-font ico-pinterest-icon"></i></a><?php } ?>
                        <?php do_action( 'mvx_vendor_store_header_social_link', $vendor_id ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class='mvx-banner-below'>
                <div class='mvx-profile-area'>
                    <img src='<?php echo esc_attr($profile); ?>' class='mvx-profile-imgcls' />
                </div>
                <div class="mvx-heading"><?php echo esc_html($vendor->page_title) ?></div>
                
                <div class="mvx_vendor_rating">
                    <?php
                    if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
                        if (mvx_is_store_page()) {
                            $vendor_term_id = get_user_meta( mvx_find_shop_page_vendor(), '_vendor_term_id', true );
                            $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);
                            $MVX->template->get_template('review/rating.php', array('rating_val_array' => $rating_val_array));
                        }
                    }
                    ?>      
                </div>  

                <div class="mvx-contact-deatil">
                    
                    <?php if (!empty($location) && $vendor_hide_address != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-location-icon"></i></span><?php echo esc_html($location); ?></p><?php } ?>

                    <?php if (!empty($mobile) && $vendor_hide_phone != 'Enable') { ?><p class="mvx-address"><span><i class="mvx-font ico-call-icon"></i></span><?php echo apply_filters('vendor_shop_page_contact', $mobile, $vendor_id); ?></p><?php } ?>
                    
                    <?php if (!empty($email) && $vendor_hide_email != 'Enable') { ?>
                    <p class="mvx-address"><a href="mailto:<?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?>" class="mvx_vendor_detail"><i class="mvx-font ico-mail-icon"></i><?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?></a></p><?php } ?>

                    <?php
                    if (apply_filters('is_vendor_add_external_url_field', true, $vendor->id)) {
                        $external_store_url = get_user_meta($vendor_id, '_vendor_external_store_url', true);
                        $external_store_label = get_user_meta($vendor_id, '_vendor_external_store_label', true);
                        if (empty($external_store_label))
                            $external_store_label = __('External Store URL', 'multivendorx');
                        if (isset($external_store_url) && !empty($external_store_url)) {
                            ?><p class="external_store_url"><label><a target="_blank" href="<?php echo apply_filters('vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id); ?>"><?php echo esc_html($external_store_label); ?></a></label></p><?php
                            }
                        }
                        ?>
                    <?php do_action('mvx_after_vendor_information',$vendor_id);?>   
                </div>
                
                <?php if (!$vendor_hide_description && !empty($description)) { ?>                
                    <div class="description_data"> 
                        <?php echo wp_kses_post(htmlspecialchars_decode( wpautop( $description ), ENT_QUOTES )); ?>
                    </div>
                <?php } ?>

                <div class='mvx-butn-area'>
                    <!-- Follow button will be added here -->
                    <?php if (mvx_is_module_active('follow-store')) { ?>
                    <button type="button" class="mvx-butn <?php echo is_user_logged_in() ? 'mvx-stroke-butn' : ''; ?>" data-vendor_id=<?php echo esc_attr($vendor_id); ?> data-status=<?php echo esc_attr($follow_status_key); ?> ><span></span><?php echo is_user_logged_in() ? esc_attr($follow_status) : esc_html_e('You must log in to follow', 'multivendorx'); ?></button>
                    <?php } ?>
                    <?php do_action( 'mvx_additional_button_at_banner' ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
// Additional hook after archive description ended
do_action('mvx_after_vendor_description', $vendor_id);
