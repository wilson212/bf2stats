$(document).ready(function() {

	// Init our validator!
	$("#restoreForm").validate();
	
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		title: "Restore System Database", 
		modal: true, 
		width: "600",
		buttons: [{
			text: "Close Window", 
			click: function() {
				$( this ).dialog( "close" );
			}
		}]
	});
	
	// Hide our close window button from view unless needed
	Modal.parent().find(".ui-dialog-buttonset").hide();

	// ===============================================
	// bind the Config form using 'ajaxForm' 
	$('#restoreForm').ajaxForm({
		beforeSubmit: function (arr, data, options)
		{
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
			// Open and close tags
			var pre = '<div class="mws-panel-content"><p>';
			var end = '</p></div>';
			
			// Parse the JSON response
			var result = jQuery.parseJSON(response);
			if(result.success == true)
			{
				$('.content').html(pre + '<div class="alert success">' + result.message + '</div></p></div>');
			}
			else
			{
				var button = '<br /><br /><center><input type="button" class="mws-button blue" value="Go Back" onClick="window.location.replace(\'?task=restoredb\');"/></center>';
				$('.content').html(pre + '<div class="alert error">' + result.message + '</div>' + button + end);
			}
			Modal.dialog('close');
		},
		error: function(request, status, err) 
		{
			$('.mws-dialog-inner').html('<font color="red">There was an error testing the system. Please refresh the page and try again.</font>');
			Modal.parent().find(".ui-dialog-buttonset").show();
		},
		timeout: 300000 
	});
});