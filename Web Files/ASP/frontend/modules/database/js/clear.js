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

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#clearDatabase").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function () {
                // Show dialog form
                $("#ajax-dialog").dialog("option", { modal: true, position: 'center center' }).dialog("open");
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {
                    $('#jui-global-message')
                        .attr('class', 'alert success')
                        .html(result.message)
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);
                }
                else {
                    $('#jui-global-message')
                        .attr('class', 'alert error')
                        .html(result.message)
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);
                }

                // Close dialog
                $("#ajax-dialog").dialog("close");
            },
            error: function() {
                $('#jui-message')
                    .attr('class', 'alert error')
                    .html('AJAX Error! Please check the console log.')
                    .append('<span class="close-bt"></span>')
                    .slideDown(500);

                // Close dialog
                $("#ajax-dialog").dialog("close");
            },
            complete: function () {

            },
            timeout: 30000
        });

    });
})(jQuery, window, document);