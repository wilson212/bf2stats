;(function ($, window, document) {

    $(document).ready(function () {

        // Data Tables
        $(".mws-table").DataTable({
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            order: [[ 0, "asc" ]],
            columnDefs: [
                { "orderable": false, "targets": 4 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

    });
})(jQuery, window, document);