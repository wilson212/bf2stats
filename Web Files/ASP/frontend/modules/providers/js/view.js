;(function ($, window, document) {

    $(document).ready(function () {

        // Get server ID
        var providerId = parseInt( $("#providerId").html() );
        var selectedRowNode;

        // Buttons
        $.fn.button && $("#mws-ui-button-radio").buttonset();

        // Spinners
        //noinspection JSUnresolvedVariable
        $.fn.spinner && $('.mws-spinner').spinner();

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Data Tables
        var table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: true,
            order: [[ 0, "asc" ]], // Order by id
            columnDefs: [
                { "searchable": false, "orderable": true, "targets": 0 },
                { "searchable": true, "orderable": false, "targets": 1 },
                { "searchable": false, "orderable": false, "targets": 2 },
                { "searchable": false, "orderable": false, "targets": 3 },
                { "searchable": false, "orderable": false, "targets": 4 },
                { "searchable": false, "orderable": false, "targets": 5 },
                { "searchable": false, "orderable": true, "targets": 6 },
                { "searchable": false, "orderable": true, "targets": 7 },
                { "searchable": false, "orderable": false, "targets": 8 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // -------------------------------------------------------------------------
        // Ajax Forms
        $("#edit-provider-form").dialog({
            autoOpen: false,
            title: "Edit Provider Details",
            modal: true,
            width: "640",
            resizable: false,
            buttons: [{
                id: "form-submit-btn",
                text: "Submit",
                click: function () {
                    $(this).find('form#mws-validate-provider').submit();
                }
            }]
        });

        $("#edit-token-form").dialog({
            autoOpen: false,
            title: "Authorize Token Addresses",
            modal: true,
            width: "500",
            resizable: false,
            buttons: [{
                id: "form-submit-btn2",
                text: "Submit",
                click: function () {
                    $(this).find('form#mws-validate-token').submit();
                }
            }]
        });

        $("#mws-jui-dialog").dialog({
            autoOpen: false,
            title: "Confirm AuthID Change",
            modal: true,
            width: "640",
            resizable: false
        });

        // -------------------------------------------------------------------------
        //noinspection JSJQueryEfficiency
        var validator = $("#mws-validate-provider").validate({
            rules: {
                providerName: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                }
            },
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors === 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error").html(message).show();
                    $('#jui-message').hide();
                } else {
                    $("#mws-validate-error").hide();
                }
            }
        });

        //noinspection JSJQueryEfficiency
        $("#mws-validate-provider").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function () {
                $("#mws-validate-error").hide();
                $('#jui-message').attr('class', 'alert loading').html("Submitting form data...").slideDown(200);
                $('#form-submit-btn').prop("disabled", true);
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {
                    $("span#sName").html(result.providerName);

                    // Close dialog
                    $("#edit-provider-form").dialog("close");
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
            timeout: 15000
        });

        //noinspection JSJQueryEfficiency
        $("#mws-validate-token").ajaxForm({
            data: { ajax: true, addresses: $("select#ips").tagsinput('items') },
            beforeSubmit: function () {
                $("#mws-validate-error").hide();
                $('#jui-message').attr('class', 'alert loading').html("Submitting form data...").slideDown(200);
                $('#form-submit-btn').prop("disabled", true);
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {
                    // Grab the addresses span element
                    var selector = $("span#addresses");
                    selector.html('');

                    // Reset label with new addresses
                    var items = $("select#ips").val();
                    $.each(items, function(index, value) {
                        selector.append('<label class="label label-info">' + value + '</label> ');
                    });

                    // Close dialog
                    $("#edit-token-form").dialog("close");
                }
                else {
                    $('#jui-message2').attr('class', 'alert error').html(result.message).slideDown(500);
                }
            },
            error: function() {
                $('#jui-message2').attr('class', 'alert error').html('AJAX Error! Please check the console log.').slideDown(500);
            },
            complete: function () {
                $('#form-submit-btn2').prop("disabled", false);
            },
            timeout: 15000
        });

        // -------------------------------------------------------------------------
        // Edit Server Details On-Click
        $("#edit-details").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Close menu
            $(this).closest(".dropdown-menu").prev().dropdown("toggle");

            // Hide previous errors
            $('#jui-message').hide();
            $("#mws-validate-error").hide();
            validator.resetForm();

            // Set form default values
            $('input[name="providerName"]').val($("span#sName").html());

            // Show dialog form
            $("#edit-provider-form").dialog("option", {
                title: 'Update Provider Details',
                modal: true
            }).dialog("open");

            // Just to be sure, older IE's needs this
            return false;
        });

        // Authorize Click
        $("#auth-provider").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Push the request
            $.post( "/ASP/providers/authorize", { action: "auth", ajax: true, providers: [providerId] })
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
                        $("#auth-provider").hide();
                        $("#unauth-provider").show();
                        $("label#authorized").html("Authorized").attr('class', 'label label-success');
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

        // Un-Authorize Click
        $("#unauth-provider").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Push the request
            $.post( "/ASP/providers/authorize", { action: "unauth", ajax: true, providers: [providerId] })
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
                        $("#auth-provider").show();
                        $("#unauth-provider").hide();
                        $("label#authorized").html("Not Authorized").attr('class', 'label label-important');
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

        // Plasma Click
        $("#plasma-provider").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Push the request
            $.post( "/ASP/providers/plasma", { action: "plasma", ajax: true, providers: [providerId] })
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
                        $("label#plasma").html("Yes").attr('class', 'label label-success');
                        $("#plasma-provider").hide();
                        $("#unplasma-provider").show();
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

        // Un-Plasma Click
        $("#unplasma-provider").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Push the request
            $.post( "/ASP/providers/plasma", { action: "unplasma", ajax: true, providers: [providerId] })
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
                        $("label#plasma").html("No").attr('class', 'label label-inactive');
                        $("#unplasma-provider").hide();
                        $("#plasma-provider").show();
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

        // Edit Server Details On-Click
        $("#edit-addresses").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Close dropdown menu
            $(".dropdown-menu").dropdown("toggle");

            // Hide previous errors
            $('#jui-message2').hide();
            $("#mws-validate-error2").hide();

            // Set form default values
            var input = $('select#ips');
            $('span#addresses').children('label').each(function(i) {
                var addy = $(this).html();
                input.tagsinput('add', addy);
            });

            // Show dialog form
            $("#edit-token-form").dialog("option", {
                title: 'Authorized Stats Server Ip Addresses',
                modal: true
            }).dialog("open");

            // Just to be sure, older IE's needs this
            return false;
        });

        // Generate New Auth ID
        $("#gen-auth-id").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Close dropdown menu
            $(".dropdown-menu").dropdown("toggle");

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to generate a new AuthID? You should never have to change an AuthID unless it has been compromised. \
                    The server owner will need to be notified of this change before they will be able to post stats data again!')
                .dialog("option", {
                    title: "Confirm AuthID Change",
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {

                            $.post( "/ASP/providers/token", { ajax: true, action: "newId", providerId: providerId })
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
                                        $("#currentAuthId").html(result.message);
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

        // Generate New Auth Token
        $("#gen-auth-token").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Close dropdown menu
            $(".dropdown-menu").dropdown("toggle");

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to generate a new AuthToken? The server owner will need to be notified of this change before they will be able to post stats data again!')
                .dialog("option", {
                    title: "Confirm AuthToken Change",
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {

                            $.post( "/ASP/providers/token", { ajax: true, action: "newToken", providerId: providerId })
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
                                        $("#currentAuthToken").html(result.message);
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

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {
            // Extract the server ID
            selectedRowNode = $(this).closest('tr');
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            var id = sid[sid.length-1];

            // If action is "go", then let the link direct the user
            if (action === "go") {
                return;
            }
            else {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();
                // Skip disabled buttons
                if ($(this).attr('disabled') === 'disabled')
                    return false;

                // Always have the user confirm his action here!
                var name = $('#tr-server-' + id).find('td:eq(1)').html();

                // Show dialog form
                $("#mws-jui-dialog")
                    .html('Are you sure you want to delete the server "' + name + '"? All player histories and round histories made by this server will also be removed!')
                    .dialog("option", {
                        modal: true,
                        buttons: [
                            {
                                text: "Confirm",
                                class: "btn btn-danger",
                                click: function () {

                                    // Push the request
                                    $.post( "/ASP/servers/delete", { action: "delete", servers: [id] })
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
                                                table.row( selectedRowNode ).remove().draw();
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

        //////////////////////////////////////////////////////
        // Charts
        /////////////////////////////////////////////////////
        if($.plot) {

            var plot = $.plot($("#mws-line-chart"), [{
                label: "Games Processed by this Provider",
                color: "#c75d7b"
            }, {
                label: "Total Games Processed",
                color: "#c5d52b"
            }], {
                tooltip: true,
                tooltipOpts: {
                    content: function(label, xval, yval, flotItem){ // expects to pass these arguments
                        return "%s : %y";
                    },
                    defaultTheme: false,
                    cssClass: 'flotTip'
                },
                series: {
                    lines: {
                        show: true,
                        fill: false
                    },
                    points: {
                        show: true
                    }
                },
                grid: {
                    borderWidth: 0,
                    hoverable: true,
                    clickable: true
                },
                yaxis: {
                    minTickSize: 1,
                    tickDecimals: 0,
                    min:0
                }
            });

            // On Window Resize, redraw chart
            $(window).resize(function() {
                plot.resize();
                plot.setupGrid();
                plot.draw();
            });
        }

        var $result;
        var $loaded = false;

        // Load graph points
        $.getJSON("/ASP/providers/chartData/" + providerId, function(result){
            $result  = result;
            $loaded = true;

            //noinspection JSUnresolvedVariable
            plot.setData([{
                data: $result.week.y.server,
                label: "Games Processed by this Provider",
                color: "#c75d7b"
            }, {
                data: $result.week.y.total,
                label: "Total Games Processed",
                color: "#c5d52b"
            }]);

            plot.getAxes().xaxis.options.min = 0;
            //noinspection JSUnresolvedVariable
            plot.getAxes().xaxis.options.max = $result.week.x.total.length - 1;
            //noinspection JSUnresolvedVariable
            plot.getAxes().xaxis.options.ticks = $result.week.x.total;
            plot.setupGrid();
            plot.draw();
        });

        $('#weekRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    data: $result.week.y.server,
                    label: "Games Processed by this Provider",
                    color: "#c75d7b"
                }, {
                    data: $result.week.y.total,
                    label: "Total Games Processed",
                    color: "#c5d52b"
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.week.x.total.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.week.x.total;
                plot.setupGrid();
                plot.draw();
            }
        });

        $('#monthRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    data: $result.month.y.server,
                    label: "Games Processed by this Provider",
                    color: "#c75d7b"
                }, {
                    data: $result.month.y.total,
                    label: "Total Games Processed",
                    color: "#c5d52b"
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.month.x.total.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.month.x.total;
                plot.setupGrid();
                plot.draw();
            }
        });

        $('#yearRadio').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                plot.setData([{
                    data: $result.year.y.server,
                    label: "Games Processed by this Provider",
                    color: "#c75d7b"
                }, {
                    data: $result.year.y.total,
                    label: "Total Games Processed",
                    color: "#c5d52b"
                }]);

                plot.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.max = $result.year.x.total.length - 1;
                //noinspection JSUnresolvedVariable
                plot.getAxes().xaxis.options.ticks = $result.year.x.total;
                plot.setupGrid();
                plot.draw();
            }
        });

    });
})(jQuery, window, document);