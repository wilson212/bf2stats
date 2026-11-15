;(function ($, window, document) {

    $(document).ready(function () {

        var serverId = $("span#serverId").html();

        // Data Tables
        $(".mws-datatable-fn").DataTable({
            pagingType: "full_numbers",
            processing: false,
            serverSide: true,
            autoWidth: false,
            order: [[ 1, "desc" ]],
            ajax: {
                url: "/ASP/servers/history",
                type: "POST",
                data: function ( d ) {
                    return $.extend( {}, d, {
                        ajax: true,
                        action: 'list',
                        serverId: serverId
                    });
                },
                beforeSend: function() {
                    $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/loading.gif)');
                },
                complete: function(jqXHR, textStatus) {
                    if (textStatus === "success")
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/tick-circle.png)');
                    else
                        $('.loading-cell').css('background-image', 'url(/ASP/frontend/images/core/alerts/exclamation.png)');
                }
            },
            columns: [
                { "data": "check" },
                { "data": "round_end" },
                { "data": "map" },
                { "data": "players" },
                { "data": "team1" },
                { "data": "team2" },
                { "data": "winner" },
                { "data": "tickets" },
                { "data": "actions" }
            ],
            columnDefs: [
                { "searchable": false, "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 2 },
                { "searchable": true, "targets": 3 },
                { "searchable": false, "orderable": false, "targets": 4 },
                { "searchable": false, "orderable": false, "targets": 5 },
                { "searchable": false, "orderable": false, "targets": 6 },
                { "searchable": false, "orderable": false, "targets": 7 },
                { "searchable": false, "orderable": false, "targets": 8 }
            ]
        });

    });
})(jQuery, window, document);