;(function ($, window, document) {

    $(document).ready(function () {

        // Data Tables
        $(".mws-datatable-fn").DataTable({
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            order: [[ 3, "desc" ], [ 8, "desc" ], [ 7, "asc" ]],
            columnDefs: [
                { "orderable": false, "targets": 0 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Data Tables
        $(".mws-table:not(#all)").DataTable({
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            order: [[ 2, "desc" ], [ 7, "desc" ], [ 6, "asc" ]],
            columnDefs: [
                { "orderable": false, "targets": 0 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        });

        // Tooltips
        //noinspection JSUnresolvedVariable
        $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });

        // jQuery-UI Tabs
        // noinspection JSUnresolvedVariable
        $.fn.tabs && $(".mws-tabs").tabs();

        // Enable popovers
        $("[rel=popover]").popover({html: true});

    });
})(jQuery, window, document);