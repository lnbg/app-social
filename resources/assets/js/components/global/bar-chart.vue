<template>
    <div id="bar-chart-stack" style="padding-left: 5px; padding-right: 5px;">
        <div class="box" :class="boxStyle">
            <div class="box-header with-border">
                <h3 class="box-title">{{ title }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button v-if="false" type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChart" style="height:250px"></canvas>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        source: Object,
        title: String,
        boxStyle: String,
        stacked: Boolean
    },
    mounted() {
        var _vue = this;
        var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
        var barChartOptions                  = {
            scales: {
                xAxes: [{
                    stacked: _vue.stacked
                }],
                yAxes: [{
                    stacked: _vue.stacked,
                    ticks: {
                        callback: function(value, index, values) {
                            return numeral(value).format('0,0')
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, chart){
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return numeral(tooltipItem.yLabel).format('0,0')
                    }
                }
            },
        }

        new Chart(barChartCanvas , {
            type: "bar",
            data: _vue.source, 
            options: barChartOptions,
        });
    }
}
</script>

