<?php
/**
 * The template for displaying Customer Q & A
 *
 * Override this template by copying it to yourtheme/MultiVendorX/mvx-customer-qna-form.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version    3.0.0
 */
global $MVX, $product;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
do_action('mvx_before_customer_qna_form');
?>
<div id="mvx_customer_qna" class="woocommerce-mvx_customer_qna">
    <div id="cust_qna_form_wrapper">
        <div id="cust_qna_form">
            <h2 id="custqna-title" class="custqna-title"><?php echo apply_filters('mvx_customer_qna_label', esc_html_e('Questions and Answers', 'multivendorx'));?></h2>			
            <div class="qna-ask-wrap">
                <form action="" method="post" id="customerqnaform" class="customerqna-form" novalidate="">
                    <?php wp_nonce_field( 'mvx_customer_qna_form_submit', 'cust_qna_nonce' ); ?>
                    <div id="qna-ask-input">
                        <input type="text" name="cust_question" id="cust_question" placeholder="<?php esc_attr_e('Have a question? Search for answer', 'multivendorx');?>">
                        <div id="ask-wrap">
                            <label class="no-answer-lbl"><?php echo apply_filters('mvx_customer_qna_no_answer_label',esc_html_e("Have not discovered the information you seek", 'multivendorx'));?></label>
                            <button id="ask-qna" class="btn btn-info btn-lg" type="button"><?php esc_html_e('Ask Now', 'multivendorx');?></button>
                        </div>
                        <input type="hidden" name="product_ID" value="<?php echo esc_attr($product->get_id()); ?>" id="product_ID">
                        <input type="hidden" name="cust_ID" id="cust_ID" value="<?php echo esc_attr(get_current_user_id()); ?>">
                    </div>
                </form> 
            </div>
            <div id="qna-result-wrap" class="qna-result-wrap">
            <?php if($cust_qna_data){ 
                foreach ($cust_qna_data as $qna) { 
                    $vendor = get_mvx_vendor($qna->ans_by);
                    if($vendor){
                        $ans_by = $vendor->page_title;
                    }else{
                        $ans_by = get_userdata($qna->ans_by)->display_name;
                    }
                    ?>
                <div class="qna-item-wrap item-<?php echo esc_attr($qna->ques_ID); ?>">
                    <div class="qna-block">

                        <div class="qna-vote">
                            <div class="vote">
                            <?php $count = 0;
                            $ans_vote = maybe_unserialize($qna->ans_vote);
                            if(is_array($ans_vote)){
                                $count = array_sum($ans_vote);
                            }
                            if(is_user_logged_in()){ ?>
                                <?php if($ans_vote && array_key_exists(get_current_user_id(), $ans_vote)) {
                                    if($ans_vote[get_current_user_id()] > 0){ ?>
                                <a href="javascript:void(0)" title="<?php esc_attr_e('You already gave a thumbs up.', 'multivendorx');?>" class="give-up-vote" data-vote="up" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-like"></i></a>
                                <span class="vote-count"><?php echo esc_html($count); ?></span>
                                <a href="" title="<?php esc_attr_e('Give a thumbs down', 'multivendorx');?>" class="give-vote-btn give-down-vote" data-vote="down" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-dislike"></i></a>
                                    <?php }else{ ?>
                                <a href="" title="<?php esc_attr_e('Give a thumbs up', 'multivendorx');?>" class="give-vote-btn give-up-vote" data-vote="up" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-like"></i></a>
                                <span class="vote-count"><?php echo esc_html($count); ?></span>
                                <a href="javascript:void(0)" title="<?php esc_attr_e('You already gave a thumbs down.', 'multivendorx');?>" class="give-down-vote" data-vote="down" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-dislike"></i></a>
                                    <?php } 
                                }else{ ?>
                                <a href="" title="<?php esc_attr('Give a thumbs up', 'multivendorx');?>" class="give-vote-btn give-up-vote" data-vote="up" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-like"></i></a>
                                <span class="vote-count"><?php echo esc_html($count); ?></span>
                                <a href="" title="<?php esc_attr('Give a thumbs down', 'multivendorx');?>" class="give-vote-btn give-down-vote" data-vote="down" data-ans="<?php echo esc_attr($qna->ans_ID); ?>"><i class="vote-sprite vote-sprite-dislike"></i></a>
                                <?php } 
                            }else{ ?>
                                <a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-like"></i></a><span class="vote-count"><?php echo esc_html($count); ?></span><a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-dislike"></i></a>
                            <?php } ?>
                            </div>
                        </div>

                        <div class="qtn-content">
                            <div class="qtn-row">
                                <p class="qna-question"><span><?php esc_html_e('Q:', 'multivendorx'); ?> </span><?php echo esc_html(stripslashes($qna->ques_details)); ?></p>
                            </div>
                            <div class="qtn-row">
                                <p class="qna-answer "><span><?php esc_html_e('A:', 'multivendorx'); ?> </span><?php echo esc_html(stripslashes($qna->ans_details)); ?></p>
                            </div>
                            <div class="bottom-qna">
                                <ul class="qna-info">
                                    <li class="qna-user"><?php echo esc_html($ans_by); ?></li>
                                    <li class="qna-date"><?php echo esc_html(date_i18n(wc_date_format(), strtotime($qna->ques_created))); ?></li> 
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            <?php }
                if(count($cust_qna_data) > 4){
                    echo '<div class="qna-item-wrap load-more-qna"><a href="" class="load-more-btn button" style="width:100%;text-align:center;">'.esc_html('Load More', 'multivendorx').'</a></div>';
                }
            }
            ?>
            </div>
            <div class="clear"></div>
            <?php if( !is_user_logged_in() ) : ?>
            <div id="qna_user_msg_wrap" class="simplePopup">
                <div class="qna_msg" style="text-align: center;">
                    <p><?php esc_html_e('You are not logged in', 'multivendorx'); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php do_action('mvx_after_customer_qna_form');