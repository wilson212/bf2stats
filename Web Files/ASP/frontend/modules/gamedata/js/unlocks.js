;(function( $, window, document ) {

    $(document).ready(function() {

        function htmlDecode(value) {
            return $("<div/>").html(value).text();
        }

        function htmlEncode(value) {
            return $('<div/>').text(value).html();
        }

        /**
         * nameRegex : specifies the characters allowed in an unlock name
         */
        $.validator.addMethod("nameRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9_]+$/i.test(value);
        }, "Unlock name must contain only letters, numbers, or underscores.");

        // Data Table
        var Table = $(".mws-datatable-fn").DataTable({
            pageLength: 25,
            pagingType: "full_numbers"
        }).on( 'draw.dt', function () {
            // Add tooltips to dynamically added rows
            // noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Selected row node, when we click an action button
        var selectedRowNode;

        // Ajax and form Validation
        //noinspection JSJQueryEfficiency
        var validator = $("#mws-validate").validate({
            rules: {
                unlockId: {
                    required: true,
                    min: 1,
                    max: 999999
                },
                unlockName: {
                    required: true,
                    maxlength: 32,
                    nameRegex: true
                },
                unlockDesc: {
                    required: true,
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
            $("#editor-form").dialog({
                autoOpen: false,
                title: "Add New Unlock",
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
                title: "Confirm Delete Unlock",
                modal: true,
                width: "640",
                resizable: false
            });

            // Add New Server Click
            $("#add-new").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('add');
                $('input[name="originalId"]').val('0');

                // Set form default values
                $('input[name="unlockName"]').val("");
                $('input[name="unlockDesc"]').val("");
                $('input[name="unlockId"]').val("");
                $("#unlockKit").val(0);
                $("#unlockRequired").val(0);

                // Show dialog form
                $("#editor-form").dialog("option", {
                    modal: true,
                    title: "Create New Unlock"
                }).dialog("open");
                $('#form-submit-btn').prop("disabled", false);

                // Just to be sure, older IE's needs this
                return false;
            });
        }

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#mws-validate").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function () {
                $('#mws-validate-error').hide();
                $('#jui-message').attr('class', 'alert loading').html("Submitting form data...").slideDown(200);
                $('#form-submit-btn').prop("disabled", true);
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {
                    var id = result.id;
                    var rowNode;

                    if (result.mode === 'add') {
                        // Add award to table
                        //noinspection JSUnresolvedFunction
                        rowNode = Table.row.add([
                            result.id,
                            result.name,
                            result.desc,
                            result.kit,
                            result.reqname,
                            '<span class="btn-group"> \
                                <a id="edit-' + id + '" href="#"  rel="tooltip" title="Edit Unlock" class="btn btn-small"><i class="icon-pencil"></i></a> \
                                <a id="delete-' + id + '" href="#" rel="tooltip" title="Delete Unlock" class="btn btn-small"><i class="icon-trash"></i></a> \
                            </span>'
                        ]).draw().node();

                        $( rowNode ).attr('id', 'tr-unlock-' + id);

                        $('#unlockRequired').append($('<option>', {
                            value: result.id,
                            text : result.name
                        }));
                    }
                    else if (result.mode === 'edit') {
                        selectedRowNode.find('td:eq(0)').html(result.id);
                        selectedRowNode.find('td:eq(1)').html(result.name);
                        selectedRowNode.find('td:eq(2)').html(result.desc);
                        selectedRowNode.find('td:eq(3)').html(result.kit);
                        selectedRowNode.find('td:eq(4)').html(result.reqname);

                        // Extract the old unlock ID
                        var ssid = selectedRowNode.attr('id').split("-");
                        var oldId = ssid[2];

                        // Update row id also
                        selectedRowNode.attr('id', 'tr-unlock-' + result.id);

                        // Update the select option value
                        $('#unlockRequired').find('[value="' + oldId + '"]').prop({
                            value: result.id
                        });
                    }

                    // Close dialog
                    $("#editor-form").dialog("close");
                }
                else {
                    $('#jui-message').attr('class', 'alert error').html(result.message);
                }
            },
            error: function(request, status, error) {
                $('#jui-message').attr('class', 'alert error').html('AJAX Error! Please check the console log.');
                console.log(error);
            },
            timeout: 5000
        });

        // Spinners
        // noinspection JSUnresolvedVariable
        $.fn.spinner && $('.mws-spinner').spinner();

        // Tooltips
        // noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

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

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            // Extract the server ID
            selectedRowNode = $(this).closest('tr');
            var sid = $(this).attr('id').split("-");
            var action = sid[0];

            // Always have the user confirm his action here!
            var id = selectedRowNode.find('td:eq(0)').html();
            var name = selectedRowNode.find('td:eq(1)').html();
            var desc = htmlDecode( selectedRowNode.find('td:eq(2)').html() );
            var kit = selectedRowNode.find('td:eq(3)').html();
            var req = selectedRowNode.find('td:eq(4)').html();

            if (action === 'edit') {

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('edit');
                $('input[name="originalId"]').val(id);

                // Set form default values
                $('input[name="unlockName"]').val(name);
                $('input[name="unlockDesc"]').val(desc);
                $('input[name="unlockId"]').val(id);
                $("#unlockKit").find("option:contains(" + kit + ")").prop("selected", "selected");
                $("#unlockRequired").find("option:contains(" + req + ")").prop("selected", "selected");

                // Show dialog form
                $("#editor-form").dialog("option", {
                    modal: true,
                    title: 'Update Existing Unlock'
                }).dialog("open");
                $('#form-submit-btn').prop("disabled", false);
            }
            else if (action === 'delete') {
                // Show dialog form
                $("#mws-jui-dialog")
                    .html('Are you sure you want to delete unlock "' + name + '"? This action cannot be <b>reversed</b>! \
                        All Players who have earned this unlock will be giving a new unlock to choose from in the BFHQ.'
                    )
                    .dialog("option", {
                        modal: true,
                        buttons: [
                            {
                                text: "Confirm",
                                class: "btn btn-danger",
                                click: function () {

                                    $.post( "/ASP/gamedata/deleteUnlock", { action: "delete", unlockId: id })
                                        .done(function( data ) {
                                            // Parse response
                                            var result = jQuery.parseJSON(data);
                                            if (result.success === false) {
                                                $('#jui-global-message')
                                                    .attr('class', 'alert error')
                                                    .html(result.message)
                                                    .slideDown(500)
                                                    .delay(5000)
                                                    .slideUp(500);
                                            }
                                            else {
                                                // Update html and button displays
                                                Table.row( selectedRowNode ).remove().draw();

                                                // Remove option
                                                $('#unlockRequired').find('[value="' + id + '"]').remove();
                                            }
                                        })
                                        .fail(function( jqXHR ) {
                                            var result = jQuery.parseJSON(jqXHR.responseText);
                                            if (result != null)
                                            {
                                                $('#jui-global-message')
                                                    .attr('class', 'alert error')
                                                    .html(result.message)
                                                    .slideDown(500)
                                                    .delay(5000)
                                                    .fadeOut('slow');
                                            }
                                            else
                                            {
                                                $('#jui-global-message')
                                                    .attr('class', 'alert error')
                                                    .html("An Error Occurred. Please check the ASP error log for details.")
                                                    .slideDown(500)
                                                    .delay(5000)
                                                    .fadeOut('slow');
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

    });

}) (jQuery, window, document);