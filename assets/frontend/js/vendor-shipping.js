/* global mvx_vendor_shipping_script_data */
(function ($) {
    if (typeof mvx_vendor_shipping_script_data === 'undefined') {
        return false;
    }
    var app = app || {
        build: function () {
            this.init();
            this.bindEvents();
        },

        init: function () {
            this.modify_shipping_methods = '.modify-shipping-methods';
            this.vendor_shipping_methods = '#vendor-shipping-methods';
            this.shipping_by_zone_holder = '#mvx_settings_form_shipping_by_zone';
            this.shipping_zone_table = this.shipping_by_zone_holder + ' .shipping-zone-table';
            this.shipping_zone_list = '.shipping-zone-list';
            this.shipping_method_manage_form = '#mvx_shipping_method_manage_form';
            this.show_shipping_methods = this.vendor_shipping_methods + ' .show-shipping-methods';
            this.add_shipping_methods = this.vendor_shipping_methods + ' .add-shipping-method';
            this.edit_shipping_method = this.vendor_shipping_methods + ' .edit-shipping-method';
            this.update_shipping_method = this.vendor_shipping_methods + ' .update-shipping-method';
            this.delete_shipping_method = this.vendor_shipping_methods + ' .delete-shipping-method';
            this.limit_zone_location = this.vendor_shipping_methods + ' #limit_zone_location';
            this.method_status = this.vendor_shipping_methods + ' .method-status';
            this.modal_close_link = '.modal-close-link';
            this.modal_dialog = '.mvx-modal-dialog';
        },

        bindEvents: function () {
            $(this.modify_shipping_methods).on('click', this.modifyShippingMethods.bind(this));
            $(document).on('zone_settings_loaded', this.zoneLoadedEvents.bind(this));
            $( document.body ).on( 'change', '.wc-shipping-zone-method-selector select', this.onChangeShippingMethodSelector );
            /* delegate events */
            $(document).delegate(this.shipping_zone_list, 'click', this.goToShippingZones.bind(this));
            $(document).delegate(this.show_shipping_methods, 'click', this.showShippingMethods.bind(this));
            $(document).delegate(this.add_shipping_methods, 'click', this.addShippingMethod.bind(this));
            $(document).delegate(this.edit_shipping_method, 'click', this.editShippingMethod.bind(this));
            $(document).delegate(this.update_shipping_method, 'click', this.updateShippingMethod.bind(this));
            $(document).delegate(this.delete_shipping_method, 'click', this.deleteShippingMethod.bind(this));
            $(document).delegate(this.limit_zone_location, 'click', this.limitZoneLocation.bind(this));
            $(document).delegate(this.method_status, 'change', this.toggleShippingMethod.bind(this));
            $(document).delegate(this.modal_close_link, 'click', this.closeModal.bind(this));
        },

        modifyShippingMethods: function (event, zoneID) {
            var appObj = this;
            $('#mvx_settings_form_shipping_by_zone').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            
            if (typeof event !== "undefined") {
                event.preventDefault();
                zoneID = $(event.currentTarget).data('zoneId');
            }

            var ajaxRequest = $.ajax({
                method: 'post',
                url: mvx_vendor_shipping_script_data.ajaxurl,
                data: {
                    action: 'mvx-get-shipping-methods-by-zone',
                    zoneID: zoneID,
                    security: mvx_vendor_shipping_script_data.security
                },
                success: function (response) {
                    $(appObj.vendor_shipping_methods).html(response.data.html).show();
                    $(appObj.shipping_zone_table).hide();
                },
                complete: function () {
                    $('#mvx_settings_form_shipping_by_zone').unblock();
                    $(document).trigger('zone_settings_loaded');
                }
            });
        },

        zoneLoadedEvents: function (event) {
            this.limitZoneLocation(event);
        },

        goToShippingZones: function (event) {
            event.preventDefault();
            $(this.vendor_shipping_methods).html('').hide();
            $(this.shipping_zone_table).show();
            window.location.reload();
        },
        
        onChangeShippingMethodSelector: function() {
            var description = $( this ).find( 'option:selected' ).data( 'description' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( '' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( description );
        },

        showShippingMethods: function (event) {
            event.preventDefault();

            /* make popup */
            $('#mvx_shipping_method_add_container').show();
            $('#mvx_shipping_method_add_container ' + this.modal_dialog).show();
        },

        addShippingMethod: function (event) {
            event.preventDefault();
            $(this.add_shipping_methods).prop('disabled', true);
            $( this.add_shipping_methods ).block({
                message: null,
                overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                }
            });

            var appObj = this;

            var zoneId = $('#zone_id').val(),
                    shippingMethod = $('#shipping_method option:selected').val();
            if (zoneId == '') {
                // alert(mvx_dashboard_messages.shiping_zone_not_found);
            } else if (shippingMethod == '') {
                // alert(mvx_dashboard_messages.shiping_method_not_selected);
            } else {
                var data = {
                    action: 'mvx-add-shipping-method',
                    zoneID: zoneId,
                    method: shippingMethod,
                    security: mvx_vendor_shipping_script_data.security
                };

                $('#mvx_shipping_method_add_button').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                // $('#mvx_settings_save_button').click();

                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: mvx_vendor_shipping_script_data.ajaxurl,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('#mvx_shipping_method_add_container').hide();
                            appObj.modifyShippingMethods(undefined, zoneId);
                        } else {

                        }
                    },
                });
            }
        },

        editShippingMethod: function (event) {
            event.preventDefault();
            $( '.mvx-zone-method-content' ).block({
                    message: null,
                    overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                    }
            });
            
            var instanceId = $(event.currentTarget).parents('.edit_del_actions').data('instance_id'),
                methodId = $(event.currentTarget).parents('.edit_del_actions').data('method_id'),
                zoneId = $(event.currentTarget).parents('.edit_del_actions').data('zone_id'),
                data = {
                    action: 'mvx-vendor-configure-shipping-method',
                    zoneId: zoneId,
                    instanceId: instanceId,
                    methodId: methodId,
                    security: mvx_vendor_shipping_script_data.security
                };
                
            var ajaxRequest = $.ajax({
                method: 'post',
                url: mvx_vendor_shipping_script_data.ajaxurl,
                data: data,
                success: function (response) {
                    if (response){
                        $( '.mvx-zone-method-content' ).unblock();
                        /* make popup */
                        $('#mvx_shipping_method_edit_container #method_id_selected').val(methodId);
                        $('#mvx_shipping_method_edit_container #instance_id_selected').val(instanceId);
                        $('#mvx_shipping_method_edit_container #zone_id_selected').val(zoneId);
                        $('#shipping-form-fields').html(response.settings_html);
                        $('#mvx_shipping_method_edit_container').show();
                    }
                },
            });
        },

        updateShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var methodID = $('#mvx_shipping_method_edit_container #method_id_selected').val(),
                    instanceId = $('#mvx_shipping_method_edit_container #instance_id_selected').val(),
                    zoneId = $('#zone_id').val(),
                    data = {
                        action: 'mvx-update-shipping-method',
                        zoneID: zoneId,
                        security: mvx_vendor_shipping_script_data.security,
                        args: {
                            instance_id: instanceId,
                            zone_id: zoneId,
                            method_id: methodID,
                            settings: $('#mvx-vendor-edit-shipping-form').serializeArray()
                        }
                    };

            $('#mvx_shipping_method_edit_button').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            // $('#mvx_settings_save_button').click();

            var ajaxRequest = $.ajax({
                method: 'POST',
                url: mvx_vendor_shipping_script_data.ajaxurl,
                data: data,
                success: function (response) {
                    if (response.success) {
                        appObj.modifyShippingMethods(undefined, zoneId);
                    } else {
                        alert(response.data);
                    }
                },
            });
        },

        deleteShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            if (confirm(mvx_vendor_shipping_script_data.i18n.deleteShippingMethodConfirmation)) {
                var currentTarget = $(event.target).is(this.delete_shipping_method) ? event.target : $(event.target).closest(this.delete_shipping_method),
                        instance_id = $(currentTarget).parents('.edit_del_actions').attr('data-instance_id'),
                        zoneId = $('#zone_id').val();
                var data = data = {
                    action: 'mvx-delete-shipping-method',
                    zoneID: zoneId,
                    instance_id: instance_id,
                    security: mvx_vendor_shipping_script_data.security
                };

                if (zoneId == '') {
                    // alert( mvx_dashboard_messages.shiping_zone_not_found );
                } else if (instance_id == '') {
                    // alert( mvx_dashboard_messages.shiping_method_not_found );
                } else {
                    // $('#mvx_settings_save_button').click();

                    var ajaxRequest = $.ajax({
                        method: 'post',
                        url: mvx_vendor_shipping_script_data.ajaxurl,
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                appObj.modifyShippingMethods(undefined, zoneId);
                            } else {
                                alert(resp.data);
                            }
                        },
                    });
                }
            }
        },

        limitZoneLocation: function (event) {
            if ($('#limit_zone_location').is(':checked')) {
                $('.hide_if_zone_not_limited').show();
                $('#select_zone_states').select2();
            } else {
                $('.hide_if_zone_not_limited').hide();
            }
        },

        toggleShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var checked = $(event.target).is(':checked'),
                    value = $(event.target).val(),
                    zoneId = $('#zone_id').val();

            var data = {
                action: 'mvx-toggle-shipping-method',
                zoneID: zoneId,
                instance_id: value,
                checked: checked,
                security: mvx_vendor_shipping_script_data.security
            };

            if (zoneId == '') {
                // alert( mvx_dashboard_messages.shiping_zone_not_found );
            } else if (value == '') {
                // alert( mvx_dashboard_messages.shiping_method_not_found );
            } else {
                $('.mvx-container').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: mvx_vendor_shipping_script_data.ajaxurl,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('.mvx-container').unblock();
                        } else {
                            $('.mvx-container').unblock();
                            alert(response.data);
                        }
                    },
                });
            }
        },

        closeModal: function (event) {
            event.preventDefault();

            var appObj = this;

            var modalDialog = $(event.target).parents(appObj.modal_dialog);

            if (modalDialog.length) {
                modalDialog.hide();
                $(this.modal_dialog).hide();
            }
        },
    };

    $(app.build.bind(app));

    // Distance and country by shipping
    $('#shipping-options').change(function () {
        var shipping_option = $(this).val();
        if (shipping_option == 'distance_by_shipping') {
            $('#mvx-vendor-shipping-by-distance-section').show();
            $('#mvx-vendor-shipping-by-zone-section').hide();
            $('#mvx-vendor-shipping-by-country-section').hide();
        } else if (shipping_option == 'distance_by_zone') {
            $('#mvx-vendor-shipping-by-distance-section').hide();
            $('#mvx-vendor-shipping-by-zone-section').show();
            $('#mvx-vendor-shipping-by-country-section').hide();
        } else if (shipping_option == 'shipping_by_country') {
            $('#mvx-vendor-shipping-by-distance-section').hide();
            $('#mvx-vendor-shipping-by-country-section').show();
            $('#mvx-vendor-shipping-by-zone-section').hide();
        } else {}
    });

    if ($('#shipping-options').val() == 'distance_by_shipping') {
        $('#mvx-vendor-shipping-by-distance-section').show();
        $('#mvx-vendor-shipping-by-zone-section').hide();
        $('#mvx-vendor-shipping-by-country-section').hide();
    } else if ($('#shipping-options').val() == 'distance_by_zone') {
        $('#mvx-vendor-shipping-by-distance-section').hide();
        $('#mvx-vendor-shipping-by-zone-section').show();
        $('#mvx-vendor-shipping-by-country-section').hide();
    } else if ($('#shipping-options').val() == 'shipping_by_country') {
        $('#mvx-vendor-shipping-by-distance-section').hide();
        $('#mvx-vendor-shipping-by-zone-section').hide();
        $('#mvx-vendor-shipping-by-country-section').show();
    } else {}

    $('.mvx_country_to_select').select2();
    $('.mvx-select').select2();
    $('.mvx_country_to_select').each(function() {
      $(this).change(function() {
        setStateBoxforCountry( $(this) );
      }).change();
    });

    setTimeout(function() {
        $('#mvx_shipping_rates').children('.multi_input_block').children('.add_multi_input_block').click(function() {
            $('#mvx_shipping_rates').children('.multi_input_block:last').find('.mvx_country_to_select').select2();
            $('#mvx_shipping_rates').children('.multi_input_block:last').find('.mvx_country_to_select').change(function() {
                setStateBoxforCountry( $(this) );
            }).change();
        });
    }, 2000 );
    function setStateBoxforCountry( countryBox ) {
        var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
                states = $.parseJSON( states_json ),
                country = countryBox.val();

        if ( states[ country ] ) {
            if ( $.isEmptyObject( states[ country ] ) ) {
                countryBox.parent().find('.mvx_state_to_select').each(function() {
                    $statebox = $(this);
                    $statebox_id = $statebox.attr('id');
                    $statebox_name = $statebox.attr('name');
                    $statebox_val = $statebox.val();
                    if ( $statebox_val === null ) $statebox_val = '';
                    $statebox_dataname = $statebox.data('name');
                    
                    if ( $statebox.is( 'select' ) ) {
                        $statebox.replaceWith( '<input type="text" name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" value="'+$statebox_val+'" class="mvx-text mvx_state_to_select multi_input_block_element" />' );
                    }
                });
            } else {
                input_selected_state = '';
                var options = '',
                        state = states[ country ];
                countryBox.parent().find('.mvx_state_to_select').each(function() {

                    $statebox = $(this);
                    $statebox_id = $statebox.attr('id');
                    $statebox_name = $statebox.attr('name');
                    $statebox_val = $statebox.val();
                    if ( $statebox_val === null ) $statebox_val = '';
                    $statebox_dataname = $statebox.data('name');
                    
                    for ( var index in state ) {
                        if ( state.hasOwnProperty( index ) ) {
                            if ( $statebox_val ) {
                                if ( $statebox_val == index ) {
                                    var selected_value = 'selected="selected"';
                                } else {
                                    var selected_value = '';
                                }
                            }
                            options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
                        }
                    }
                                            

                    if ( $statebox.is( 'select' ) ) {
                        $statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option><optgroup label="-------------------------------------"><option value="everywhere">'+ mvx_vendor_shipping_script_data.everywhere_else_option +'</option></optgroup><optgroup label="-------------------------------------">' + options + '</optgroup>' );
                    }
                    if ( $statebox.is( 'input' ) ) {
                        $statebox.replaceWith( '<select name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" class="mvx-select mvx_state_to_select multi_input_block_element"></select>' );
                        $statebox = $('#'+$statebox_id);
                        $statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option><optgroup label="-------------------------------------"><option value="everywhere">'+ mvx_vendor_shipping_script_data.everywhere_else_option +'</option></optgroup><optgroup label="-------------------------------------">' + options + '</optgroup>' );
                    }
                    $statebox.val( $statebox_val );
                });
            }
        } else {
            countryBox.parent().find('.mvx_state_to_select').each(function() {
                $statebox = $(this);
                $statebox_id = $statebox.attr('id');
                $statebox_name = $statebox.attr('name');
                $statebox_val = $statebox.val();
                if ( $statebox_val === null ) $statebox_val = '';
                $statebox_dataname = $statebox.data('name');
                
                if ( $statebox.is( 'select' ) ) {
                    $statebox.replaceWith( '<input type="text" name="'+$statebox_name+'" id="'+$statebox_id+'" data-name="'+$statebox_dataname+'" value="'+$statebox_val+'" class="mvx-text mvx_state_to_select multi_input_block_element" />' );
                }
            });
        }
    }

    $('.multi_input_holder').each(function() {
      var multi_input_holder = $(this);
      addMultiInputProperty(multi_input_holder);
    });
    
    function addMultiInputProperty(multi_input_holder) {
        var multi_input_limit = multi_input_holder.data('limit');
        if ( typeof multi_input_limit == 'undefined' ) multi_input_limit = -1;
        if (multi_input_holder.children('.multi_input_block').length == 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
        if ( multi_input_holder.children('.multi_input_block').length == multi_input_limit )  multi_input_holder.find('.add_multi_input_block').hide();
        else multi_input_holder.find('.add_multi_input_block').show();
          multi_input_holder.children('.multi_input_block').each(function() {
            if ($(this)[0] != multi_input_holder.children('.multi_input_block:last')[0]) {
                $(this).children('.add_multi_input_block').remove();
            }
            $(this).children('.add_multi_input_block').attr( 'title', mvx_vendor_shipping_script_data.mvx_multiblick_addnew_help );
            $(this).children('.remove_multi_input_block').attr( 'title', mvx_vendor_shipping_script_data.mvx_multiblick_remove_help );
        });
    
        multi_input_holder.children('.multi_input_block').children('.add_multi_input_block').off('click').on('click', function() {
          var holder_id = multi_input_holder.attr('id');
          var holder_name = multi_input_holder.data('name');
          var multi_input_blockCount = multi_input_holder.data('length');
          multi_input_blockCount++;
          var multi_input_blockEle = multi_input_holder.children('.multi_input_block:first').clone(false);
          
          multi_input_blockEle.find('textarea,input:not(input[type=button],input[type=submit],input[type=checkbox],input[type=radio])').val('');
          multi_input_blockEle.find('input[type=checkbox]').attr('checked', false);
          multi_input_blockEle.find('.select2-container').remove();
          multi_input_blockEle.find('select').select2();
          multi_input_blockEle.find('select').select2('destroy');
          multi_input_blockEle.children('.multi_input_block_element:not(.multi_input_holder)').each(function () {
            var ele = $(this);
            var ele_name = ele.data('name');
            if (ele.hasClass('mvx-wp-fields-uploader')) {} else if (ele.hasClass('wp-picker-container')) {} else {
                ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
                ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
            }
          });
          // Nested multi-input block property
          multi_input_blockEle.children('.multi_input_holder').each(function() {
            setNestedMultiInputIndex($(this), holder_id, holder_name, multi_input_blockCount);
          });
            multi_input_blockEle.children('.remove_multi_input_block').off('click').on('click', function() {
                var rconfirm = confirm(mvx_vendor_shipping_script_data.multiblock_delete_confirm);
                if (rconfirm) {
                    var remove_ele_parent = $(this).parent().parent();
                    var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
                    $(this).parent().remove();
                    remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
                    remove_ele_parent.children('.multi_input_block:last').append(addEle);
                    if ( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
                    else remove_ele_parent.find('.add_multi_input_block').show();
                    if (remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
                }
            });
          
          multi_input_blockEle.children('.add_multi_input_block').remove();
          multi_input_holder.append(multi_input_blockEle);
          multi_input_holder.children('.multi_input_block:last').append($(this));
          if (multi_input_holder.children('.multi_input_block').length > 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'block');
          if ( multi_input_holder.children('.multi_input_block').length == multi_input_limit ) multi_input_holder.find('.add_multi_input_block').hide();
          else multi_input_holder.find('.add_multi_input_block').show();
          multi_input_holder.data('length', multi_input_blockCount);
          
                // Fields Type Property
                multi_input_holder.find('.field_type_options').each(function() {
                    $(this).off('change').on('change', function() {
                        $(this).parent().find('.field_type_select_options').hide();
                        $(this).parent().find('.field_type_html_options').hide();
                        if ( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
                        else if ( $(this).val() == 'mselect' ) $(this).parent().find('.field_type_select_options').show();
                        else if ( $(this).val() == 'dropdown' ) $(this).parent().find('.field_type_select_options').show();
                        else if ( $(this).val() == 'html' ) $(this).parent().find('.field_type_html_options').show();
                    } ).change();
                } );
                
                // Group Name
                multi_input_holder.find('.custom_field_is_group').each( function() {
                    $(this).change( function() {
                        if ( $(this).is(':checked') ) {
                            $(this).parent().find('.custom_field_is_group_name').css('visibility', 'visible');
                        } else {
                            $(this).parent().find('.custom_field_is_group_name').css('visibility', 'hidden');
                        }
                    } ).change();
                } );
        });
    
        if (!multi_input_holder.hasClass('multi_input_block_element')) {
                //multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
            }
            if (multi_input_holder.children('.multi_input_block').children('.multi_input_holder').length > 0) {
                //multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
            }
        
        multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').off('click').on('click', function() {
            var rconfirm = confirm(mvx_vendor_shipping_script_data.multiblock_delete_confirm);
                if (rconfirm) {
                    var remove_ele_parent = $(this).parent().parent();
                    var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
                    $(this).parent().remove();
                    remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
                    remove_ele_parent.children('.multi_input_block:last').append(addEle);
                    if (remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
                    if ( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
                    else remove_ele_parent.find('.add_multi_input_block').show();
                }
        });
    
        // Fields Type Property
        multi_input_holder.find('.field_type_options').each(function() {
            $(this).off('change').on('change', function() {
                $(this).parent().find('.field_type_select_options').hide();
                $(this).parent().find('.field_type_html_options').hide();
                if ( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
                else if ( $(this).val() == 'mselect' ) $(this).parent().find('.field_type_select_options').show();
                else if ( $(this).val() == 'dropdown' ) $(this).parent().find('.field_type_select_options').show();
                else if ( $(this).val() == 'html' ) $(this).parent().find('.field_type_html_options').show();
            } ).change();
        } );
        
        // Group Name
        multi_input_holder.find('.custom_field_is_group').each( function() {
            $(this).change( function() {
                if ( $(this).is(':checked') ) {
                    $(this).parent().find('.custom_field_is_group_name').css('visibility', 'visible');
                } else {
                    $(this).parent().find('.custom_field_is_group_name').css('visibility', 'hidden');
                }
            } ).change();
        } );
    }

    function setNestedMultiInputIndex(nested_multi_input, holder_id, holder_name, multi_input_blockCount) {
        nested_multi_input.children('.multi_input_block:not(:last)').remove();
        var multi_input_id = nested_multi_input.attr('id');
        multi_input_id = multi_input_id.replace(holder_id + '_', '');

        var multi_input_id_splited = multi_input_id.split('_');
        var multi_input_name = '';        

        for(var i = 0; i < (multi_input_id_splited.length -1); i++) {
            if (multi_input_name != '') multi_input_name += '_';
            multi_input_name += multi_input_id_splited[i];
        }
        nested_multi_input.attr('data-name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']');
        nested_multi_input.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount);
        var nested_multi_input_block_count = 0;
        nested_multi_input.children('.multi_input_block').children('.multi_input_block_element:not(.multi_input_holder)').each(function() {
            var ele = $(this);
            var ele_name = ele.data('name');
            if (ele.hasClass('mvx-wp-fields-uploader')) {} else {
                var multiple = ele.attr('multiple');
                if (typeof multiple !== typeof undefined && multiple !== false) {
                    ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']['+nested_multi_input_block_count+']['+ele_name+'][]');
                } else {
                    ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']['+nested_multi_input_block_count+']['+ele_name+']');
                }
                ele.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount + '_' + ele_name + '_' + nested_multi_input_block_count);
            }
        });

        addMultiInputProperty(nested_multi_input);

        if (nested_multi_input.children('.multi_input_block').children('.multi_input_holder').length > 0) nested_multi_input.children('.multi_input_block').css('padding-bottom', '40px');

        nested_multi_input.children('.multi_input_block').children('.multi_input_holder').each(function() {
            setNestedMultiInputIndex($(this), holder_id+'_'+multi_input_name+'_0', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']', 0);
        });
    }

})(jQuery);