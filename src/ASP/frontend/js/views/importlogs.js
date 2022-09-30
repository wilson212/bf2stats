$(document).ready(function() {
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		title: "Importing System Logs", 
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

	// Bind the Process Buttons actions
    $("#import-up").click(function() {
		process('up');
	});
    $("#import-all").click(function() {
		process('all');
	});
	
	// Main function for importing the logs
	function process(mode)
	{
		// Open the Modal Window
		Modal.dialog("option", {
			modal: true, 
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			closeOnEscape: false, 
			draggable: false,
			resizable: false
		}).dialog("open");
		
		// Lock the buttons so we dont click again after errors
		$("#import-up").attr("disabled", true);
		$("#import-all").attr("disabled", true);
	
		// Begin the Ajax Request
		$.ajax({
            type: "POST",
            url: '?task=importlogs',
            data: { action : 'import', type: mode },
            dataType: "json",
            timeout: 300000, // in milliseconds
            success: function(result) 
            {
				// Create our message!
				if(result.success == true)
				{
					var message = '<div class="alert '+ result.type +'">'+ result.message +'</div><br />';
				}
				else
				{
					var message = '<div class="alert '+ result.type +'">Importing Logs Failed!' + result.message + '</div><br />';
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
    }
});