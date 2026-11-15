;(function( $, window, document ) {

    $(document).ready(function() {

        /**
         * codeRegex : specifies the characters allowed in an award snapshot code
         */
        $.validator.addMethod("codeRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9]+$/i.test(value);
        }, "Snapshot code must contain only letters or numbers.");

        // Data Table
        var Table = $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers"
        }).on( 'draw.dt', function () {
            // Add tooltips to dynamically added rows
            // noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Selected row node, when we click an action button
        var selectedRowNode;

        // Ajax and form Validation
        // noinspection JSJQueryEfficiency
        var validator = $("#mws-validate").validate({
            rules: {
                awardCode: {
                    required: true,
                    minlength: 1,
                    maxlength: 6,
                    codeRegex: true
                },
                awardId: {
                    required: true,
                    min: 1000000,
                    max: 3400000
                },
                awardName: {
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

        /**
         * Updates the award ID rules with the award type changes
         */
        $('#awardType').change(function() {
            var selector = $('#awardId');
            selector.rules('remove', 'min');
            selector.rules('remove', 'max');

            var i = parseInt($(this).val());
            switch (i)
            {
                case 0:
                    selector.rules('add', { min: 3000000, max: 3400000 });
                    break;
                case 1:
                    selector.rules('add', { min: 1000000, max: 1400000 });
                    break;
                case 2:
                    selector.rules('add', { min: 2000000, max: 2400000 });
                    break;
            }
        });

        // Modal forms
        // noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#editor-form").dialog({
                autoOpen: false,
                title: "Add New Award",
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
            $("#add-new").on('click', function(e) {

                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('add');
                $('input[name="originalId"]').val('add');

                // Set form default values
                $('input[name="awardName"]').val("");
                $('input[name="awardCode"]').val("");
                $('input[name="awardId"]').val("");


                $("#awardType").val(0);
                $("#awardBackend").val(0);

                // Show dialog form
                $("#editor-form").dialog("option", {
                    modal: true,
                    title: "Create New Award"
                }).dialog("open");

                // Just to be sure, older IE's needs this
                return false;
            });
        }

        // Ajax Form
        // noinspection JSJQueryEfficiency
        $("#mws-validate").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function ()
            {
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
                    var backend = (parseInt(result.backend) === 1) ? 'Yes' : 'No';

                    if (result.mode === 'add') {
                        // Add award to table
                        //noinspection JSUnresolvedFunction
                        rowNode = Table.row.add([
                            result.id,
                            result.name,
                            result.code,
                            typeToString(result.type),
                            "0",
                            backend,
                            '<span class="btn-group"> \
                                <a id="edit-' + id + '" href="#"  rel="tooltip" title="Edit Award" class="btn btn-small"><i class="icon-pencil"></i></a> \
                                <a id="delete-' + id + '" href="#" rel="tooltip" title="Delete Award" class="btn btn-small"><i class="icon-trash"></i></a> \
                            </span>'
                        ]).draw().node();

                        $( rowNode ).attr('id', 'tr-award-' + id);
                    }
                    else if (result.mode === 'edit') {
                        selectedRowNode.find('td:eq(0)').html(result.id);
                        selectedRowNode.find('td:eq(1)').html(result.name);
                        selectedRowNode.find('td:eq(2)').html(result.code);
                        selectedRowNode.find('td:eq(3)').html(typeToString(result.type));
                        selectedRowNode.find('td:eq(5)').html(backend);
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
            complete: function () {
                $('#form-submit-btn').prop("disabled", false);
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
        $("#refresh").click(function(e) {

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
            var code = selectedRowNode.find('td:eq(2)').html();
            var backend = selectedRowNode.find('td:eq(5)').html();

            if (action === 'edit') {

                // Hide previous errors
                $('#jui-message').hide();
                $("#mws-validate-error").hide();
                validator.resetForm();

                // Set hidden input value
                $('input[name="action"]').val('edit');
                $('input[name="originalId"]').val(id);

                // Set form default values
                $('input[name="awardName"]').val(name);
                $('input[name="awardCode"]').val(code);
                $('input[name="awardId"]').val(id);
                $("#awardBackend").val( (backend === 'Yes') ? 1 : 0 );

                // Set award type
                var awardType = $("#awardType");
                switch (id[0])
                {
                    case "1":
                        awardType.val(1);
                        break;
                    case "2":
                        awardType.val(2);
                        break;
                    case "3":
                        awardType.val(0);
                        break;
                }

                // Show dialog form
                $("#editor-form").dialog("option", {
                    modal: true,
                    title: 'Update Existing Award'
                }).dialog("open");
            }
            else if (action === 'delete') {
                // Show dialog form
                $("#mws-jui-dialog")
                    .html('Are you sure you want to delete award "' + name + '"? This action cannot be <b>reversed</b>! \
                        All Players who have earned this award will have this award removed from their awards list.'
                    )
                    .dialog("option", {
                        modal: true,
                        buttons: [
                            {
                                text: "Confirm",
                                class: "btn btn-danger",
                                click: function () {

                                    $.post( "/ASP/gamedata/deleteAward", { action: "delete", awardId: id })
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
                                                Table.row( selectedRowNode ).remove().draw();
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

    });

    function typeToString(type)
    {
        switch (parseInt(type))
        {
            default:
            case 0:
                return "Ribbon";
            case 1:
                return "Badge";
            case 2:
                return "Medal";
        }
    }

}) (jQuery, window, document);