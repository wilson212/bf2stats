;(function ($, window, document) {

    $(document).ready(function () {

        // Variables
        var playerId = parseInt($("#playerId").html());

        // Data Tables
        $(".mws-table").DataTable({
            bPaginate: false,
            bFilter: false,
            bInfo: false,
            order: [[ 1, "desc" ]],
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

        // Enable popovers
        $("[rel=popover]").popover({ html: true });

        // Kill Death Ratio Chart
        if ($("#mws-pie-1").length > 0) {
            // noinspection JSUnresolvedVariable
            $.plot($("#mws-pie-1"), killData, {
                series: {
                    pie: {
                        innerRadius: 0.3,
                        highlight: {
                            opacity: 0.2
                        },
                        stroke: {
                            width: 1
                        },
                        show: true
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true
                },
                tooltip: true,
                tooltipOpts: {
                    content: "Total %s: %n",
                    defaultTheme: false,
                    cssClass: 'flotTip',
                    show: true
                }
            });
        }

        // Time Played As Chart
        if ($("#mws-pie-2").length > 0) {
            // noinspection JSUnresolvedVariable
            $.plot($("#mws-pie-2"), timePlayedData, {
                series: {
                    pie: {
                        innerRadius: 0.3,
                        highlight: {
                            opacity: 0.2
                        },
                        stroke: {
                            width: 1
                        },
                        show: true
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true,
                    clickable: true
                },
                tooltip: true,
                tooltipOpts: {
                    content: "%s Time: %t",
                    defaultTheme: false,
                    cssClass: 'flotTip'
                }
            });
        }

        // Row Button Clicks
        $(document).on('click', 'button.btn', function(e) {

            // Extract the server ID
            var sid = $(this).attr('id').split("-");
            var action = sid[0];
            var id = sid[sid.length-1];

            // For all modern browsers, prevent default behavior of the click
            e.preventDefault();

            if (action === 'go') {
                $('button[id^="go-"]').prop('disabled', true);
                window.location = "/ASP/players/history/" + playerId + "/" + id;
            }

            // Just to be sure, older IE's needs this
            return false;
        });

    });
})(jQuery, window, document);