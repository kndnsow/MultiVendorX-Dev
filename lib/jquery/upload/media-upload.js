jQuery(document).ready(function ($) {

    $('.dc-wp-fields-uploader .upload_button').click(function (e) {
        var button = $(this);
        var id = button.attr('id').replace('_button', '');
        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            wp.media.editor.open('dc-wp-fields-uploader');
        }
        // backup of original send function
        original_send = wp.media.editor.send.attachment;
        // new send function
        wp.media.editor.send.attachment = function (props, attachment) {
            if (attachment.type === 'image') {
                $("#" + id + '_display').attr('src', attachment.url).removeClass('placeHolder');
                if ($("#" + id + '_preview').length > 0) {
                    $("#" + id + '_preview').attr('src', attachment.url);
                }
            }
            $("#" + id).val(attachment.id);
            $("#" + id).hide();
            button.hide();
            $("#" + id + '_remove_button').show();
            // or whatever you want to do with the data at this point
            // original function makes an ajax call to retrieve the image html tag and does a little more
        };
        // wp.media.send.to.editor will automatically trigger window.send_to_editor for backwards compatibility

        // backup original window.send_to_editor
        window.original_send_to_editor = window.send_to_editor;

        // override window.send_to_editor
        window.send_to_editor = function (html) {
            if (html !== '') { 
                var src = $(html).attr('src');
                if (src.match(/\.(jpeg|jpg|gif|png)$/) != null) {
                    $("#" + id + '_display').attr('src', src).removeClass('placeHolder');
                    if ($("#" + id + '_preview').length > 0) {
                        $("#" + id + '_preview').attr('src', src);
                    }
                }
                $("#" + id).val(src);
                $("#" + id).hide();
                button.hide();
                $("#" + id + '_remove_button').show();
            }
            // html argument might not be useful in this case
            // use the data from var b (attachment) here to make your own ajax call or use data from b and send it back to your defined input fields etc.
        };
    });

    $('.dc-wp-fields-uploader .remove_button').each(function () {
        var button = $(this);
        var id = button.attr('id').replace('_remove_button', '');
        var attachment_url = $("#" + id + '_display').attr('src');
        if (attachment_url.length == 0)
            button.hide();
        else
            $("#" + id + '_button').hide();
        button.click(function (e) { 
            $("#" + id + '_display').attr('src', '').addClass('placeHolder');
            $("#" + id + '_preview').attr('src', '');
            $("#" + id).attr('value', '');
            $("#" + id).val('').show();
            button.hide();
            $("#" + id + '_button').show();
            return false;
        });
    });

//    $('.add_media').on('click', function () {
//        _custom_media = false;
//    });
});