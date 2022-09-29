$(document).ready(function() {
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		title: "Backup System Database", 
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
	//Modal.parent().find(".ui-dialog-buttonset .ui-button-text:eq(0)").text("Close Window");

	// Bind the Test Button button to an action
    $("#backup").click(function() {

		// Open the Modal Window
		Modal.dialog("option", {
			modal: true, 
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			closeOnEscape: false, 
			draggable: false,
			resizable: false
		}).dialog("open");
		
		// Lock the button so we dont click again after errors
		$("#backup").attr("disabled", true).attr('value', 'Please Refresh Window');
	
		// Begin the Ajax Request
		$.ajax({
			type: "POST",
			url: '?task=backupdb',
			data: { action : 'backup' },
			dataType: "json",
			timeout: 30000, // in milliseconds
			success: function(result) 
			{
				// Create our message!
				if(result.success == true)
				{
					var message = '<div class="alert success">' + result.message + '</div><br />';
				}
				else
				{
					var message = '<div class="alert error">' + result.message + '</div><br />';
				}
				
				$('.mws-panel-content').html(message);
				Modal.dialog('close');
			},
			error: function(request, status, err) 
			{
				$('.mws-dialog-inner').html('<font color="red">There was an error clearing the system database. Please refresh the page and try again.</font>');
				Modal.parent().find(".ui-dialog-buttonset").show();
			}
		});
    });
});