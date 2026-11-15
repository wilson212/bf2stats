;(function ($, window, document) {

    $(document).ready(function () {

        // Globals
        var snapshotIds;
        var processIndex = 0;

        // Create data table
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: false,
            bPaginate: false,
            autoWidth: false
        });

        // Modal forms
        // noinspection JSUnresolvedVariable
        $("#ajax-dialog").dialog({
            autoOpen: false,
            title: "Importing Snapshots",
            modal: true,
            width: "640",
            resizable: false,
            closeOnEscape: false,
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            }
        });

        // Delete Selected Click
        $("#delete-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            snapshotIds = [];
            $('input[type=checkbox]:checked').map(function() {
                // Extract the server ID
                var sid = $(this).attr('id').split("-").slice(1).join("-");
                if (sid !== "all")
                    snapshotIds.push(sid);
            });

            // Is anything selected?
            if (snapshotIds.length < 1)
                return false;

            // Process Selected Snapshots
            // Push the request
            $.post( "/ASP/snapshots/delete", { action: "delete", category: "auth", snapshots: snapshotIds, ajax: true })
                .done(function( data ) {

                    // Parse response
                    var result = jQuery.parseJSON(data);
                    if (result.success === false) {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html(result.message)
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
                    }
                    else {
                        // Remove each row
                        $.each(snapshotIds, function (index, value) {
                            Table.row( $('#snapshot-' + value) ).remove().draw();
                        });

                        Table.draw();
                    }
                })
                .fail(function( jqXHR ) {
                    var result = jQuery.parseJSON(jqXHR.responseText);
                    if (result != null)
                    {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html(result.message)
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
                    }
                    else
                    {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html("An Error Occurred. Please check the ASP error log for details.")
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
                    }
                });

            // Just to be sure, older IE's needs this
            return false;
        });

        // Process Selected Click
        $("#accept-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            snapshotIds = [];
            $('input[type=checkbox]:checked').map(function() {
                // Extract the server ID
                var sid = $(this).attr('id').split("-").slice(1).join("-");
                if (sid !== "all")
                    snapshotIds.push(sid);
            });

            // Is anything selected?
            if (snapshotIds.length < 1)
                return false;

            // Update progress
            $("#count").html(snapshotIds.length);

            // Show dialog form
            $("#ajax-dialog").dialog("option", { modal: true, position: 'center center' }).dialog("open");

            // Now it can be used reliably with $.map()
            processIndex = 0;
            processNextSnapshot();

            // Just to be sure, older IE's needs this
            return false;
        });

        // Refresh Click
        $("#refresh").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Reload page (temporary).
            location.reload();

            // Just to be sure, older IE's needs this
            return false;
        });

        function processNextSnapshot()
        {
            var snapshot = snapshotIds[processIndex];
            processIndex += 1;

            // Update progress
            $("#progress").html(processIndex);

            // Process Selected Snapshots
            $.post( "/ASP/snapshots/accept", { action: "process", snapshot: snapshot, ajax: true })
                .done(function( data ) {

                    // Parse response
                    var result = jQuery.parseJSON(data);
                    if (result.success === false) {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html(result.message)
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);

                        // Close dialog
                        $("#ajax-dialog").dialog("close");
                    }
                    else {

                        // Remove each row
                        Table.row( $('#snapshot-' + snapshot) ).remove().draw();

                        if (processIndex < snapshotIds.length) {
                            processNextSnapshot();
                        }
                        else {
                            // Close dialog
                            $("#ajax-dialog").dialog("close");
                        }
                    }
                })
                .fail(function( jqXHR ) {
                    var result = jQuery.parseJSON(jqXHR.responseText);
                    if (result != null)
                    {
                        $('#jui-global-message')
                            .attr('class', 'alert error')
                            .html(result.message)
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
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
        }

    });
})(jQuery, window, document);