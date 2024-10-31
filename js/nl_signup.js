
jQuery(document).ready(function(jQuery) {

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery('.nl_subscribe_form').find('input[type="submit"]').click(function() {

		var data = jQuery(this).parent('.nl_subscribe_form').serialize()+'&action=nl_front_submit';
		var current_this = jQuery(this);
		console.log(data);

		jQuery.post(ajaxurl, data, function(response) {
			console.log(response);
			var res = jQuery.parseJSON(response);
			var nl_msg = '';
			console.log(res);
			if (res.success==true) {
				nl_msg = '<p class="success_msg">' + res.msg + '</p>';
				current_this.parent('.nl_subscribe_form').find('input[type="email"]').attr('value', '');
				current_this.parent('.nl_subscribe_form').find('input[type="text"]').attr('value', '');
			} else {
				nl_msg = '<p class="error_msg">' + res.msg + '</p>';
			};
				
			// redirect after 1 sec.
			if (res.redirect==true) {
				setTimeout(function (){
					window.location.reload(true);
				}, 1000);
			};

			if (jQuery(this).parent("#nl_signup_popup")!=undefined) {
				setTimeout(function (){
					jQuery("#lean_overlay").fadeOut(200);
					jQuery("#nl_signup_popup").css({"display":"none"})
				}, 3000);
			};

			if (res.close_popup==true) {
				setTimeout(function (){
					jQuery("#lean_overlay").fadeOut(200);
					jQuery("#nl_signup_popup").css({"display":"none"})
				}, 3000);
			};

			current_this.parent('.nl_subscribe_form').find('.nl_msg_box').html(nl_msg);
		});

		return false;
	});
});