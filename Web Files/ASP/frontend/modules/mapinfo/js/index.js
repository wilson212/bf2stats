;(function( $, window, document ) {

    $(document).ready(function() {

        // Define globals
        var currentId = '';
        var tableRow;

        // Data Tables
        $(".mws-datatable-fn").DataTable({
            autoWidth: false,
            pagingType: "full_numbers",
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 8 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // Ajax and form Validation
        // noinspection JSJQueryEfficiency
        var validator = $("#mws-validate").validate({
            rules: {
                mapName: {
                    required: true,
                    minlength: 3,
                    maxlength: 48
                }
            },
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors === 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error").html(message).show();
                } else {
                    $("#mws-validate-error").hide();
                }
            }
        });

        // Modal forms
        //noinspection JSUnresolvedVariable
        if( $.fn.dialog ) {
            $("#editor-form").dialog({
                autoOpen: false,
                title: "Edit Display Name",
                modal: true,
                width: "640",
                resizable: false,
                buttons: [{
                    text: "Submit",
                    click: function () {
                        $(this).find('form#mws-validate').submit();
                    }
                }]
            });
        }

        // Row Button Clicks
        $(document).on('click', 'a.btn-small', function(e) {

            // Extract the Map ID
            var sid = $(this).attr('id').split("-");
            if (sid.length !== 3 || $(this).attr('disabled') === 'disabled') {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();
                return false;
            }

            // If action is "go", then let the link direct the user
            if (sid[0] === "go") {
                return;
            }
            else {
                // For all modern browsers, prevent default behavior of the click
                e.preventDefault();
            }

            // Parse sections into variables
            currentId = sid[2];
            tableRow = $(this).closest('tr');
            var name = tableRow.find('td:eq(2)').html();

            // Hide previous errors
            $("#mws-validate-error").hide();
            validator.resetForm();

            // Set hidden input value
            $('input[name="mapId"]').val(currentId);

            // Set form default values
            $('input[name="mapName"]').val(name);

            // Show dialog form
            $("#editor-form").dialog("option", {
                modal: true,
                title: "Edit Map Name"
            }).dialog("open");

            // Just to be sure, older IE's needs this
            return false;
        });

        //noinspection JSJQueryEfficiency
        $("#mws-validate").ajaxForm({
            data: { ajax: true },
            beforeSubmit: function () {
                $("#mws-validate-error").hide();
                return true;
            },
            success: function (response) {
                // Parse the JSON response
                var result = jQuery.parseJSON(response);
                if (result.success === true) {

                    tableRow.find('td:eq(2)').html(result.displayName);

                    // Close dialog
                    $("#editor-form").dialog("close");
                }
                else {
                    $('#jui-message').attr('class', 'alert error').html(result.message).slideDown(500);

                    // Close dialog
                    $("#editor-form").dialog("close");
                }
            },
            error: function() {
                $('#jui-message').attr('class', 'alert error').html('AJAX Error! Please check the console log.').slideDown(500);
            },
            timeout: 10000
        });

    });

}) (jQuery, window, document);