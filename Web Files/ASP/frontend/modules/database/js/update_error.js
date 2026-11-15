;(function ($, window, document) {

    $(document).ready(function () {

        $('#button-to-home').on('click', function(event) {
            event.preventDefault();
            var url = $(this).data('target');
            location.replace(url);
        });

    });
})(jQuery, window, document);