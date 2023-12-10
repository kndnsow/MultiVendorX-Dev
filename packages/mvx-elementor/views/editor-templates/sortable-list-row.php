<script type="text/template" id="tmpl-elementor-sortable-list-row">
    <div class="elementor-control-type-repeater">
        <div class="elementor-repeater-row-tools">
            <# if ( itemActions.drag_n_drop ) {  #>
                <div class="elementor-repeater-row-handle-sortable">
                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    <span class="elementor-screen-only"><?php esc_html_e( 'Drag & Drop', 'multivendorx' ); ?></span>
                </div>
            <# } #>
            <div class="elementor-repeater-row-item-title"></div>
            <div class="elementor-repeater-row-tool elementor-repeater-tool-display">
                <div class="elementor-control-type-switcher elementor-label-inline elementor-control-separator-default">
                    <div class="elementor-control-content">
                        <div class="elementor-control-field">
                            <div class="elementor-control-input-wrapper">
                                <label class="elementor-switch">
                                    <input
                                        type="checkbox"
                                        data-setting="show"
                                        class="elementor-switch-input"
                                        value="show"
                                        <# if ( show ) {  #>checked <# } #>
                                    >
                                    <span
                                        class="elementor-switch-label"
                                        data-on="<?php esc_attr_e( 'Show', 'multivendorx' ); ?>"
                                        data-off="<?php esc_attr_e( 'Hide', 'multivendorx' );  ?>"
                                    ></span>
                                    <span class="elementor-switch-handle"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="elementor-repeater-row-controls"></div>
    </div>
</script>
