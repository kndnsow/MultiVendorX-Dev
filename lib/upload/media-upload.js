var _custom_media = true;
//_orig_send_attachment = wp.media.editor.send.attachment;

jQuery(document).ready(function($){
  $('.dc-wp-fields-uploader').each(function() {
  	addDCUploaderProperty($(this));	
  });
  $('.add_media').on('click', function(){
    _custom_media = false;
  });
});

function addDCUploaderProperty(dcuploader) {
	dcuploader.find('img').each(function() {
	  var src = jQuery(this).attr('src');
	  if(src.length == 0) jQuery(this).hide();
	});
	
	dcuploader.find('.upload_button').click(function(e) {
		//var send_attachment_bkp = wp.media.editor.send.attachment;
		var mediaUploader;
		var button = jQuery(this);
		var mime = jQuery(this).data('mime');
		var id = button.attr('id').replace('_button', '');
		_custom_media = true;
		
		e.preventDefault();
		
    // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: media_upload_params.media_title,
      button: {
      text: media_upload_params.media_title
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
      var attachment = mediaUploader.state().get('selection').first().toJSON();
      if(mime  == 'image') {
				jQuery("#"+id+'_display').attr('src', attachment.url).removeClass('placeHolder').show();
				if(jQuery("#"+id+'_preview').length > 0)
					jQuery("#"+id+'_preview').attr('src', attachment.url);
			} else {
				jQuery("#"+id+'_display').attr('href', attachment.url);
				//if(attachment.icon) jQuery("#"+id+'_display span').css('background', 'url("'+attachment.icon+'")').css('width', '48px').css('height', '64px');
			}
			jQuery("#"+id+'_display span').show();
			jQuery("#"+id).val(attachment.url);
			jQuery("#"+id).hide();
			button.hide();
			jQuery("#"+id+'_remove_button').show();
    });
    // Open the uploader dialog
    mediaUploader.open();
		
		
		/*wp.media.editor.send.attachment = function(props, attachment) {
			//console.log(JSON.stringify(props) +":"+ JSON.stringify(attachment));
			if ( _custom_media ) {
				if(mime  == 'image') {
					jQuery("#"+id+'_display').attr('src', attachment.url).removeClass('placeHolder').show();
					if(jQuery("#"+id+'_preview').length > 0)
						jQuery("#"+id+'_preview').attr('src', attachment.url);
				} else {
					jQuery("#"+id+'_display').attr('href', attachment.url);
					//if(attachment.icon) jQuery("#"+id+'_display span').css('background', 'url("'+attachment.icon+'")').css('width', '48px').css('height', '64px');
				}
				jQuery("#"+id+'_display span').show();
				jQuery("#"+id).val(attachment.url);
				jQuery("#"+id).hide();
				button.hide();
				jQuery("#"+id+'_remove_button').show();
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}
		wp.media.editor.open(button);*/
		
		return false;
	});
	
	dcuploader.find('.remove_button').each(function() {
		var button = jQuery(this);
		var mime = jQuery(this).data('mime');
		var id = button.attr('id').replace('_remove_button', '');
		if(mime == 'image')
			var attachment_url = jQuery("#"+id+'_display').attr('src');
		else
			var attachment_url = jQuery("#"+id+'_display').attr('href');
		if(!attachment_url || attachment_url.length == 0) {
			button.hide();
			jQuery("#"+id+'_display span').hide();
		} else {
			jQuery("#"+id+'_button').hide();
		}
		button.click(function(e) {
			id = jQuery(this).attr('id').replace('_remove_button', '');
			if(mime == 'image') {
				jQuery("#"+id+'_display').attr('src', '').addClass('placeHolder').hide();
				jQuery("#"+id+'_preview').attr('src', '');
			} else {
				jQuery("#"+id+'_display').attr('href', '#');
			}
			jQuery("#"+id+'_display span').hide();
			jQuery("#"+id).val('');
			jQuery(this).hide();
			jQuery("#"+id+'_button').show();
			return false;
		});
	});
}