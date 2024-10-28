<?php ?>
<div class="chart-filter">
    <div class="chart-item-title">

    </div>
</div>
<div id="chart"></div>

<script>
    $(function () {
        $(document).ready(function () {
            //hour to float conversation
            function timesToSeconds(time) {
                var timeArray =[];
                for (let i = 0; i < time.length; i++) {
                    var parts = time[i].split(":");
                    var hours = parts[0];
                    var minutes = parts[1];
                    var seconds = parts[2];
                    var totalSeconds = hours * 3600 + minutes * 60 ;
                    var floatHours = totalSeconds / 3600;
                     timeArray[i] = parseFloat(floatHours.toFixed(2));
                }
                return timeArray;
            }
            //getting php array to javascript
            var project = <?php echo json_encode($project); ?>;
            var estimatehours = <?php echo json_encode($hours); ?>;
            var floatEstimateHoursArray = timesToSeconds(estimatehours);
            var actualhours = <?php echo json_encode($actualhours); ?>;
            var floatActualHoursArray = timesToSeconds(actualhours);

            chartDisplay();
            //chart design function
            function chartDisplay() {
                var options = {
                    chart: {
                        renderTo: 'chart',
                        type: 'column',
                        height: 600,
                        width: 1000,
                        style: {
                            margin: '0 auto'
                        }
                    },
                    title: {
                        text: 'MonthlyPlan'
                    },
                    xAxis: {
                        categories: project,
                        title: {text: 'Projects/Groups'}
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Hours',
                            align: 'middle'
                        }
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{}, {}]
                };


                var hours = ["Estimate Hours", "Actual Hours"];
                var data = [floatEstimateHoursArray, floatActualHoursArray];
                var color = ["#10c0d2", "#f1e019"];

                // Clear previous data and reset series data
                for (i = 0; i < data.length; i++) {
                    options.series[i].name = "";
                    options.series[i].data = "";
                    options.series[i].color = "";
                }

                // Intializeseries data based 
                var i = 0;
                for (i = 0; i < hours.length; i++) {

                    options.series[i].name = hours[i];
                    options.series[i].data = data[i];
                    options.series[i].color = color[i];



                }
                ;

                // Draw chart with options
                var chart = new Highcharts.Chart(options);

                // Display legend only for visible data.
                var item;
                for (k = i; k <= data.length; k++) {
                    item = chart.series[k];
                    if (item) {
                        item.options.showInLegend = false;
                        item.legendItem = null;
                        chart.legend.destroyItem(item);
                        chart.legend.render();
                    }
                }

            }

        });
    });

</script>