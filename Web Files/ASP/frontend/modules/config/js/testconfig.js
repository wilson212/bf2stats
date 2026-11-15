$(document).ready(function() {
    // Create our base loading modal
    var Modal = $("#ajax-dialog").dialog({
        autoOpen: false,
        title: "Processing System Tests",
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
    $("#test-config").click(function() {
        // Open the Modal Window
        Modal.dialog("option", {
            modal: true,
            open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
            closeOnEscape: false,
            draggable: false,
            resizable: false
        }).dialog("open");

        // Lock the button so we dont click again after errors
        $("#test-config").attr("disabled", true).attr('value', 'Please Refresh Window');

        // Begin the Ajax Request
        $.ajax({
            type: "POST",
            url: '/ASP/config/test',
            data: { action : 'runtests', ajax: true },
            dataType: "json",
            timeout: 30000, // in milliseconds
            success: function(result)
            {
                //var result = jQuery.parseJSON(response);
                var message = '';

                // Create our message!
                if(result.success === true)
                {
                    if(result.warnings === true)
                    {
                        message = '<div class="alert warning">Not all System Checks Passed... But your current configuration is good enough to start recieving stats.</div><br />';
                    }
                    else
                    {
                        message = '<div class="alert success">All System Checks Passed! Your system is ready to start recieving stats.</div><br />';
                    }
                }
                else
                {
                    message = '<div class="alert error">Some System Checks Failed! You need to Look at the list below and try and fix these errors. Most errors can be fixed by '
                        + 'double checking your <a href="/ASP/config">configuration</a>, and fixing read/write access to the ASP directories</div><br />';
                }
                // Create our button
                var button = '<br /><br /><center><input id="refresh" type="button" class="btn btn-danger" value="Refresh Window" onClick="window.location.reload();"/></center>';

                $('.mws-panel-content').html(message + result.html + button);
                Modal.dialog('close');
            },
            error: function()
            {
                $('.mws-dialog-inner').html('<font color="red">There was an error testing the system. Please refresh the page and try again.</font>');
                Modal.parent().find(".ui-dialog-buttonset").show();
            }
        });
    });
});