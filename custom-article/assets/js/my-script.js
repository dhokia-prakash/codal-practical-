jQuery(document).ready(function($) {
    $('#contact-form').submit(function(e) {
        e.preventDefault();
        var form_data = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: {
                action: 'submit_contact_form',
                form_data: form_data
            },
            success: function(response) {
                console.log(response);
                jQuery('#msg').text(response.message);
                jQuery('#msg').delay(3000).fadeOut('slow');
                jQuery('#msg').show();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });
});