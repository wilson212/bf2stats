(function($) {
	$(document).ready(function() {	
		$("#mws-login-form form").validate({
			rules: {
				username: {required: true}, 
				password: {required: true}
			}, 
			errorPlacement: function(error, element) {  
			}, 
			invalidHandler: function(form, validator) {
				if($.fn.effect) {
					$("#mws-login").effect("shake", {distance: 6, times: 2}, 35);
				}
			}
		});
		
		$.fn.placeholder && $('[placeholder]').placeholder();
	});
}) (jQuery);
