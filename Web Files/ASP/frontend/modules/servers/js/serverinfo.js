;(function( $, window, document ) {

    $(document).ready(function() {

        // IPv4 Address Validation
        jQuery.validator.addMethod('validIP', function(value) {
            var split = value.split('.');
            if (split.length !== 4)
                return false;

            for (var i = 0; i < split.length; i++) {
                var s = split[i];
                if (s.length === 0 || isNaN(s) || s < 0 || s > 255)
                    return false;
            }
            return true;
        }, ' Invalid IP Address');


        // Data Tables
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            bSort: false
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
                serverName: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                serverIp: {
                    required: true,
                    validIP: true
                },
                serverPort: {
                    required: true,
                    min: 1,
                    max: 65535
                },
                serverQueryPort: {
                    required: true,
                    min: 1,
                    max: 65535
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
            $("#add-server-form").dialog({
                autoOpen: false,
                title: "Update Server Info",
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
                title: "Confirm Delete Server",
                modal: true,
                width: "640",
                resizable: false
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

                    selectedRowNode.find('td:eq(1)').html(result.serverName);
                    selectedRowNode.find('td:eq(3)').html(result.serverIp);
                    selectedRowNode.find('td:eq(4)').html(result.serverPort);
                    selectedRowNode.find('td:eq(5)').html(result.serverQueryPort);

                    // Close dialog
                    $("#add-server-form").dialog("close");
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
            // Extract the server ID
            selectedRowNode = $(this).closest('tr');
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            var id = sid[sid.length-1];

            // If action is "go", then let the link direct the user
            if (action === "go" || action === "view") {
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
                $('input[name="serverId"]').val(id);

                // Set form values
                $('input[name="serverName"]').val(selectedRowNode.find('td:eq(1)').html());
                $('input[name="serverIp"]').val(selectedRowNode.find('td:eq(3)').html());
                $('input[name="serverPort"]').val(selectedRowNode.find('td:eq(4)').html());
                $('input[name="serverQueryPort"]').val(selectedRowNode.find('td:eq(5)').html());

                // Show dialog form
                $("#add-server-form").dialog("option", {
                    title: 'Update Server Info',
                    modal: true
                }).dialog("open");
            }
            else if (action === 'delete') {
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
                                    delete_servers([id]);
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


        function delete_servers(ids)
        {
            // Push the request
            $.post( "/ASP/servers/delete", { action: "delete", servers: ids })
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
                            Table.row( $('#tr-server-' + value) ).remove().draw();
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

    });

}) (jQuery, window, document);