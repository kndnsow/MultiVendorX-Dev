<?php 
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/widget/vendor-review.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     0.0.1
 */

global $MVX;
$review_count = count($comments) ;
if($review_count > 3) { ?>
<div id="mvx_widget_vendor_review" style="max-height: 300px; overflow-y: auto;" >
<?php } else { ?>
<div id="mvx_widget_vendor_review">
<?php
 }
if($comments){
	foreach($comments as $comment) {
		$rating   = intval( get_comment_meta( $comment->comment_ID, 'vendor_rating', true ) );
		$verified = mvx_review_is_from_verified_owner( $comment, $vendor->term_id );
		if ( $rating && get_option( 'woocommerce_enable_review_rating' ) === 'yes'){?>
			<div class="comment-text">
				<a href="<?php echo esc_url($vendor->permalink).'#li-comment-'.$comment->comment_ID ?>">
					<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'multivendorx' ), $rating ) ?>">
						<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo esc_html($rating); ?></strong> <?php esc_html_e( 'out of 5', 'multivendorx' ); ?></span>
					</div>
				</a>
				<?php if ( $comment->comment_approved != '0' ) {?>
					<p class="meta">
						<strong itemprop="author"><?php esc_html(comment_author($comment->comment_ID)); ?></strong> <?php
						if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' )
							if ( $verified )
								echo '<em class="verified">(' . apply_filters('mvx_widget_varified_buyer_text_filter',esc_html_e( 'verified buyer', 'multivendorx' )) . ')</em> ';

						?>&ndash; <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c',$comment->comment_ID ); ?>"><?php echo esc_html(get_comment_date( wc_date_format(), $comment->comment_ID )); ?></time>
					</p>
				<?php } ?>
				<div itemprop="description" class="description"><?php 
					echo wp_trim_words($comment->comment_content, 10, "..<a href=".$vendor->permalink.'#li-comment-'.$comment->comment_ID.">Read more</a>" );?>
					<div style="height:10px; width:100%">&nbsp;</div>
				</div>
			</div>
		<?php
		}
	}
} else {
 		echo apply_filters('mvx_widget_empty_reviews_text_filter', __( 'No Reviews..', 'multivendorx' ));
 	}
 ?>
</div>