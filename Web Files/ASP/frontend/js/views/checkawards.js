$(document).ready(function() {
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		title: "Check & Validate Player Awards", 
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
    $("#check-awards").click(function() {
		// Open the Modal Window
		Modal.dialog("option", {
			modal: true, 
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			closeOnEscape: false, 
			draggable: false,
			resizable: false
		}).dialog("open");
		
		// Lock the button so we dont click again after errors
		$("#check-awards").attr("disabled", true).attr('value', 'Please Refresh Window');
	
		// Begin the Ajax Request
		$.ajax({
            type: "POST",
            url: '?task=checkawards',
            data: { action : 'check' },
            dataType: "json",
            timeout: 300000, // in milliseconds
            success: function(result) 
            {
				// Create our message!
				if(result.success == true)
				{
					var message = '<div class="alert success">All Player Backend Awards Validated Successfully! You may see any changes made in the "ASP/system/logs/validate_awards.log" file.</div><br />';
				}
				else
				{
					var message = '<div class="alert error">Checking Awards Failed to Complete! Please check your error log for errors.</div><br />';
				}
				// Create our button
				var button = '<br /><br /><center><input id="refresh" type="button" class="mws-button blue" value="Refresh Window" onClick="window.location.reload();"/></center>';
				
				$('.mws-panel-content').html(message + button);
				Modal.dialog('close');
			},
			error: function(request, status, err) 
            {
                $('.mws-dialog-inner').html('<font color="red">There was an error sending the request. Please refresh the page and try again.</font>');
				Modal.parent().find(".ui-dialog-buttonset").show();
            }
		});
    });
});