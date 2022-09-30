$(document).ready(function() {
    // Init our validator!
    $("#mws-validate").validate();
    
    // Create our table
    $(".mws-datatable-fn").dataTable({sPaginationType: "full_numbers"});
    
    // bind the Config form using 'ajaxForm' 
    $('#mws-validate').ajaxForm({
        beforeSubmit: function (arr, data, options)
        {
            $('#ajax-message').html('<div class="alert loading">Submitting Data...</div>').slideDown(300);
            return true;
        },
        success: post_result,
        timeout: 5000 
    });
    
    // Create our base loading modal
    Modal = $("#ajax-dialog").dialog({
        autoOpen: false, 
        modal: true,
        width: "600",
    });
    
    $('#view').live('click', function(e) {
        e.preventDefault();
        open_modal(this.name);
    });
    
    function open_modal(id)
    {
        // Set our server ID
        $('#server-id').attr('value', id);
        $('#rcon-port').attr('value', 4711);
        $('#rcon-pass').attr('value', '');
        $('#ajax-message').hide();
        
        // Open the Modal Window
        Modal.dialog("option", {
            modal: true, 
            open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
            title: 'Rcon Configuration (Server ID: ' + id +')',
            closeOnEscape: false, 
            draggable: false,
            resizable: false,
            buttons: [
                {
                    text: "Save", 
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
        .eq(0).addClass("mws-button blue").end()
        .eq(1).addClass("mws-button blue").end();
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
            Modal.dialog("option", { 
                buttons: [{
                    text: "Close", 
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }]
            });
            getStatus();
        }
        else
        {
            $('#ajax-message').html('<div class="alert error">' + result.message + '</div>');
            $('#ajax-message').delay(5000).slideUp(300);
            Modal.dialog("option", { 
                buttons: [{
                    text: "Close", 
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }]
            });
        }
    }
    
    function getStatus()
    {
        // Begin the Ajax Request for server status
        $.ajax({
            type: "POST",
            url: '?task=serverinfo',
            data: { action : 'status' },
            dataType: "json",
            timeout: 10000, // in milliseconds
            success: function(result) 
            {
                // Create our message!
                $.each( result.data, function(k, v){
                    $('#status_' + k).html( v );
                });
            }
        });
    }
    
    // Do this now
    getStatus();
    
    // Set Int. to update status every few seconds
    window.setInterval(function(){
      getStatus();
    }, 15000);
});