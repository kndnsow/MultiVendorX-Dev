<?php
/**
 * The template for displaying archive vendor info
 *
 * Override this template by copying it to yourtheme/MultiVendorX/archive-vendor-banner.php
 *
 * @author      MultiVendorX
 * @package     MultiVendorX/Templates
 * @version     3.7
 */
global $MVX;
if ($vendor_banner_type == 'slider') { 
    if ($slider_image_gallery) { ?>
        <div id="carouselControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
            <?php $attachments = array_filter( explode( ',', $slider_image_gallery ) );
            if ( !empty( $attachments ) ) {
                $count = $indicator = 0; ?>
                <ol class="carousel-indicators">
                    <?php foreach ($attachments as $attachments_id) { ?>
                        <li data-target="#carouselIndicators" data-slide-to="<?php echo $indicator; ?>" <?php echo ($indicator == 0) ? 'class="active"' : ''; ?>></li>
                        <?php $indicator++;
                    }?>
                    </ol>
                    <?php foreach ($attachments as $attachment_id) {    
                    $attachment = wp_get_attachment_image_src($attachment_id, 'full', true); ?>
                        <div class="carousel-item <?php echo ($count == 0) ? 'active' : ''; ?>">
                            <img class="d-block w-100" src="<?php echo $attachment[0]; ?>" alt="slide" height=60>
                        </div>
                    <?php
                    $count++;
                } 
            }?>
            </div>
        </div>
    <?php
    } else { ?>
        <img src="<?php echo $MVX->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" class="mvx-imgcls"/>
    <?php        
    }
} elseif ($vendor_banner_type == 'video') { 
    if ($vendor_video) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $vendor_video, $match);
        $video_id = $match[1]; ?>
        <div class="banner_video">
            <div id="player" class="vlite-js" data-youtube-id="<?php echo $video_id; ?>"></div>        
        </div>
    <?php
    } else { ?>
        <img src="<?php echo $MVX->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" class="mvx-imgcls"/>
    <?php
    }   
} else {
    if ($banner != '') { ?>
        <div class='banner-img-cls'>
        <img src="<?php echo esc_url($banner); ?>" class="mvx-imgcls"/>
        </div>
<?php } else { ?>
        <img src="<?php echo $MVX->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" class="mvx-imgcls"/>
<?php } 
}