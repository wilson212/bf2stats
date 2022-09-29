$(document).ready(function() {

    // Init our validator!
	$("#configForm").validate();

	// ===============================================
    // bind the Config form using 'ajaxForm' 
    $('#configForm').ajaxForm({
        beforeSubmit: function (arr, data, options)
        {
            $('#js_message').attr('class', 'alert loading').html('Submitting config settings...').slideDown(300);
            $("html, body").animate({ scrollTop: 0 }, "fast");
            return true;
        },
        success: save_result,
        timeout: 5000 
    });

    // Callback function for the Config ajaxForm 
    function save_result(response, statusText, xhr, $form)  
    { 
        // Parse the JSON response
        var result = jQuery.parseJSON(response);
        if (result.success == true)
        {
            // Display our Success message, and ReDraw the table so we imediatly see our action
            $('#js_message').attr('class', 'alert success').html('Success! Config saved successfully!');
        }
        else
        {
            $('#js_message').attr('class', 'alert error').html('There was an error saving the configuration file. Please make sure it is writable.');
        }
        $('#js_message').delay(5000).slideUp(300);
    }
});