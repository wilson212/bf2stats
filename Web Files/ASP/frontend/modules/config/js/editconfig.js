/**
 * Created by Steve on 2/14/2017.
 */
$(document).ready(function() {

    if( $.fn.dialog ) {
        $("#mws-jui-dialog").dialog({
            autoOpen: false,
            title: "AJAX error",
            modal: true,
            width: "640",
            buttons: [{
                text: "Reload Page",
                click: function () {
                    location.reload();
                }
            }]
        });
    }

    if ($.fn.validate) {
        $("#configForm").validate({
            ignoreTitle: true,
            rules: {
                cfg__db_port: {
                    required: true,
                    min: 1,
                    max: 65535,
                    digits: true
                },
                cfg__stats_min_game_time: {
                    required: true,
                    min: 0,
                    digits: true
                },
                cfg__stats_min_player_game_time: {
                    required: true,
                    min: 0,
                    digits: true
                },
                cfg__stats_players_min: {
                    required: true,
                    min: 1,
                    digits: true
                },
                cfg__stats_players_max: {
                    required: true,
                    min: 1,
                    digits: true
                },
                cfg__game_custom_mapid: {
                    required: true,
                    min: 602,
                    digits: true
                },
                cfg__stats_risingstar_interval: {
                    required: true,
                    min: 0,
                    max: 365,
                    digits: true
                },
                cfg__stats_smoc_interval: {
                    required: true,
                    min: 0,
                    max: 365,
                    digits: true
                },
                cfg__stats_general_interval: {
                    required: true,
                    min: 0,
                    max: 365,
                    digits: true
                }
            },
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    var message = errors === 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                    $("#mws-validate-error").html(message).show();
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                } else {
                    $("#mws-validate-error").hide();
                }
            }
        });
    }

    // Enable popovers
    $("[rel=popover]").popover();

    // Spinners
    // noinspection JSUnresolvedVariable
    $.fn.spinner && $('.mws-spinner').spinner();

    // Chosen Select Box Plugin
    // noinspection JSUnresolvedVariable
    $.fn.select2 && $("select.mws-select2").select2();

    // ===============================================
    // bind the Config form using 'ajaxForm'
    $('#configForm').ajaxForm({
        data: { ajax: true },
        beforeSubmit: function ()
        {
            $("#mws-validate-error").hide();
            $('#js_message').attr('class', 'alert loading').html('Submitting config settings...').slideDown(300);
            $("html, body").animate({ scrollTop: 0 }, "fast");
            return true;
        },
        success: function (response)
        {
            // Parse the JSON response
            var result = jQuery.parseJSON(response);
            if (result.success === true)
            {
                // Display our Success message, and ReDraw the table so we immediately see our action
                $('#js_message').attr('class', 'alert success')
                    .html('Success! Config saved successfully!')
                    .append('<span class="close-bt"></span>');
            }
            else
            {
                $('#js_message').attr('class', 'alert error')
                    .html('There was an error saving the configuration file. ' + result.message)
                    .append('<span class="close-bt"></span>');
            }
        },
        error: function(request) {
            $("#mws-jui-dialog").html('<pre>' + request.responseText + '</pre>').dialog("open");
        },
        timeout: 5000
    });

});