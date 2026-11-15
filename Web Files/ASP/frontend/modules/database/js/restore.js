;(function ($, window, document) {

    $(document).ready(function () {

        // Modal forms
        //noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#ajax-dialog").dialog({
                autoOpen: false,
                title: "Performing Restoration",
                modal: true,
                width: "640",
                resizable: false,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                }
            });
        }

        // ===============================================
        // bind the Config form using 'ajaxForm'
        var restoreForm = $('#restore-form').ajaxForm({
            beforeSubmit: function () {
                // Show dialog form
                $("#ajax-dialog").dialog("option", { modal: true, position: 'center center' }).dialog("open");
                return true;
            },
            success: function (response) {
                // Parse response
                var result = jQuery.parseJSON(response);
                if (result.success === false) {
                    $('#jui-global-message')
                        .attr('class', 'alert error')
                        .html(result.message)
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);
                }
                else {
                    $('#jui-global-message')
                        .attr('class', 'alert success')
                        .html(result.message)
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);
                }

                // Close dialog
                $("#ajax-dialog").dialog("close");
            },
            error: function(request) {
                $('#jui-global-message')
                    .attr('class', 'alert success')
                    .html(request.responseText)
                    .append('<span class="close-bt"></span>')
                    .slideDown(500);
            },
            timeout: 5000
        });

        $("#restore").on('click', function(){
            // Disable button
            $("#restore").prop('disabled', true);
            restoreForm.submit();
        });
    });
})(jQuery, window, document);