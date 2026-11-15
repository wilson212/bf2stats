;(function( $, window, document ) {

    $(document).ready(function() {

        // Data Tables
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: false,
            autoWidth: false
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Selected row node, when we click an action button
        var selectedRowNode;

        // Ajax and form Validation
        //noinspection JSJQueryEfficiency
        var validator = $("#mws-validate").validate({
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

        // Modal forms
        //noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#add-provider-form").dialog({
                autoOpen: false,
                title: "Add New Provider",
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
                title: "Confirm Delete Provider",
                modal: true,
                width: "640",
                resizable: false
            });

            // Add New Provider Click
            $("#add-new").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('add');

                // Set form default values
                $('input[name="providerName"]').val("");

                // Show dialog form
                $("#add-provider-form").dialog("option", {
                    title: "Add New Provider",
                    modal: true
                }).dialog("open");

                // Just to be sure, older IE's needs this
                return false;
            });
        }

        //noinspection JSJQueryEfficiency
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
                    var id = result.providerId;
                    if (result.mode === 'add') {
                        // Add provider to table
                        //noinspection JSUnresolvedFunction
                        var rowNode = Table.row.add([
                            '<td class="checkbox-column"><input id="provider-' + id + '" type="checkbox"></td>',
                            result.providerId,
                            result.providerName,
                            result.authId,
                            result.authToken,
                            '0',
                            '<span id="tr-auth-' + id + '" class="badge badge-success">Authorized</span>',
                            '<span id="tr-plasma-' + id + '" class="badge badge-inactive">No</span>',
                            '<span class="btn-group"> \
                                <a id="go-btn" href="/ASP/provider/view/' + id + '" rel="tooltip" title="View Provider" class="btn btn-small"><i class="icon-eye-open"></i></a>\
                                <a id="edit-btn-' + id + '" href="#"  rel="tooltip" title="Edit Provider" class="btn btn-small"><i class="icon-pencil"></i></a> \
                                <a id="auth-btn-' + id + '" href="#" rel="tooltip" title="Authorize Provider" class="btn btn-small" style="display: none"><i class="icon-ok"></i></a> \
                                <a id="unauth-btn-' + id + '" href="#" rel="tooltip" title="Un-Authorize Provider" class="btn btn-small"><i class="icon-unlink"></i></a> \
                                <a id="delete-btn-' + id + '" href="#" rel="tooltip" title="Delete Provider" class="btn btn-small"><i class="icon-trash"></i></a> \
                            </span>'
                        ]).draw().node();

                        $( rowNode ).attr('id', 'tr-provider-' + id);
                    }
                    else if (result.mode === 'update') {
                        selectedRowNode.find('td:eq(2)').html(result.providerName);
                    }

                    // Close dialog
                    $("#add-provider-form").dialog("close");
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

        // Spinners
        //noinspection JSUnresolvedVariable
        $.fn.spinner && $('.mws-spinner').spinner();

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {
            // Extract the provider ID
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
            }

            if (action === 'edit') {

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input values
                $('input[name="action"]').val('edit');
                $('input[name="providerId"]').val(id);

                // Set form values
                $('input[name="providerName"]').val(selectedRowNode.find('td:eq(2)').html());

                // Show dialog form
                $("#add-provider-form").dialog("option", {
                    title: 'Update Provider Info',
                    modal: true
                }).dialog("open");
            }
            else if (action === 'unauth') {
                // Push the request
                $.post( "/ASP/providers/authorize", { action: "unauth", providers: [id] })
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
                            $('#tr-auth-' + id).attr('class', 'badge badge-important').html('Not Authorized');
                            $('#unauth-btn-' + id).hide();
                            $('#auth-btn-' + id).show();
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
            else if (action === 'auth') {
                // Push the request
                $.post( "/ASP/providers/authorize", { action: "auth", providers: [id] })
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
                            $('#tr-auth-' + id).attr('class', 'badge badge-success').html('Authorized');
                            $('#auth-btn-' + id).hide();
                            $('#unauth-btn-' + id).show();
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
            else if (action === 'delete') {
                // Skip disabled buttons
                if ($(this).attr('disabled') === 'disabled')
                    return false;

                // Always have the user confirm his action here!
                var name = $('#tr-provider-' + id).find('td:eq(2)').html();

                // Show dialog form
                $("#mws-jui-dialog")
                    .html('Are you sure you want to delete the provider "' + name + '"? All player histories and round histories made by this provider will also be removed!')
                    .dialog("option", {
                        modal: true,
                        buttons: [
                            {
                                text: "Confirm",
                                class: "btn btn-danger",
                                click: function () {
                                    deleteProviders([id]);
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
            var serverIds = getSelectedProviders();

            // Is anything selected?
            if (serverIds.length < 1)
                return false;

            // Show dialog form
            $("#mws-jui-dialog")
                .html('Are you sure you want to delete the selected providers? All player histories and round histories made by this provider will also be removed!')
                .dialog("option", {
                    modal: true,
                    buttons: [
                        {
                            text: "Confirm",
                            class: "btn btn-danger",
                            click: function () {
                                deleteProviders(serverIds);
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

            // Just to be sure, older IE's needs this
            return false;
        });

        // Un-Authorized Selected Click
        $("#unauth-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Get all checked
            var checkValues = getSelectedProviders();

            // Is anything selected?
            if (checkValues.length < 1)
                return false;

            // Push the request
            $.post( "/ASP/providers/authorize", { action: "unauth", providers: checkValues })
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
                        $.each(checkValues, function (key, value) {
                            // Update html and button displays
                            $('#tr-auth-' + value).attr('class', 'badge badge-important').html('Not Authorized');
                            $('#auth-btn-' + value).show();
                            $('#unauth-btn-' + value).hide();
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

            // Just to be sure, older IE's needs this
            return false;
        });

        // Authorized Selected Click
        $("#auth-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Get all checked
            var checkValues = getSelectedProviders();

            // Is anything selected?
            if (checkValues.length < 1)
                return false;

            // Push the request
            $.post( "/ASP/providers/authorize", { action: "auth", providers: checkValues })
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
                        $.each(checkValues, function (key, value) {
                            // Update html and button displays
                            $('#tr-auth-' + value).attr('class', 'badge badge-success').html('Authorized');
                            $('#unauth-btn-' + value).show();
                            $('#auth-btn-' + value).hide();
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

            // Just to be sure, older IE's needs this
            return false;
        });

        // Plasma Click
        $("#plasma-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Get all checked
            var checkValues = getSelectedProviders();

            // Is anything selected?
            if (checkValues.length < 1)
                return false;

            // Push the request
            $.post( "/ASP/providers/plasma", { action: "plasma", ajax: true, providers: checkValues })
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
                        // Update each row
                        $.each(checkValues, function (key, value) {
                            // Update html and button displays
                            $('#tr-plasma-' + value).attr('class', 'badge badge-success').html('Yes');
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

            // Just to be sure, older IE's needs this
            return false;
        });

        // Plasma Click
        $("#unplasma-selected").on('click', function(e) {

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Get all checked
            var checkValues = getSelectedProviders();

            // Is anything selected?
            if (checkValues.length < 1)
                return false;

            // Push the request
            $.post( "/ASP/providers/plasma", { action: "unplasma", ajax: true, providers: checkValues })
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
                        // Update each row
                        $.each(checkValues, function (key, value) {
                            // Update html and button displays
                            $('#tr-plasma-' + value).attr('class', 'badge badge-inactive').html('No');
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

        function deleteProviders(ids)
        {
            // Push the request
            $.post( "/ASP/providers/delete", { action: "delete", providers: ids })
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
                            Table.row( $('#tr-provider-' + value) ).remove().draw();
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

        function getSelectedProviders()
        {
            return $('input[type=checkbox]:checked').map(function() {
                // Extract the provider ID
                var sid = $(this).attr('id').split("-");
                return sid[sid.length-1];
            }).get();
        }

    });

}) (jQuery, window, document);