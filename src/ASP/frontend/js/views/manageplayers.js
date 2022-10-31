$(document).ready(function() {
	// Setup our variables
	var player = null;
	
	// Init players table
	Table = $(".mws-datatable-fn").dataTable({
		sPaginationType: "full_numbers",
		bProcessing: false,
        bServerSide: true,
        sAjaxSource: "?task=manageplayers&ajax=list",
		
	});
	
	// Create our base loading modal
	Modal = $("#ajax-dialog").dialog({
		autoOpen: false, 
		modal: true, 
		width: "600",
	});

    // bind the Players form using 'ajaxForm' 
    $('#mws-validate').ajaxForm({
        beforeSubmit: function (arr, data, options)
        {
            $('#ajax-message').html('<div class="alert loading">Submitting Data...</div>').slideDown(300);
            return true;
        },
        success: post_result,
        timeout: 5000 
    });
	
	$('#edit').live('click', function(e) {
        e.preventDefault();
		pid = this.name;
		player = pid.split('|');
		
		// Begin the Ajax Request to request user information!
		$.ajax({
            type: "POST",
            url: '?task=manageplayers&ajax=player',
            data: { action : 'fetch', id : player[0] },
            dataType: "json",
            timeout: 3000, // in milliseconds
            success: function(result) 
            {
				// Create our message!
				if(result.success == true)
				{
					open_modal(result);
				}
				else
				{
					alert( result.message );
				}
			},
			error: function(request, status, err) 
            {
                alert('There was an error fetching players information. Please refresh the page and try again.');
            }
		});
    });
	
	function open_modal(result)
	{
		// Set form values
		$('#player-id').attr('value', player[0]);
		$('#pid').attr('value', player[0]);
		$('#player-nick').attr('value', player[1]);
		$('#player-clantag').attr('value', result.clantag );
		$('#player-rank').val( result.rank );
		$('#player-ban').val( result.permban );
		$('#player-hidden').val( result.hidden );
		
		// Open the Modal Window
		Modal.dialog("option", {
			modal: true, 
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			title: '<b>' + player[1] + ' (' + player[0] + ')</b>',
			closeOnEscape: false, 
			draggable: false,
			resizable: false,
			buttons: [
				{
					text: "Delete Player", 
					click: function() {
						if( confirm('Are you sure you want to permanetly delete player '+ player[1] + '?') )
						{
							doDelete(player[0]);
						}
					}
				},
				{
					text: "Submit", 
					click: function() {
						$( this ).find('form#mws-validate').submit();
					}
				},
				{
					text: "Cancel", 
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		}).dialog("open");
		
		// Fix button colors
		Modal.parents().find(".ui-dialog-buttonpane button")
		.eq(0).addClass("mws-button red").end()
		.eq(1).addClass("mws-button blue").end()
		.eq(2).addClass("mws-button blue").end();
	}
	
	function doDelete(pid)
	{
		// Slide message
		$('#ajax-message').html('<div class="alert loading">Ssending Request...</div>').slideDown(300);
		
		// Begin the Ajax Request
		$.ajax({
            type: "POST",
            url: '?task=manageplayers&ajax=action',
            data: { action : 'delete', id : pid },
            dataType: "json",
            timeout: 3000, // in milliseconds
            success: function(result) 
            {
				// Create our message!
				if(result.success == true)
				{
					Modal.dialog('close');
					Table.fnDraw();
				}
				else
				{
					$('#ajax-message').html('<div class="alert error">There was an error deleting the player.Please refresh the page and try again.</div>');
					$('#ajax-message').delay(5000).slideUp(300);
				}
			},
			error: function(request, status, err) 
            {
                $('#ajax-message').html('<div class="alert error">There was an error sending the request... Please refresh the page and try again.</div>');
				$('#ajax-message').delay(5000).slideUp(300);
            }
		});
	}
	
	function post_result(response, statusText, xhr, $form)  
    { 
        // Parse the JSON response
        var result = jQuery.parseJSON(response);

		// Create our message!
		if(result.success == true)
		{
			$('#ajax-message').html('<div class="alert success">' + result.message + '</div>');
			$('#ajax-message').delay(5000).slideUp(300);
			Table.fnDraw();
		}
		else
		{
			$('#ajax-message').html('<div class="alert error">' + result.message + '</div>');
			$('#ajax-message').delay(5000).slideUp(300);
		}
	}
});
