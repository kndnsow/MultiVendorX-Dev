<?php
/**
 * Top Rated Vendors block.
 *
 * @package MVX/Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * TopRatedVendors class.
 */
class TopRatedVendors extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'top-rated-vendors';

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return array_merge(
			parent::get_attributes(),
			array(
				'block_title'    	=> $this->get_schema_string(),
				'block_columns'  	=> $this->get_schema_number( 3 ),
				'block_rows'     	=> $this->get_schema_number( 1 ),
				'contentVisibility' => $this->get_schema_content_visibility(),
			)
		);
	}

	/**
	 * Render the Product Categories List block.
	 *
	 * @param array  $attributes Block attributes. Default empty array.
	 * @param string $content    Block content. Default empty string.
	 * @return string Rendered block type output.
	 */
	public function render( $attributes = array(), $content = '' ) {
		global $MVX;
		wp_enqueue_style('frontend_css');
		$contentVisibility = isset($attributes['contentVisibility']) && !empty($attributes['contentVisibility']) ? $attributes['contentVisibility'] : '';
		$topvendors = $this->get_toprated_vendors();
		// slice with rows & columns
		$topvendors = array_slice( $topvendors, 0, ( $attributes['block_columns'] * $attributes['block_rows'] ) );
		$output = '';
    	ob_start();
    	?>
		<div class="mvx-block-wrapper <?php echo isset ($attributes['className'] ) ? $attributes['className'] : ''; ?>">
		<?php if ( $topvendors ) : 
			if( $attributes['block_title'] ) echo '<h4 class="mvx-block-heading">' . $attributes['block_title'] . '</h4>'; 
		?>
			<ul class="top_vendors">
			<?php foreach( $topvendors as $vendor_rating_info ) : 
				$vendor = get_mvx_vendor( $vendor_rating_info['vendor_id'] );
				$banner_src = ( $contentVisibility && isset($contentVisibility['banner']) && $contentVisibility['banner'] ) ? $vendor->get_image('banner') : $vendor->get_image();
				$logo_src = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
			?>
		  		<li class="top_vendors__item">
					<div class="top_vendor">
						<div class="top_vendor__image" style="background-image: url('<?php echo $banner_src; ?>');">
							<?php if ( $contentVisibility && isset($contentVisibility['logo']) && $contentVisibility['logo'] ) : ?>
							<div class="profile-pic">
								<img src="<?php echo $logo_src; ?>" alt="<?php echo $vendor->page_title; ?>">
							</div>
							<?php endif; ?>
						</div>
			  			
						<div class="top_vendor__content">
							<?php if ( $contentVisibility && isset($contentVisibility['rating']) && $contentVisibility['rating'] ) : ?>
							<div class="rating">
								<?php
                                $MVX->template->get_template( 'review/rating_vendor_lists.php', array( 'rating_val_array' => $vendor_rating_info ) );
                            	?>
							</div>
							<?php endif; ?>
							<?php if ( $contentVisibility && isset($contentVisibility['title']) && $contentVisibility['title'] ) : ?>
							<div class="top_vendor__title"><a href="<?php echo $vendor->get_permalink(); ?>" ><?php echo $vendor->page_title; ?></a></div>
							<?php endif; ?>
							<!--p class="top_vendor__text"></p-->
							<!--button class="btn btn--block top_vendor__btn"></button-->
						</div>
						<?php if ( $contentVisibility && isset($contentVisibility['social_link']) && $contentVisibility['social_link'] ) : ?>
						<div class="social">
							<div class="hover">
								<span><?php _e( 'Join Me', 'multivendorx' ) ?></span>
								<?php
								$vendor_fb_profile = get_user_meta( $vendor->id, '_vendor_fb_profile', true );
								$vendor_twitter_profile = get_user_meta( $vendor->id, '_vendor_twitter_profile', true );
								$vendor_linkdin_profile = get_user_meta( $vendor->id, '_vendor_linkdin_profile', true );
								$vendor_google_plus_profile = get_user_meta( $vendor->id, '_vendor_google_plus_profile', true );
								$vendor_youtube = get_user_meta( $vendor->id, '_vendor_youtube', true );
								$vendor_instagram = get_user_meta( $vendor->id, '_vendor_instagram', true );
								?>
								<?php if ($vendor_fb_profile) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_fb_profile); ?>"><i class="mvx-font ico-facebook-icon"></i></a><?php } ?>
								<?php if ($vendor_twitter_profile) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_twitter_profile); ?>"><i class="mvx-font ico-twitter-icon"></i></a><?php } ?>
								<?php if ($vendor_linkdin_profile) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_linkdin_profile); ?>"><i class="mvx-font ico-linkedin-icon"></i></a><?php } ?>
								<?php if ($vendor_google_plus_profile) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_google_plus_profile); ?>"><i class="mvx-font ico-google-plus-icon"></i></a><?php } ?>
								<?php if ($vendor_youtube) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_youtube); ?>"><i class="mvx-font ico-youtube-icon"></i></a><?php } ?>
								<?php if ($vendor_instagram) { ?> <a class="social-link" target="_blank" href="<?php echo esc_url($vendor_instagram); ?>"><i class="mvx-font ico-instagram-icon"></i></a><?php } ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
		  		</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		</div>
		<?php
    	$output = ob_get_contents();
    	ob_end_clean();
	
		return $output;
	}

	/**
	 * Get Top rated vendors.
	 *
	 * @return array
	 */
	protected function get_toprated_vendors() {
		$allvendors = get_mvx_vendors( array(), 'id' );
		$vendors = array();
		foreach ( $allvendors as $vendor_id ) {
			$vendor_term_id = ( get_user_meta( $vendor_id, '_vendor_term_id', true) ) ? get_user_meta( $vendor_id, '_vendor_term_id', true) : false;
			if( !$vendor_term_id ) continue;
			$rating_info = mvx_get_vendor_review_info( $vendor_term_id );
			$vendors[$vendor_id] = $rating_info;
			$vendors[$vendor_id]['vendor_id'] = $vendor_id;
		}
		// sort by avg_rating
		array_multisort( array_map( function( $vendor ) {
			return $vendor['avg_rating'];
		}, $vendors ), SORT_DESC, $vendors );
		return $vendors;
	}

	/**
	 * Get the schema for the contentVisibility attribute
	 *
	 * @return array List of block attributes with type and defaults.
	 */
	protected function get_schema_content_visibility() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'banner'  		=> $this->get_schema_boolean( true ),
				'logo'  		=> $this->get_schema_boolean( true ),
				'rating' 		=> $this->get_schema_boolean( true ),
				'title' 		=> $this->get_schema_boolean( true ),
				'social_link' 	=> $this->get_schema_boolean( true ),
			),
		);
	}
}
