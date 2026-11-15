(function ($) {
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxPrefilter(function (options, originalOptions, xhr) {
        if (!options.crossDomain && !/^(GET|HEAD)$/i.test(options.type)) {
            xhr.setRequestHeader('X-CSRF-Token', token);
        }
    });
    $(function () {
        $('form').each(function () {
            var target = document.createElement('a');
            target.href = this.action || window.location.href;
            if (this.method.toLowerCase() === 'post' && target.protocol === window.location.protocol
                && target.host === window.location.host) {
                $('<input>', {type: 'hidden', name: 'csrf_token', value: token}).appendTo(this);
            }
        });
    });
})(jQuery);
