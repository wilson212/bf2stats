;(function ($, window, document) {

    $(document).ready(function () {

        // Globals
        var snapshotIds;

        // Create data table
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: false,
            bPaginate: false,
            autoWidth: false
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
            $.post( "/ASP/snapshots/delete", { action: "delete", category: "failed", snapshots: snapshotIds, ajax: true })
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

        // Refresh Click
        $("#refresh").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Reload page (temporary).
            location.reload();

            // Just to be sure, older IE's needs this
            return false;
        });

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {

            // Extract the server ID
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            var id = sid[sid.length-1];

            // If action is "go", then let the link direct the user
            if (action === "view") {
                return;
            }
            else {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Push the request
                $.post( "/ASP/snapshots/delete", { ajax: true, action: "delete", category: "failed", snapshots: [id] })
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
                            // Remove row
                            Table.row( $('#snapshot-' + id) ).remove().draw();
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
            }

            // Just to be sure, older IE's needs this
            return false;
        });

    });
})(jQuery, window, document);