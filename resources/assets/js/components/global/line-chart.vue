<template>
    <div class="line-chart" style="padding-left: 5px; padding-right: 5px;">
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
                    <canvas :id="idWrapper" style="height:250px"></canvas>
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
        options: Object,
        idWrapper: {
            default: "barChart",
            type: String
        }
    },
    mounted() {
        var _vue = this;
        var _idWrapper = "#" + this.idWrapper;
        var barChartCanvas                   = $(_idWrapper).get(0).getContext('2d')
        var _options = Object.assign({}, this.options)
        new Chart(barChartCanvas , {
            type: "line",
            data: _vue.source, 
            options: _options,
        });
    }
}
</script>

