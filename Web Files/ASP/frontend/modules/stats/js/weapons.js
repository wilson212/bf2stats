;(function ($, window, document, undefined) {

    $(document).ready(function () {

        // Variables
        var filterCountry = 99;
        var filterItem = 0;

        // DataTables
        var table = $("table#topPlayers").DataTable({
            pageLength: 25,
            pagingType: "full_numbers",
            processing: false,
            serverSide: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: "/ASP/stats/topWeaponPlayers",
                type: "POST",
                data: function ( d ) {
                    return $.extend( {}, d, {
                        ajax: true,
                        filterItem: filterItem,
                        filterCountry: filterCountry
                    });
                },
                beforeSend: function() {
                    $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/loading.gif)');
                },
                complete: function(jqXHR, textStatus) {
                    if (textStatus === "success")
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/tick-circle.png)');
                    else
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/cross-circle.png)');
                }
            },
            order: [[ 5, "desc" ], [ 4, "desc" ]], // Order by kills, then time
            columns: [
                { "data": "check" },
                { "data": "rank" },
                { "data": "name" },
                { "data": "country" },
                { "data": "time" },
                { "data": "kills" },
                { "data": "deaths" },
                { "data": "ratio" },
                { "data": "accuracy" },
                { "data": "actions" }
            ],
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 0 },
                { "searchable": false, "orderable": false, "targets": 1 },
                { "searchable": true, "orderable": false, "targets": 2 },
                { "searchable": false, "orderable": false, "targets": 3 },
                { "searchable": false, "targets": 4 },
                { "searchable": false, "targets": 5 },
                { "searchable": false, "targets": 6 },
                { "searchable": false, "targets": 7 },
                { "searchable": false, "targets": 8 },
                { "searchable": false, "orderable": false, "targets": 9 }
            ]
        }).on( 'draw.dt', function () {
            //noinspection JSUnresolvedVariable
            $.fn.tooltip && $('[rel="tooltip"]').tooltip({ "delay": { show: 500, hide: 0 } });
        }).on( 'order.dt search.dt', function () {
            table.column(0, { search:'applied', order:'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i+1;
            });
        });

        // Chosen Select Box Plugin
        // noinspection JSUnresolvedVariable
        $.fn.select2 && $("select.mws-select2").select2();

        // Chosen Select Box Plugin
        $("select#filterCountry").select2().change(function() {
            //Use $option (with the "$") to see that the variable is a jQuery object
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            filterCountry = $option.val();//to get content of "value"

            // Redraw Table
            table.ajax.reload();
        });

        $("select#filterItem").select2().change(function() {
            //Use $option (with the "$") to see that the variable is a jQuery object
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            filterItem = $option.val();//to get content of "value"

            // Redraw Table
            table.ajax.reload();
        });

    });
})(jQuery, window, document);