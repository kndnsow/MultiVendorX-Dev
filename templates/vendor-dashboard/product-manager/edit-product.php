<?php
/**
 * MVX edit product template
 *
 * Used by MVX_Products_Edit_Product->output()
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/edit-product.php.
 *
 * HOWEVER, on occasion MVX will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/templates/vendor dashboard/product manager
 * @version     3.3.0
 */
defined( 'ABSPATH' ) || exit;

global $MVX;
$get_product_data_tabs = $self->get_product_data_tabs();
$other_tabs = apply_filters('mvx_product_extra_tabs_added', array('shipping', 'variations'));
$product_fileds = get_mvx_global_settings('products_fields', array());
$default_types = array('general', 'inventory', 'linked_product', 'attribute', 'advanced', 'policies');
foreach ($get_product_data_tabs as $key_tabs => $value_tabs) {
    if (is_array($other_tabs) && in_array($key_tabs, $other_tabs)) continue;
}

if ($default_types && !empty($default_types)) {
    foreach ($default_types as $key_types => $value_types) {
        if (!in_array($value_types, $product_fileds)) {
            unset($get_product_data_tabs[$value_types]);
        }
    }
} else {
    unset($get_product_data_tabs['general'], $get_product_data_tabs['inventory'], $get_product_data_tabs['linked_product'], $get_product_data_tabs['attribute'], $get_product_data_tabs['advanced']);
}

