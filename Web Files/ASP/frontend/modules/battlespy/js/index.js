;(function ($, window, document) {

    $(document).ready(function () {

        $("#mws-jui-dialog").dialog({
            autoOpen: false,
            title: "Confirm Delete Report",
            modal: true,
            width: "640",
            resizable: false
        });

        // Data Tables
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: false
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {
            // Extract the server ID
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            var id = sid[sid.length-1];

            // If action is "go", then let the link direct the user
            if (action === "go") {
                return;
            }

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            if (action === 'delete') {

                // Show dialog form
                $("#mws-jui-dialog")
                    .html('Are you sure you want to delete BattleSpy report #' + id + '?')
                    .dialog("option", {
                        modal: true,
                        buttons: [
                            {
                                text: "Confirm",
                                class: "btn btn-danger",
                                click: function () {
                                    deleteReports([id]);
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: "Cancel",
                                click: function () {
                                    $(this).dialog("close");
                                }
                            }
                        ]
                    }).dialog("open");
            }

            // Just to be sure, older IE's needs this
            return false;
        });

        // Delete Selected Click
        $("#delete-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Get all checked
            var reportIds = getSelectedReports();

            // Is anything selected?
            if (reportIds.length < 1)
                return false;

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to delete the selected BattleSpy Reports?')
                .dialog("option", {
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {
                            deleteReports(reportIds);
                            $(this).dialog("close");
                        }
                    },
                    {
                        text: "Cancel",
                        click: function () {
                            $(this).dialog("close");
                        }
                    }]
                }).dialog("open");

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

        function deleteReports(ids)
        {
            // Push the request
            $.post( "/ASP/battlespy/deleteReports", { action: "delete", reports: ids })
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
                        $.each(ids, function (key, value) {
                            Table.row( $('#tr-report-' + value) ).remove().draw();
                        });
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

        function getSelectedReports()
        {
            return $('input[type=checkbox]:checked').map(function() {
                // Extract the report ID
                var sid = $(this).attr('id').split("-");
                return sid[sid.length-1];
            }).get();
        }

    });
})(jQuery, window, document);