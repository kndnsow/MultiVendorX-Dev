<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-announcements/vendor-announcements-all.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.3.0
 */
global $MVX;
$tab1_counter = 0;
?>

<?php if($posts_array) { ?>
    <div id="accordion-1">
    <?php foreach( $posts_array as $post_element) { ?>
        <div <?php if($tab1_counter >= 6) {?> class="mvx_hide_message" <?php }?>>
                <div class="msg_date_box"><span><?php echo @date('d',strtotime($post_element->post_date)); ?></span><br><?php echo @date_i18n('M',strtotime($post_element->post_date)); ?></div>
                <div class="msg_title_box"><span class="title"><?php echo $post_element->post_title; ?></span><br><span class="mormaltext"> <?php echo $short_content = substr(stripslashes(strip_tags($post_element->post_content)),0,105); if(strlen(stripslashes(strip_tags($post_element->post_content))) > 105) {echo '...'; } ?></span> </div>
                <div class="msg_arrow_box">
                        <a href="#" class="msg_stat_click">
                                <i class="mvx-font ico-downarrow-2-icon"></i>
                        </a>
                        <div class="msg_stat" style="display:none" >
                                <ul class="mvx_msg_all_ul" data-element="<?php echo $post_element->ID; ?>">									
                                        <?php if(!$post_element->is_read) {?>
                                        <li class="_mvx_vendor_message_read"><a href="#"> <?php _e('Mark Read','multivendorx');?></a></li>
                                        <?php } ?>
                                        <?php if($post_element->is_read) {?>
                                        <li class="_mvx_vendor_message_unread"><a href="#"> <?php _e('Mark Unread','multivendorx');?></a></li>
                                        <?php } ?>
                                        <li class="_mvx_vendor_message_delete"><a href="#"> <?php _e('Delete','multivendorx');?></a></li>							 
                                </ul>
                        </div>
                </div>
                <div class="clear"></div>
        </div>

        <div <?php if($tab1_counter >= 6) {?> class="mvx_hide_message" <?php }?> >
                <div class="mvx_anouncement-content">
                        <?php echo $content = apply_filters('the_content',$post_element->post_content); ?>	
                        <?php $url = get_post_meta($post_element->ID, '_mvx_vendor_notices_url', true);  if(!empty($url)) { ?>
                        <p style="text-align:right; width:100%;"><a href="<?php echo $url;?>" target="_blank" class="btn btn-default mvx_black_btn_link"><?php echo __('Read More','multivendorx');?></a></p>
                        <?php }?>
                </div>
        </div>

	<?php $tab1_counter++; }
	if($tab1_counter <= 6) {
		$tab1_counter_show = $tab1_counter;
	}
	else {
		$tab1_counter_show = 6;
	}
        ?>
</div>
<?php }else{ ?>
  <div class="panel panel-default panel-pading text-center empty-panel"><?php _e('Sorry no announcement found.','multivendorx'); ?></div>  
<?php }	?>			


<div class="mvx_mixed_txt" >
	<?php if($tab1_counter > 6) {?>	
	<button class="mvx_black_btn mvx_black_btn_msg_for_nav" style="float:right"><?php _e('Show More','multivendorx'); ?></button>
	<?php }?>

	<div class="clear"></div>
</div>	
