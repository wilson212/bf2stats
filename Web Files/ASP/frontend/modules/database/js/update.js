;(function ($, window, document) {

    $(document).ready(function () {

        // Modal forms
        //noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#ajax-dialog").dialog({
                autoOpen: false,
                title: "Clearing Database",
                modal: true,
                width: "640",
                resizable: false,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                }
            });
        }

        $("#update").on('click', function(){
            // Show dialog form
            $("#ajax-dialog").dialog("option", { modal: true, position: 'center center' }).dialog("open");

            // Disable button
            $("#update").prop('disabled', true);

            // Tell the backend to perform the backup
            $.post( "/ASP/database/update", { action: "update", ajax: true })
                .done(function( data ) {
                    // Parse response
                    var result = jQuery.parseJSON(data);
                    if (result.success === false) {
                        $('#initial-message').hide();

                        $('#update-failed').show();
                        $('#fail-message').html(result.message);
                    }
                    else {
                        $('#initial-message').hide();
                        $('#update-success').show();
                    }

                    // Close dialog
                    $("#ajax-dialog").dialog("close");
                })
                .fail(function( jqXHR ) {
                    var result = jQuery.parseJSON(jqXHR.responseText);
                    if (result != null)
                    {
                        $('#initial-message').hide();

                        $('#update-failed').show();
                        $('#fail-message').html(result.message);
                    }
                    else
                    {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html("An Error Occurred. Please check the ASP error log for details.")
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
                    }

                    // Close dialog
                    $("#ajax-dialog").dialog("close");
                });
        });

        $('#button-to-home').on('click', function(event) {
            event.preventDefault();
            var url = $(this).data('target');
            location.replace(url);
        });

        $('#button-to-start-over').on('click', function(event) {
            event.preventDefault();
            var url = $(this).data('target');
            location.replace(url);
        });

    });
})(jQuery, window, document);