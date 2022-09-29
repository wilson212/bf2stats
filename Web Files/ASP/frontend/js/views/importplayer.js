$(document).ready(function() {

	// Build our modal message
	var Modal_html = '<p><center><img src="frontend/images/core/loading32.gif" /><br /><br />Importing Player from EA Servers... '
		+ 'Please allow up to 30 seconds for this process to complete.<br /><br /><font color="red">DO NOT</font> close '
		+ 'or refresh this window.</center></p>';
	
	// Init our validator!
	$("#importForm").validate();
	
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		title: "Import Player", 
		modal: true, 
		width: "600",
		buttons: [{
			text: "Close Window", 
			click: function() {
				$( this ).dialog( "close" );
			}
		}]
	});
	

	// ===============================================
	// bind the Config form using 'ajaxForm' 
	$('#importForm').ajaxForm({
		beforeSubmit: function (arr, data, options)
		{
			// Hide our close window button from view unless needed and set our html
			Modal.parent().find(".ui-dialog-buttonset").hide();
			$('.mws-dialog-inner').html(Modal_html);

			// Open the Modal Window
			Modal.dialog("option", {
				modal: true, 
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
				closeOnEscape: false, 
				draggable: false,
				resizable: false
			}).dialog("open");
			return true;
		},
		success: function(response, statusText, xhr, $form)  
		{
			// Parse the JSON response
			var result = jQuery.parseJSON(response);
			if(result.success == true)
			{
				$('.mws-dialog-inner').html('<div class="alert success">' + result.message + '</div>');
			}
			else
			{
				$('.mws-dialog-inner').html('<div class="alert error">' + result.message + '</div>');
			}
			Modal.parent().find(".ui-dialog-buttonset").show();
		},
		error: function(request, status, err) 
		{
			$('.mws-dialog-inner').html('<font color="red">There was an error sending the request. Please refresh the page and try again.</font>');
			Modal.parent().find(".ui-dialog-buttonset").show();
		},
		timeout: 300000 
	});
});