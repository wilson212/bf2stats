;(function ($, window, document, undefined) {

    $(document).ready(function () {

        // Define variables
        var showBots = false;
        var selectedName = '';
        var selectedId = 0;

        // Modal forms
        // noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {

            // Create our base loading modal
            var rebuildDialog = $("#ajax-dialog").dialog({
                autoOpen: false,
                title: "Processing 4-Star General Eligibility list",
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
            rebuildDialog.parent().find(".ui-dialog-buttonset").hide();
            //Modal.parent().find(".ui-dialog-buttonset .ui-button-text:eq(0)").text("Close Window");

            var selectDialog = $("#mws-jui-dialog").dialog({
                autoOpen: false,
                title: "Confirm Selection",
                modal: true,
                width: "640",
                resizable: false,
                buttons: [{
                    text: "Cancel",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                },{
                    id: 'selectPlayerButton',
                    text: "Select Player",
                    click: function() {

                        // Disable this button
                        $(":button:contains('Select Player')").prop("disabled", true).addClass("ui-state-disabled");

                        // Show loading message
                        $('#jui-local-message')
                            .attr('class', 'alert loading')
                            .html('Promoting player...')
                            .slideDown(500);

                        // Begin the Ajax Request
                        $.ajax({
                            type: "POST",
                            url: "/ASP/service/select",
                            data: { ajax: true, action: "general", playerId: selectedId },
                            dataType: "json",
                            timeout: 6000, // in milliseconds
                            success: function(result)
                            {
                                // Create our message!
                                if(result.success === true)
                                {
                                    $('#jui-local-message')
                                        .attr('class', 'alert success')
                                        .html(result.message  + '. Reloading window...');

                                    // Reload window after 2 seconds
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 2000);
                                }
                                else
                                {
                                    $('#jui-global-message')
                                        .attr('class', 'alert error')
                                        .html(result.message);

                                    selectDialog.dialog('close');
                                }
                            },
                            error: function()
                            {
                                $('#jui-global-message')
                                    .attr('class', 'alert error')
                                    .html("An Error Occurred. Please check the ASP error log for details.")
                                    .append('<span class="close-bt"></span>')
                                    .slideDown(500);

                                // Close the dialog
                                selectDialog.dialog('close');
                            }
                        });
                    }
                }]
            });
        }

        // Data Tables
        var Table = $(".mws-datatable-fn").DataTable({
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
                        showBots: (showBots) ? 1 : 0,
                        action: 'general'
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
            order: [[4, "desc"]], // Order by global score
            columns: [
                { "data": "check" },
                { "data": "rank" },
                { "data": "name" },
                { "data": "country" },
                { "data": "score" },
                { "data": "spm" },
                { "data": "rising" },
                { "data": "games" },
                { "data": "seen" },
                { "data": "actions" }
            ],
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 0 },
                { "searchable": false, "targets": 1 },
                { "searchable": true, "orderable": false, "targets": 2 },
                { "searchable": false, "orderable": false, "targets": 3 },
                { "searchable": false, "targets": 4 },
                { "searchable": false, "targets": 5 },
                { "searchable": false, "targets": 6 },
                { "searchable": false, "targets": 7 },
                { "searchable": false, "targets": 8 },
                { "searchable": false, "orderable": false, "targets": 9 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Bind the Test Button button to an action
        $("#rebuild-btn").click(function() {
            // Open the Modal Window
            rebuildDialog.dialog("option", {
                modal: true,
                open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
                closeOnEscape: false,
                draggable: false,
                resizable: false
            }).dialog("open");

            // Lock the button so we dont click again after errors
            $("#rebuild-btn").attr("disabled", true);

            // Begin the Ajax Request
            $.ajax({
                type: "POST",
                url: '/ASP/service/cron',
                data: { action : 'general', ajax: true },
                dataType: "json",
                timeout: 60000, // in milliseconds
                success: function(result)
                {
                    var message = '';
                    var alertStyle = '';

                    // Create our message!
                    if(result.success === true)
                    {
                        alertStyle = 'success';
                        message = 'The 4-Star General Eligibility list has been successfully refreshed.';
                    }
                    else
                    {
                        alertStyle = 'error';
                        message = 'There was an error refreshing the 4-Star General eligibility list! ' + result.message;
                    }

                    // Show alert
                    $('div#jui-global-message')
                        .attr('class', 'alert ' + alertStyle)
                        .html(message)
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);

                    // Close dialog
                    rebuildDialog.dialog('close');

                    // noinspection JSUnresolvedFunction
                    Table.ajax.reload();
                },
                error: function()
                {
                    // Show alert
                    $('div#jui-global-message')
                        .attr('class', 'alert error')
                        .html('There was an error refreshing the 4-Star General eligibility list!')
                        .append('<span class="close-bt"></span>')
                        .slideDown(500);

                    // Close dialog
                    rebuildDialog.dialog('close');
                }
            });
        });

        // Show Bots Button Click
        $("#show-bots").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            var selected = $("#show-bots");

            // The a element does not have a property disabled. So defining one won't
            // affect any event handlers you may have attached to it. Therefore, we use data instead
            if ($(this).data('disabled')) return;

            selected.data('disabled', true);
            showBots = true;

            // noinspection JSUnresolvedFunction
            Table.ajax.reload();

            selected.data('disabled', false);

            // Switch button views
            selected.hide();
            $("#hide-bots").show();

            // Just to be sure, older IE's needs this
            return false;
        });

        // Add New Server Click
        $("#hide-bots").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            var selected = $("#hide-bots");

            // The a element does not have a property disabled. So defining one won't
            // affect any event handlers you may have attached to it. Therefore, we use data instead
            if ($(this).data('disabled')) return;

            selected.data('disabled', true);
            showBots = false;

            // noinspection JSUnresolvedFunction
            Table.ajax.reload();

            selected.data('disabled', false);

            // Switch button views
            selected.hide();
            $("#show-bots").show();

            // Just to be sure, older IE's needs this
            return false;
        });

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {

            // Extract the server ID
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            selectedId = sid[sid.length-1];

            // If action is "go", then let the link direct the user
            if (action === "go") {
                return;
            }
            else {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();
            }

            // Always have the user confirm his action here!
            var tr = $(this).closest('tr');
            selectedName = tr.find('td:eq(2)').html();

            $("span#selectPlayerName").html(selectedName);
            $("span#selectPlayerName").css('color', 'red');

            if (action === 'select') {

                // Verify action!
                selectDialog.dialog("option", { modal: true, position: 'center center' }).dialog("open");
            }

            // Just to be sure, older IE's needs this
            return false;
        });

    });
})(jQuery, window, document);