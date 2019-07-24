jQuery(document).ready(function($) {
	$('.upload_button').click(function() {
		var send_attachment_at = wp.media.editor.send.attachment, 
		button = $(this);

		wp.media.editor.send.attachment = function(props, attachment) {
			button.prev('input').val(attachment.id);
			button.siblings('.img-preview').children('img').attr('src', attachment.sizes.medium.url); 
			$('#submit_options_form').trigger('click');
				
			wp.media.editor.send.attachment = send_attachment_at;
		}

		wp.media.editor.open(button);
		
		return false;       
		
	});
	
}); // end jquery