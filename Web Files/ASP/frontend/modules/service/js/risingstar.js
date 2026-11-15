;(function ($, window, document, undefined) {

    $(document).ready(function () {

        // Create our base loading modal
        var model = $("#ajax-dialog").dialog({
            autoOpen: false,
            title: "Processing Rising Star",
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
        model.parent().find(".ui-dialog-buttonset").hide();
        //Modal.parent().find(".ui-dialog-buttonset .ui-button-text:eq(0)").text("Close Window");

        // Bind the Test Button button to an action
        $("#test-config").click(function() {
            // Open the Modal Window
            model.dialog("option", {
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
                url: '/ASP/service/cron',
                data: { action : 'risingstar', ajax: true },
                dataType: "json",
                timeout: 60000, // in milliseconds
                success: function(result)
                {
                    //var result = jQuery.parseJSON(response);
                    var message = '';

                    // Create our message!
                    if(result.success === true)
                    {
                        message = '<div class="alert success">Rising Star leaderboard has been successfully refreshed.</div><br />';
                    }
                    else
                    {
                        message = '<div class="alert error">There was an error refreshing the Rising Star leaderboard!' + result.message + '</div><br />';
                    }
                    // Create our button
                    var button = '<br /><br /><div style="text-align: center;"><input id="refresh" type="button" class="btn btn-danger" value="Refresh Window" onClick="window.location.reload();"/></div>';

                    $('.mws-panel-content').html(message + button);
                    model.dialog('close');
                },
                error: function()
                {
                    $('.mws-dialog-inner').html('<span style="color: red; ">There was an error refreshing the Rising Star leaderboard!</span>');
                    model.parent().find(".ui-dialog-buttonset").show();
                }
            });
        });

        // Data Tables
        var table = $(".mws-datatable-fn").DataTable({
            pageLength: 25,
            pagingType: "full_numbers",
            processing: false,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "/ASP/service/list",
                type: "POST",
                data: function ( d ) {
                    return $.extend( {}, d, {
                        ajax: true,
                        action: 'risingstar'
                    });
                },
                beforeSend: function() {
                    $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/loading.gif)');
                },
                complete: function(jqXHR, textStatus) {
                    if (textStatus === "success")
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/tick-circle.png)');
                    else
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/cross-circle.png)');
                }
            },
            order: [[ 1, "asc" ]], // Order by global score
            columns: [
                { "data": "check" },
                { "data": "position" },
                { "data": "id" },
                { "data": "rank" },
                { "data": "name" },
                { "data": "country" },
                { "data": "score" },
                { "data": "joined" },
                { "data": "actions" }
            ],
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 0 },
                { "searchable": false, "targets": 1 },
                { "searchable": false, "targets": 2 },
                { "searchable": false, "orderable": false, "targets": 3 },
                { "searchable": true, "targets": 4 },
                { "searchable": false, "targets": 5 },
                { "searchable": false, "targets": 6 },
                { "searchable": false, "targets": 7 },
                { "searchable": false, "orderable": false, "targets": 8 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

    });
})(jQuery, window, document);