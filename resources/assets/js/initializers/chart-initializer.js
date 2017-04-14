(function ($, undefined) {

    "use strict";

    var ChartInitializer = namespace("Tmt.Initializers").ChartInitializer = function () {
        this.initialize();
        this.data = [];
        this.charts = [];
        this.waves = {};
    };

    inherit([Tmt.EventEmitter], ChartInitializer, {
        'build': function (app) {
            app.on('chartData', addChartData.bind(this));
            app.on('waveData', addWaveData.bind(this));
            app.on('chart', addChart.bind(this));

            $(onDomReady.bind(this));
        }
    });

    function addChartData(app, slug, datasetName, chartData) {
        if (this.data[datasetName]) {
            throw Error("Dataset name is not unique: " + datasetName);
        }

        this.data[datasetName] = {
            'slug' : slug,
            'data' : chartData
        }
    }

    function addWaveData(app, slug, waveData) {
        this.waves[slug] = waveData;
    }

    function addChart(app, selector, datasetName, startPosition, endPosition) {
        var dataset = this.data[datasetName],
            chart = new Tmt.Components.Chart.LineChart($(selector));

        chart.setData(dataset.data)
        chart.setRange(startPosition, endPosition);

        if (this.waves[dataset.slug]) {
            chart.setWave(this.waves[dataset.slug]);
        }

        this.charts.push(chart);
    }

    function onDomReady() {
        this.charts.forEach(function(chart){
            chart.render();
        });
    }

})(jQuery);
