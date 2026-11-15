;(function( $, window, document ) {

    $(document).ready(function() {

        function htmlDecode(value) {
            return $("<div/>").html(value).text();
        }

        function htmlEncode(value) {
            return $('<div/>').text(value).html();
        }

        /**
         * nameRegex : specifies the characters allowed in a mod name
         */
        $.validator.addMethod("nameRegex", function(value, element) {
            return this.optional(element) || /^[a-z0-9_]+$/i.test(value);
        }, "Mod name must contain only letters, numbers, or underscores.");

        // Data Table
        var Table = $(".mws-datatable-fn").DataTable({
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            columnDefs: [
                { "orderable": false, "targets": 4 }
            ]
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
                shortName: {
                    required: true,
                    maxlength: 24,
                    nameRegex: true
                },
                longName: {
                    required: true,
                    maxlength: 48
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
                title: "Add New Game Mod",
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
                $('input[name="shortName"]').val("");
                $('input[name="longName"]').val("");

                // Show dialog form
                $("#editor-form").dialog("option", {
                    modal: true,
                    title: "Create New Game Mod"
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
              
                  if (result.mode === 'add') {
                    // Add mod to table
                    //noinspection JSUnresolvedFunction
                    rowNode = Table.row.add([
                        result.id,
                        result.name,
                        result.longname,
                        '<span id="status-' + id + '" class="badge badge-' + result.status_badge + '}">' + result.status_text + '</span>',
                        '<span class="btn-group"> \
                          <a id="edit-' + id + '" href="#"  rel="tooltip" title="Edit Details" class="btn btn-small"><i class="icon-pencil"></i></a> \
                        </span>'
                      ]).draw().node();

                    $(rowNode).attr('id', 'tr-mod-' + id);
                  } 
                  else if (result.mode === 'edit') {
                    selectedRowNode.find('td:eq(0)').html(result.id);
                    selectedRowNode.find('td:eq(1)').html(result.name);
                    selectedRowNode.find('td:eq(2)').html(result.longname);
                    $('span#status-' + id).attr('class', 'badge badge-' + result.status_badge).html(result.status_text);
                  }
                  
                    // Close dialog
                    $("#editor-form").dialog("close");
                }
                else {
                  $('#jui-message').attr('class', 'alert error').html(result.message);
                }
            },
            error: function(request, status, error) {
                $('#jui-message').attr('class', 'alert error')
                    .html('AJAX Error! Please check the console log.')
                    .append('<span class="close-bt"></span>');
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

            // Always have the user confirm his action here!
            var id = selectedRowNode.find('td:eq(0)').html();
            var name = selectedRowNode.find('td:eq(1)').html();
            var desc = htmlDecode( selectedRowNode.find('td:eq(2)').html() );
            var status = $('span#status-' + id).html();


            // Hide previous errors
            $('#jui-message').hide();
            $("#mws-validate-error").hide();
            validator.resetForm();

            // Set hidden input value
            $('input[name="action"]').val('edit');
            $('input[name="originalId"]').val(id);

            // Set form default values
            $('input[name="shortName"]').val(name);
            $('input[name="longName"]').val(desc);
            $("#authorized").val((status === 'Authorized') ? 1 : 0);

            // Show dialog form
            $("#editor-form").dialog("option", {
                modal: true,
                title: 'Update Game Mod'
            }).dialog("open");
            $('#form-submit-btn').prop("disabled", false);

            // Just to be sure, older IE's needs this
            return false;
        });

    });

}) (jQuery, window, document);
