<?php
    global $MVX;
    $vendor_shipping_methods = mvx_get_shipping_methods();
?>
<div id="mvx_shipping_method_add_container" class="collapse mvx-modal-dialog">
    <div class="mvx-modal">
        <div class="mvx-modal-content" tabindex="0">
            <section class="mvx-modal-main" role="main">
                <header class="mvx-modal-header">
                    <h1><?php _e( 'Add shipping method', 'multivendorx' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'multivendorx' ); ?></span>
                    </button>
                </header>
                <article>
                    <form action="" method="post">
                        <div class="wc-shipping-zone-method-selector">
                            <p><?php _e( 'Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'multivendorx' ); ?></p>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-9">
                                    <select id="shipping_method" class="form-control mt-15" name="mvx_shipping_method">
                                        <?php foreach( $vendor_shipping_methods as $key => $method ) { 
                                            echo '<option data-description="' . esc_attr( wp_kses_post( wpautop( $method->get_method_description() ) ) ) . '" value="' . esc_attr( $method->id ) . '">' . esc_attr( $method->get_method_title() ) . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="wc-shipping-zone-method-description"><p><?php _e( 'Lets you charge a fixed rate for shipping.', 'multivendorx' ); ?></p></div>
                        </div>
                    </form>
                </article>
                <footer>
                    <div class="inner">
                        <button id="btn-ok" class="btn btn-default add-shipping-method"><?php _e( 'Add shipping method', 'multivendorx' ); ?></button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
    <div class="mvx-modal-backdrop modal-close"></div>
</div>