;(function ($, window, document, undefined) {

    $(document).ready(function () {

        // Variables
        var playerId = parseInt($("#playerCurrentId").html());
        var playerRank = parseInt($("#playerCurrentRank").html());
        var playerName = $("#changeableName").html();
        var playerEmail = $("#playerCurrentEmail").html();
        var playerIso = $("#playerCurrentIso").html();
        var labelStatus = $("#labelStatus").html();

        function getCountryName() {
            var element = $("#country").find("option[value='" + playerIso + "']");
            return ( element.val() === undefined) ? "Unknown" : element.text();
        }

        // DataTables options for the majority of tables here
        var options = {
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            order: [[ 1, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": 0 }
            ]
        };

        // Set country name
        $("#fullCountryName").html(getCountryName());

        // Vehicle Data Table
        $("#vehicleData").DataTable(options).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Kit Data Table
        $("#kitData").DataTable(options).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Weapon Data Table
        $("#weaponData").DataTable(options).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Army Data Table
        $("#armyData").DataTable(options).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Map Data Table
        $("#mapData").DataTable(options).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Server Data Table
        $("#serverData").DataTable({
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            order: [[ 2, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Buttonset
        if( $.fn.button ) {
            $("#mws-ui-button-radio").buttonset();
        }

        // Modal forms
        // noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#edit-player-form").dialog({
                autoOpen: false,
                title: "Update Player",
                modal: true,
                width: "640",
                resizable: false,
                buttons: [{
                    id: "form-submit-btn",
                    text: "Submit",
                    click: function () {
                        $(this).find('form#mws-validate').submit();
                    }
                }]
            });

            $("#mws-jui-dialog").dialog({
                autoOpen: false,
                title: "Confirm Delete Award",
                modal: true,
                width: "640",
                resizable: false
            });

            // Add New Server Click
            $("#edit-player").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $("#jui-message").hide();

                // Set form default values
                $('input[name="playerName"]').val(playerName);
                $('input[name="playerEmail"]').val(playerEmail);
                $("#rankSelect").val(playerRank);
                $("select.mws-select2").val(playerIso).change();

                // Show dialog form
                $("#edit-player-form").dialog("option", {
                    modal: true,
                    title: "Update Player"
                }).dialog("open");

                // Just to be sure, older IE's needs this
                return false;
            });
        }

        // Ajax and form Validation
        //noinspection JSJQueryEfficiency
        $("#mws-validate").validate({
            rules: {
                playerName: {
                    required: true,
                    minlength: 3,
                    maxlength: 32
                },
                playerEmail: {
                    required: true,
                    email: true, //for validate email
                    maxlength: 64
                }
            },
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error").html(message).show();
                    $('#jui-message').hide();
                } else {
                    $("#mws-validate-error").hide();
                }
            }
        });

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#mws-validate").ajaxForm({
            data: {
                ajax: true,
                playerId: playerId
            },
            beforeSubmit: function () {
                $('#jui-message').attr('class', 'alert loading').html("Submitting form data...").slideDown(200);
                $('#form-submit-btn').prop("disabled", true);
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {
                    // Update variables
                    playerName = result.name;
                    playerRank = result.rank_id;
                    playerIso = result.iso;
                    playerEmail = result.email;

                    // Update html
                    $("#playerCurrentRank").html(result.rank_id);
                    $("#playerCurrentName").html(result.name);
                    $("#playerCurrentEmail").html(result.email);
                    $("#playerCurrentIso").html(result.iso);

                    $("#changeableName").html(result.name);
                    $("#changeableRank").html(result.rankName);
                    $("#fullCountryName").html(getCountryName());
                    $("#rankIcon").attr('src', "/ASP/frontend/images/ranks/rank_" + result.rank_id + ".gif");
                    $("#flag").attr('src', "/ASP/frontend/images/flags/" + result.iso + ".png");

                    // Close dialog
                    $("#edit-player-form").dialog("close");
                }
                else {
                    $('#jui-message').attr('class', 'alert error').html(result.message).slideDown(500);
                }
            },
            error: function() {
                $('#jui-message').attr('class', 'alert error').html('AJAX Error! Please check the console log.').slideDown(500);
            },
            complete: function () {
                $('#form-submit-btn').prop("disabled", false);
            },
            timeout: 5000
        });

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Enable popovers
        $("[rel=popover]").popover({html: true});

        // jQuery-UI Tabs
        // noinspection JSUnresolvedVariable
        $.fn.tabs && $(".mws-tabs").tabs();

        // Chosen Select Box Plugin
        // noinspection JSUnresolvedVariable
        $.fn.select2 && $("select.mws-select2").select2();

        // Ban Button Click
        $("#ban-player").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            $.post( "/ASP/players/authorize", { ajax: true, action: "ban", playerId: playerId })
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
                        // Update buttons
                        $("#ban-player").hide();
                        $("#unban-player").show();

                        // Update account status
                        $("#status").html("Banned").attr('class', 'label label-important');
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

        // UnBan Button Click
        $("#unban-player").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            $.post( "/ASP/players/authorize", { ajax: true, action: "unban", playerId: playerId })
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
                        // Update buttons
                        $("#ban-player").show();
                        $("#unban-player").hide();

                        // Update account status
                        if (labelStatus === 'success') {
                            $("#status").html("Online").attr('class', 'label label-success');
                        }
                        else if (labelStatus === 'info') {
                            $("#status").html("Active").attr('class', 'label label-info');
                        }
                        else {
                            $("#status").html("Inactive").attr('class', 'label label-inactive');
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
                });

            // Just to be sure, older IE's needs this
            return false;
        });

        // Reset Stats and Awards Button Click
        $("#reset-stats").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to reset this players stats? This action cannot be undone!')
                .dialog("option", {
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {

                            $.post( "/ASP/players/reset", { ajax: true, action: "stats", playerId: playerId })
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
                                        // Reload window
                                        location.reload();
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

                            // Close dropdown menu
                            $("#dlDropDown").dropdown("toggle");

                            // Close dialog
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

        // Reset Awards Button Click
        $("#reset-awards").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to reset this players awards? This action cannot be undone!')
                .dialog("option", {
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {

                            $.post( "/ASP/players/reset", { ajax: true, action: "awards", playerId: playerId })
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
                                        // Reload window
                                        location.reload();
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

                            // Close dropdown menu
                            $("#dlDropDown").dropdown("toggle");

                            // Close dialog
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

        // Reset Unlocks Button Click
        $("#reset-unlocks").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            $.post( "/ASP/players/reset", { ajax: true, action: "unlocks", playerId: playerId })
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
                        $('#jui-global-message')
                            .attr('class', 'alert success')
                            .html("Player unlocks were successfully reset.")
                            .append('<span class="close-bt"></span>')
                            .slideDown(500);
                    }

                    // Close dropdown menu
                    $("#dlDropDown").dropdown("toggle");
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

                    // Close dropdown menu
                    $("#dlDropDown").dropdown("toggle");
                });

            // Just to be sure, older IE's needs this
            return false;
        });

        ////////////////////////////////////////////
        // CHART DATA
        ///////////////////////////////////////////

        if( $.plot ) {

            var data = [{
                label: "Games Played",
                color: "#c75d7b"
            }];

            var plot = $.plot($("#mws-games-chart"), data, {
                series: {
                    lines: {
                        show: true,
                        fill: true
                    },
                    points: {
                        show: true
                    }
                },
                tooltip: true,
                tooltipOpts: {
                    content: function(label, xval, yval, flotItem){ // expects to pass these arguments
                        return "%s : %y";
                    },
                    defaultTheme: false,
                    cssClass: 'flotTip'
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0
                },
                yaxis: {
                    minTickSize: 1,
                    tickDecimals: 0,
                    min:0
                }
            });
        }

        var $result;
        var $loaded = false;

        // Load graph points
        $.getJSON("/ASP/players/timePlayed/" + playerId, function(result){
            $result  = result;
            $loaded = true;

            //noinspection JSUnresolvedVariable
            plot.setData([{
                label: "Games Played",
                color: "#c75d7b",
                data: result.week.y
            }]);

            plot.getAxes().xaxis.options.min = 0;
            //noinspection JSUnresolvedVariable
            plot.getAxes().xaxis.options.max = result.week.x.length - 1;
            //noinspection JSUnresolvedVariable
            plot.getAxes().xaxis.options.ticks = result.week.x;
            plot.setupGrid();
            plot.draw();
        });

        $('#weekRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    label: "Games Played",
                    color: "#c75d7b",
                    data: $result.week.y
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.week.x.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.week.x;
                plot.setupGrid();
                plot.draw();
            }
        });

        $('#monthRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    label: "Games Played",
                    color: "#c75d7b",
                    data: $result.month.y
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.month.x.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.month.x;
                plot.setupGrid();
                plot.draw();
            }
        });

        $('#yearRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    label: "Games Played",
                    color: "#c75d7b",
                    data: $result.year.y
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.year.x.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.year.x;
                plot.setupGrid();
                plot.draw();
            }
        });

    });
})(jQuery, window, document);