?> 
<div class="col-md-12 add-product-wrapper">
    <?php do_action( 'mvx_before_add_product_form' ); ?>
    <form id="mvx-edit-product-form" class="woocommerce form-horizontal" method="post">
        <?php do_action( 'mvx_add_product_form_start' ); ?>
        <!-- Top product highlight -->
        <?php
        $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-highlights.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post, 'is_update' => $is_update ) );
        $image_size = apply_filters('mvx_product_uploaded_image_size', 'medium');
        ?>
        <!-- End of Top product highlight -->
        <div class="product-primary-info custom-panel"> 
            <div class="right-primary-info"> 
                <div class="form-group-wrapper">
                    <div class="form-group product-short-description">
                        <label class="control-label col-md-12 pt-0" for="product_short_description"><?php esc_html_e( 'Product short description', 'multivendorx' ); ?></label>
                        <div class="col-md-12">
                            <?php
                            $settings = array(
                                'textarea_name' => 'product_excerpt',
                                'textarea_rows' => get_option('default_post_edit_rows', 10),
                                'quicktags'     => array( 'buttons' => 'em,strong,link' ),
                                'tinymce'       => array(
                                    'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                                    'theme_advanced_buttons2' => '',
                                ),
                                'editor_css'    => '<style>#wp-product_excerpt-editor-container .wp-editor-area{height:100px; width:100%;}</style>',
                            );
                            if( !apply_filters( 'mvx_vendor_product_excerpt_richedit', true ) ) {
                                $settings['tinymce'] = $settings['quicktags'] = $settings['media_buttons'] = false;
                            }
                            wp_editor( htmlspecialchars_decode( isset($_POST['product_excerpt']) ? wc_clean($_POST['product_excerpt']) : $product_object->get_short_description( 'edit' ) ), 'product_excerpt', $settings );
                            ?>  
                        </div>
                    </div>
                    
                    <div class="form-group product-description">
                        <label class="control-label col-md-12" for="product_description"><?php esc_attr_e( 'Product description', 'multivendorx' ); ?></label>
                        <div class="col-md-12">
                            <?php
                            $settings = array(
                                'textarea_name' => 'product_description',
                                'textarea_rows' => get_option('default_post_edit_rows', 10),
                                'quicktags'     => array( 'buttons' => 'em,strong,link' ),
                                'tinymce'       => array(
                                    'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                                    'theme_advanced_buttons2' => '',
                                ),
                                'editor_css'    => '<style>#wp-product_description-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
                            );
                            if( !apply_filters( 'mvx_vendor_product_description_richedit', true ) ) {
                                $settings['tinymce'] = $settings['quicktags'] = $settings['media_buttons'] = false;
                            }
                            wp_editor( $product_object->get_description( 'edit' ), 'product_description', $settings );
                            ?>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="left-primary-info">
                <div class="product-gallery-wrapper">
                    <div class="featured-img upload_image"><?php $featured_img = isset($_POST['featured_img']) ? wc_clean($_POST['featured_img']) : ($product_object->get_image_id( 'edit' ) ? $product_object->get_image_id( 'edit' ) : ''); ?>
                        <a href="#" class="upload_image_button tips <?php echo $featured_img ? 'remove' : ''; ?>" <?php echo current_user_can( 'upload_files' ) ? '' : 'data-nocaps="true" '; ?>data-title="<?php esc_attr_e( 'Product image', 'multivendorx' ); ?>" data-button="<?php esc_attr_e( 'Set product image', 'multivendorx' ); ?>" rel="<?php echo esc_attr( $post->ID ); ?>">
                            <div class="upload-placeholder pos-middle">
                                <i class="mvx-font ico-image-icon"></i>
                                <p><?php _e( 'Click to upload Image', 'multivendorx' );?></p>
                            </div>
                            <img src="<?php echo $featured_img ? esc_url( wp_get_attachment_image_src( $featured_img, $image_size )[0] ) : esc_url( wc_placeholder_img_src() ); ?>" />
                            <input type="hidden" name="featured_img" class="upload_image_id" value="<?php echo esc_attr( $featured_img ); ?>" />
                        </a>
                    </div>
                    <div id="product_images_container" class="custom-panel">
                        <h3><?php _e( 'Product gallery', 'multivendorx' );?></h3>
                        <ul class="product_images">
                            <?php
                            if ( metadata_exists( 'post', $post->ID, '_product_image_gallery' ) ) {
                                $product_image_gallery = get_post_meta( $post->ID, '_product_image_gallery', true );
                            } else {
                                // Backwards compatibility.
                                $attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
                                $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
                                $product_image_gallery = isset($_POST['product_image_gallery']) ? wc_clean($_POST['product_image_gallery']) : implode( ',', $attachment_ids );
                            }

                            $attachments = array_filter( explode( ',', $product_image_gallery ) );
                            $update_meta = false;
                            $updated_gallery_ids = array();

                            if ( ! empty( $attachments ) ) {
                                foreach ( $attachments as $attachment_id ) {
                                    $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                                    // if attachment is empty skip
                                    if ( empty( $attachment ) ) {
                                        $update_meta = true;
                                        continue;
                                    }

                                    echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                            ' . $attachment . '
                                            <ul class="actions">
                                                <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'multivendorx' ) . '">' . __( 'Delete', 'multivendorx' ) . '</a></li>
                                            </ul>
                                        </li>';

                                    // rebuild ids to be saved
                                    $updated_gallery_ids[] = $attachment_id;
                                }

                                // need to update product meta to set new gallery ids
                                if ( $update_meta ) {
                                    update_post_meta( $post->ID, '_product_image_gallery', implode( ',', $updated_gallery_ids ) );
                                }
                            }
                            ?>    
                        </ul>
                        <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />
                        <p class="add_product_images">
                            <a href="#" <?php echo current_user_can( 'upload_files' ) ? '' : 'data-nocaps="true" '; ?>data-choose="<?php esc_attr_e( 'Add images to product gallery', 'multivendorx' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'multivendorx' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'multivendorx' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'multivendorx' ); ?>"><?php _e( 'Add product gallery images', 'multivendorx' ); ?></a>
                        </p>
                    </div>
                    <?php do_action('mvx_product_manager_right_panel_after', $post->ID); ?>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="woocommerce-product-data" class="add-product-info-holder">   

                    <div class="add-product-info-header row-padding">
                        <div class="select-group">
                            <label for="product-type"><?php esc_html_e( 'Product Type', 'multivendorx' ); ?></label>
                            <select class="form-control inline-select" id="product-type" name="product-type">
                                <?php foreach ( mvx_get_product_types() as $value => $label ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>" <?php echo selected( $product_object->get_type(), $value, false ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        $product_type_options = $self->get_product_type_options();
                        $required_types = array();
                        foreach ( $product_type_options as $type ) {
                            if ( isset( $type['wrapper_class'] ) ) {
                                $classes = explode( ' ', str_replace( 'show_if_', '', $type['wrapper_class'] ) );
                                foreach ( $classes as $class ) {
                                    $required_types[$class] = true;
                                }
                            }
                        }
                        ?>
                        <?php if ( mvx_is_allowed_product_type( array_keys( $required_types ) ) ) :
                            ?>
                            <div class="pull-right">
                                <?php foreach ( $self->get_product_type_options() as $key => $option ) : ?>
                                    <?php
                                    if ( ! empty( $post->ID ) && metadata_exists( 'post', $post->ID, '_' . $key ) ) {
                                        $selected_value = isset($_POST['_'.$key]) && $_POST['_'.$key] == 'on' ? true : ( is_callable( array( $product_object, "is_$key" ) ) ? $product_object->{"is_$key"}() : 'yes' === get_post_meta( $post->ID, '_' . $key, true ));
                                    } else {
                                        $selected_value = 'yes' === ( isset($_POST['_'.$key]) && $_POST['_'.$key] == 'on' ? 'yes' : ( isset( $option['default'] ) ? $option['default'] : 'no' ) );                                    }
                                    ?>
                                    <label for="<?php echo esc_attr( $option['id'] ); ?>" class="<?php echo esc_attr( $option['wrapper_class'] ); ?> tips" data-tip="<?php echo esc_attr( $option['description'] ); ?>"><input type="checkbox" name="<?php echo esc_attr( $option['id'] ); ?>" id="<?php echo esc_attr( $option['id'] ); ?>" <?php echo checked( $selected_value, true, false ); ?> /> <?php echo esc_html( $option['label'] ); ?></label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- product Info Tab start -->
                    <div class="product-info-tab-wrapper" role="tabpanel">
                        <!-- Nav tabs start -->
                        <div>
                            <div class="tab-nav-direction-wrapper"></div>
                            <ul class="nav nav-tabs" role="tablist" id="product_data_tabs">
                                <?php foreach ( $get_product_data_tabs as $key => $tab ) : ?>
                                    <?php if ( apply_filters( 'mvx_frontend_dashboard_product_data_tabs_filter', ( ! isset( $tab['p_type'] ) || array_key_exists( $tab['p_type'], mvx_get_product_types() ) && mvx_is_product_type_avaliable( $tab['p_type'] ) ), $key, $tab ) ) : ?>
                                        <li role="presentation" class="nav-item <?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ', (array) $tab['class'] ) : ''  ); ?>">
                                            <a class="nav-link" href="#<?php echo esc_attr( $tab['target'] ); ?>" aria-controls="<?php echo $tab['target']; ?>" role="tab" data-toggle="tab"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php do_action( 'mvx_product_write_panel_tabs', $post->ID ); ?>
                            </ul>
                        </div>
                        <!-- Nav tabs End -->

                        <!-- Tab content start -->
                        <div class="tab-content">
                            <?php
                            $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-general.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-inventory.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            if ( !apply_filters('mvx_disabled_product_shipping_tab', true) || mvx_is_allowed_vendor_shipping() ) {
                                $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-shipping.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            }
                            $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-linked-products.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-attributes.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            do_action( 'mvx_after_attribute_product_tabs_content', $self, $product_object, $post );
                            $MVX->template->get_template( 'vendor-dashboard/product-manager/views/html-product-data-advanced.php', array( 'self' => $self, 'product_object' => $product_object, 'post' => $post ) );
                            ?>
                            <?php do_action( 'mvx_product_tabs_content', $self, $product_object, $post ); ?>
                        </div>
                        <!-- Tab content End -->
                    </div>        
                    <!-- product Info Tab End -->
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?php do_action( 'mvx_after_product_excerpt_metabox_panel', $post->ID ); ?>
                <?php do_action( 'mvx_frontend_dashboard_after_product_excerpt_metabox_panel', $post->ID ); ?>
                
                <?php 
                do_action( 'mvx_before_product_note_metabox_panel', $post->ID );
                $vendor = get_mvx_vendor(get_current_user_id() ); 
                $notes = MVX_Product::get_product_note($post->ID);
                ?>
                <?php if($post->post_status == 'pending') { ?>
                <div class="panel panel-default pannel-outer-heading order-action">
                    <div class="panel-heading d-flex">
                        <?php esc_html_e( 'Rejection Note', 'multivendorx' ); ?>
                    </div>
                    <div class="panel-body panel-content-padding form-group-wrapper"> 
                        <ul class="order_notes list-group mb-0">
                            <li class="list-group-item list-group-item-action flex-column align-items-start add_note">
                                <?php if (apply_filters('is_vendor_can_add_product_notes', true, $vendor->id)) : ?>
                                <!--  <form method="post" name="add_product_comment"> -->
                                <?php wp_nonce_field('dc-vendor-add-product-comment', 'vendor_add_product_nonce'); ?> 
                                    <h3><?php _e( 'Add note', 'multivendorx' ); ?> <span class="img_tip" data-desc="<?php echo __( 'Add a note for your reference, or add a customer note (the user will be notified).', 'multivendorx' ); ?>"></span></h3>
                                    <div class="form-group">
                                        <textarea placeholder="<?php _e('Enter text ...', 'multivendorx'); ?>" class="form-control" name="product_comment_text"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input class="btn btn-default mvx-add-order-note" type="submit" name="mvx_submit_product_comment" value="<?php _e('Submit', 'multivendorx'); ?>">
                                    </div>
                                    <input type="hidden" name="product_id" value="<?php echo $post->ID; ?>">
                                    <input type="hidden" name="current_user_id" value="<?php echo $vendor->id; ?>">
                                <!--  </form>  --> 
                                <?php endif; ?>  
                            </li>
                            <li class="list-group-item list-group-item-action flex-column align-items-start"><div class="form-group"><h3><?php esc_html_e( 'Communication Log', 'multivendorx' ); ?></h3></div></li>
                            <?php
                            if ($notes) {
                                foreach ($notes as $note) {
                                    $author = get_comment_meta( $note->comment_ID, '_author_id', true );
                                    $Seller = is_user_mvx_vendor($author) ? "(Seller)" : '';
                                    ?>
                                    <li class="list-group-item list-group-item-action flex-column align-items-start order-notes">
                                        <p class="order-note"><span><?php echo wptexturize( wp_kses_post( $note->comment_content ) ); ?></span></p>
                                        <p><?php echo esc_html($note->comment_author); ?><?php echo $Seller; ?> - <?php echo esc_html( date_i18n(wc_date_format() . ' ' . wc_time_format(), strtotime($note->comment_date) ) ); ?></p>
                                    </li>
                                    <?php
                                }
                            }else{
                                echo '<li class="list-group-item list-group-item-action flex-column align-items-start order-notes">' . __( 'There are no notes yet.', 'multivendorx' ) . '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php do_action( 'mvx_after_product_note_metabox_panel', $post->ID ); ?>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <?php if( get_mvx_vendor_settings('category_pyramid_guide', 'settings_general') == false ) :
                $product_categories = mvx_get_product_terms_HTML( 'product_cat', $post->ID, apply_filters( 'mvx_vendor_can_add_product_category', false, get_current_user_id() ) ); ?>
                <?php if ( $product_categories ) : ?>
                    <div class="panel panel-default pannel-outer-heading">
                        <div class="panel-heading d-flex">
                            <h3 class="pull-left"><?php esc_html_e( 'Product categories', 'multivendorx' ); ?></h3>
                        </div>
                        <div class="panel-body panel-content-padding form-group-wrapper"> 
                            <?php
                            echo $product_categories;
                            ?>
                        </div>
                    </div>
                <?php endif;
                endif; ?>
                <?php $product_tags = mvx_get_product_terms_HTML( 'product_tag', $post->ID, apply_filters( 'mvx_vendor_can_add_product_tag', true, get_current_user_id() ), false ); ?>
                <?php if ( $product_tags && !empty($product_fileds) && in_array('product_tag', $product_fileds) ) : ?>
                    <div class="panel panel-default pannel-outer-heading">
                        <div class="panel-heading d-flex">
                            <h3 class="pull-left"><?php esc_html_e( 'Product tags', 'multivendorx' ); ?></h3>
                        </div>
                        <div class="panel-body panel-content-padding form-group-wrapper">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <?php
                                    echo $product_tags;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php 
                $custom_taxonomies = get_object_taxonomies( 'product', 'objects' );
                if( $custom_taxonomies ){
                    foreach ( $custom_taxonomies as $taxonomy ) {
                        if ( in_array( $taxonomy->name, array( 'product_cat', 'product_tag' ) ) ) continue;
                        if ( $taxonomy->public && $taxonomy->show_ui && $taxonomy->meta_box_cb ) { ?>
                            <div class="panel panel-default pannel-outer-heading">
                                <div class="panel-heading d-flex">
                                    <h3 class="pull-left"><?php echo $taxonomy->label; ?></h3>
                                </div>
                                <div class="panel-body panel-content-padding form-group-wrapper">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <?php
                                            echo mvx_get_product_terms_HTML( $taxonomy->name, $post->ID, apply_filters( 'mvx_vendor_can_add_'.$taxonomy->name, false, get_current_user_id() ) );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }
                }
                ?>
                <?php do_action( 'mvx_after_product_tags_metabox_panel', $post->ID ); ?>
            </div>
        </div>
        <?php if ( ! empty( mvx_get_product_types() ) ) : ?>
            <div class="mvx-action-container">
                <?php
                $primary_action = __( 'Submit', 'multivendorx' );    //default value
                if ( current_vendor_can( 'publish_products' ) ) {
                    if ( ! empty( $product_object->get_id() ) && get_post_status( $product_object->get_id() ) === 'publish' ) {
                        $primary_action = __( 'Update', 'multivendorx' );
                    } else {
                        $primary_action = __( 'Publish', 'multivendorx' );
                    }
                }
                ?>
                <input type="submit" class="btn btn-default" name="submit-data" value="<?php echo esc_attr( $primary_action ); ?>" id="mvx_frontend_dashboard_product_submit" />
                <input type="submit" class="btn btn-default" name="draft-data" value="<?php esc_attr_e( 'Draft', 'multivendorx' ); ?>" id="mvx_frontend_dashboard_product_draft" />
                <input type="hidden" name="status" value="<?php echo esc_attr( get_post_status( $post ) ); ?>">
                <?php wp_nonce_field( 'mvx-product', 'mvx_product_nonce' ); ?>
            </div>
        <?php endif; ?>
        <?php do_action( 'mvx_add_product_form_end' ); ?>
    </form>
    <?php do_action( 'mvx_after_add_product_form' ); ?>
</div>