;(function( $, window, document ) {

    $(document).ready(function() {

        // Define variables
        var showBots = true;
        var filterCountry = 99;
        var filterRank = 99;
        var filterStatus = 99;

        /**
         * Extracts the filename, without extension from a path
         *
         * @param extension The filepath
         * @returns {string} Returns the filename, without extension
         */
        String.prototype.filename = function(extension){
            var s = this.replace(/\\/g, '/');
            s = s.substring(s.lastIndexOf('/')+ 1);
            return extension ? s.replace(/[?#].+$/, '') : s.split('.')[0];
        };

        // Data Tables
        var Table = $(".mws-datatable-fn").DataTable({
            pageLength: 25,
            pagingType: "full_numbers",
            processing: false,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "/ASP/players/list",
                type: "POST",
                data: function ( d ) {
                    return $.extend( {}, d, {
                        ajax: true,
                        showBots: (showBots) ? 1 : 0,
                        filterRank: filterRank,
                        filterCountry: filterCountry,
                        filterStatus: filterStatus
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
            order: [[ 5, "desc" ]], // Order by global score
            columns: [
                { "data": "check" },
                { "data": "id" },
                { "data": "rank" },
                { "data": "name" },
                { "data": "country" },
                { "data": "score" },
                { "data": "joined" },
                { "data": "online" },
                { "data": "permban" },
                { "data": "actions" }
            ],
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 0 },
                { "searchable": false, "targets": 2 },
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

        // Validate the Add Player Form
        // noinspection JSJQueryEfficiency
        var validator = $("#mws-validate").validate({
            rules: {
                playerName: {
                    required: true,
                    minlength: 3,
                    maxlength: 32
                },
                playerEmail: {
                    required: true,
                    email: true,
                    maxlength: 64
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

        // Modal forms
        // noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#add-player-form").dialog({
                autoOpen: false,
                title: "Add New Player",
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

            $("#import-form").dialog({
                autoOpen: false,
                title: "Import Bots",
                modal: true,
                width: "640",
                resizable: false,
                buttons: [{
                    id: "form-submit-btn2",
                    text: "Submit",
                    click: function () {
                        $(this).find('form#mws-validate-2').submit();
                    }
                }]
            });

            $("#mws-jui-dialog").dialog({
                autoOpen: false,
                title: "Confirm Delete Player",
                modal: true,
                width: "640",
                resizable: false
            });

            // Add New Server Click
            $("#add-new").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $("#mws-validate-error").hide();
                $('#jui-message').hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('add');

                // Set form default values
                $('input[name="playerName"]').val("");
                $('input[name="playerPassword"]').val("").rules('add', { required: true });
                $('input[name="playerEmail"]').val("").rules('add', { required: true });
                $("#rankSelect").val(0);

                // Update labels
                $('#emailLabel').html('Email');
                $('#passwordLabel').html('Password');

                // Show dialog form
                $("#add-player-form").dialog("option", {
                    modal: true,
                    title: "Create New Player"
                }).dialog("open");
                $('#form-submit-btn').prop("disabled", false);

                // Just to be sure, older IE's needs this
                return false;
            });

            // Import Click
            $("#import-bots").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $("#mws-validate-error-2").hide();
                $('#jui-message-2').hide();
                validator.resetForm();

                // Show dialog form
                $("#import-form").dialog("option", {
                    modal: false,
                    title: "Import Player Bots"
                }).dialog("open");

                // Just to be sure, older IE's needs this
                return false;
            });
        }

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#mws-validate").ajaxForm({
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

                    // Reload the table
                    // noinspection JSUnresolvedFunction
                    Table.ajax.reload();

                    // Close dialog
                    $("#add-player-form").dialog("close");
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

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#mws-validate-2").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function () {
                $("#mws-validate-error-2").hide();
                $('#jui-message-2').attr('class', 'alert loading').html("Submitting form data...").slideDown(200);
                $('#form-submit-btn2').prop("disabled", true);
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {

                    // Reload the table
                    // noinspection JSUnresolvedFunction
                    Table.ajax.reload();

                    // Close dialog
                    $("#import-form").dialog("close");
                }
                else {
                    $('#jui-message-2').attr('class', 'alert error').html(result.message).slideDown(500);
                }
            },
            error: function () {
                $('#jui-message-2').attr('class', 'alert error').html('AJAX Error! Please check the console log.').slideDown(500);
            },
            complete: function () {
                $('#form-submit-btn2').prop("disabled", false);
            },
            timeout: 5000
        }).validate({
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors === 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error-2").html(message).show();
                    $('#jui-message-2').hide();
                } else {
                    $("#mws-validate-error-2").hide();
                }
            }
        });

        // Spinners
        // noinspection JSUnresolvedVariable
        $.fn.spinner && $('.mws-spinner').spinner();

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Chosen Select Box Plugin
        $("select#filterCountry").select2().change(function() {
            //Use $option (with the "$") to see that the variable is a jQuery object
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            filterCountry = $option.val();//to get content of "value"

            // Redraw Table
            Table.ajax.reload();
        });

        $("select#filterRank").select2().change(function() {
            //Use $option (with the "$") to see that the variable is a jQuery object
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            filterRank = $option.val();//to get content of "value"

            // Redraw Table
            Table.ajax.reload();
        });

        $("select#filterStatus").select2({
            dropdownCssClass : 'no-search'
        }).change(function() {
            //Use $option (with the "$") to see that the variable is a jQuery object
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            filterStatus = $option.val();//to get content of "value"

            // Redraw Table
            Table.ajax.reload();
        });

        // Copy list of countries from one select to the next!
        var options = $("#country").html();
        $('#filterCountry').append(options);

        /* File Input Styling */
        // noinspection JSUnresolvedVariable
        $.fn.fileInput && $("input[type='file']").fileInput();

        // Refresh Click
        $("#refresh").on('click', function(e) {
            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Reload table
            Table.ajax.reload();

            // Just to be sure, older IE's needs this
            return false;
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

        // Add New Server Click
        $("#delete-bots").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            $('#jui-global-message').hide();

            // Show dialog form
            $("#mws-jui-dialog")
                .html('This action will only remove the bots that have zero time played. Are you sure you want to delete player bots?')
                .dialog("option", {
                    modal: true,
                    buttons: [{
                        text: "Confirm",
                        class: "btn btn-danger",
                        click: function () {

                            $.post( "/ASP/players/delete", { ajax: true, action: "deleteBots" })
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
                                        // Update Table
                                        Table.ajax.reload();
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
                    }]
                }).dialog("open");

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
            if (action === "go") {
                return;
            }
            else {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();
            }

            // Always have the user confirm his action here!
            var tr = $(this).closest('tr');
            var name = tr.find('td:eq(3)').html();
            var email = $("#playerEmail_" + id).html();

            if (action === 'edit') {

                // Hide previous errors
                $("#mws-validate-error").hide();
                $('#jui-message').hide();
                validator.resetForm();

                // Set hidden input values
                $('input[name="action"]').val('edit');
                $('input[name="playerId"]').val(id);

                // Set form values
                $('input[name="playerName"]').val(name);
                $('input[name="playerPassword"]').val("").rules('remove', 'required');
                $('input[name="playerEmail"]').val(email).rules('remove', 'required');

                // Update labels
                $('#emailLabel').html('Update Email');
                $('#passwordLabel').html('Update Password');

                // Set player rank
                var rankHtml = tr.find('td:eq(2)').html();
                var rank =  rankHtml.filename().split('_')[1];
                $("#rankSelect").val(rank);

                // Select users country
                var cntryHtml = tr.find('td:eq(4)').html();
                var country =  cntryHtml.filename();
                $("select.mws-select2").val(country).change();

                // Show dialog form
                $("#add-player-form").dialog("option", {
                    modal: true,
                    title: 'Update Existing Player'
                }).dialog("open");
            }
            else if (action === 'unban') {
                // Push the request
                $.post( "/ASP/players/authorize", { ajax: true, action: "unban", playerId: id })
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
                            // Update html and button displays
                            $('#tr-status-' + id).attr('class', 'badge badge-info').html('Active');
                            $('#unban-btn-' + id).hide();
                            $('#ban-btn-' + id).show();
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
            else if (action === 'ban') {
                // Push the request
                $.post( "/ASP/players/authorize", { ajax: true, action: "ban", playerId: id })
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
                            // Update html and button displays
                            $('#tr-status-' + id).attr('class', 'badge badge-important').html('Banned');
                            $('#unban-btn-' + id).show();
                            $('#ban-btn-' + id).hide();
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

}) (jQuery, window, document);