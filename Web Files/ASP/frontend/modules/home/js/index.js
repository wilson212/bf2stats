;(function( $, window, document, undefined ) {

    $(document).ready(function() {

        if( $.plot ) {

            var data1 = [{
                label: "Snapshots",
                color: "#c75d7b"
            }];

            var data2 = [{
                label: "Ranks",
                color: "#c75d7b"
            }];

            var $result;
            var $ranksChartData;
            var $loaded = false;

            var gamesPlotChart = $.plot($("#mws-dashboard-chart"), data1, {
                series: {
                    lines: {
                        show: true,
                        fill: true
                    },
                    points: {
                        show: true
                    }
                },
                tooltip: true,
                tooltipOpts: {
                    content: function(label, xval, yval, flotItem){ // expects to pass these arguments
                        return "%s : %y";
                    },
                    defaultTheme: false,
                    cssClass: 'flotTip'
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0
                },
                yaxis: {
                    minTickSize: 1,
                    tickDecimals: 0,
                    min:0
                }
            });

            var ranksPlotChart = $.plot($("#mws-dashboard-chart2"), data2, {
                series: {
                    stack: 0,
                    lines: {
                        show: true,
                        fill: true
                    },
                    bars: {
                        show: false,
                        barWidth: 0.6
                    },
                    points: {
                        show: true
                    }
                },
                tooltip: true,
                tooltipOpts: {
                    content: function(label, xval, yval, flotItem){ // expects to pass these arguments
                        var name = $ranksChartData.ranks.name[xval][1];
                        if (name.substr(-1) === "s") {
                            return "Number of %x': %y";
                        }
                        else {
                            return "Number of %xs: %y";
                        }
                    },
                    defaultTheme: false,
                    cssClass: 'flotTip'
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0
                },
                yaxis: {
                    minTickSize: 1,
                    tickDecimals: 0,
                    min:0
                }
            });

            // On Window Resize, redraw chart
            $(window).resize(function() {
                gamesPlotChart.resize();
                gamesPlotChart.setupGrid();
                gamesPlotChart.draw();

                ranksPlotChart.resize();
                ranksPlotChart.setupGrid();
                ranksPlotChart.draw();
            });
        }

        if( $.fn.button ) {
            $("#mws-ui-button-radio").buttonset();
        }

        // Load graph points
        $.getJSON("/ASP/home/gamesChartData", function(result){
            $result  = result;
            $loaded = true;

            //noinspection JSUnresolvedVariable
            gamesPlotChart.setData([{
                label: "Games Played",
                color: "#c75d7b",
                data: result.week.y
            }]);

            gamesPlotChart.getAxes().xaxis.options.min = 0;
            //noinspection JSUnresolvedVariable
            gamesPlotChart.getAxes().xaxis.options.max = result.week.x.length - 1;
            //noinspection JSUnresolvedVariable
            gamesPlotChart.getAxes().xaxis.options.ticks = result.week.x;
            gamesPlotChart.setupGrid();
            gamesPlotChart.draw();
        });

        // Load graph points
        $.getJSON("/ASP/home/rankChartData", function(result){
            $ranksChartData = result;

            //noinspection JSUnresolvedVariable
            ranksPlotChart.setData([{
                label: "Number of Players",
                color: "#c75d7b",
                data: result.ranks.count
            }]);

            ranksPlotChart.getAxes().xaxis.options.min = 0;
            //noinspection JSUnresolvedVariable
            ranksPlotChart.getAxes().xaxis.options.max = result.ranks.count.length - 1;
            //noinspection JSUnresolvedVariable
            ranksPlotChart.getAxes().xaxis.options.ticks = result.ranks.name;
            ranksPlotChart.setupGrid();
            ranksPlotChart.draw();
        });

        $('#radio1').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                gamesPlotChart.setData([{
                    label: "Snapshots",
                    color: "#c75d7b",
                    data: $result.week.y
                }]);

                gamesPlotChart.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.max = $result.week.x.length - 1;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.ticks = $result.week.x;
                gamesPlotChart.setupGrid();
                gamesPlotChart.draw();
            }
        });

        $('#radio2').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                gamesPlotChart.setData([{
                    label: "Snapshots",
                    color: "#c75d7b",
                    data: $result.month.y
                }]);

                gamesPlotChart.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.max = $result.month.x.length - 1;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.ticks = $result.month.x;
                gamesPlotChart.setupGrid();
                gamesPlotChart.draw();
            }
        });

        $('#radio3').on('click', function() {
            if ($loaded) {
                //noinspection JSUnresolvedVariable
                gamesPlotChart.setData([{
                    label: "Snapshots",
                    color: "#c75d7b",
                    data: $result.year.y
                }]);

                gamesPlotChart.getAxes().xaxis.options.min = 0;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.max = $result.year.x.length - 1;
                //noinspection JSUnresolvedVariable
                gamesPlotChart.getAxes().xaxis.options.ticks = $result.year.x;
                gamesPlotChart.setupGrid();
                gamesPlotChart.draw();
            }
        });
    });
}) (jQuery, window, document);