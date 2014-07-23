$(function() {
    $('#chart-new-members').highcharts({
        title: {
            text: ''
        },
        xAxis: {
            categories: ['Nov 2013', 'Dec 2013', 'Jan 2014', 'Fev 2014', 'Mar 2014', 'Apr 2014',
                'May 2014', 'Jun 2014', 'Jul 2014', 'Aug 2014', 'Sep 2014', 'Oct 2014']
        },
        yAxis: {
            title: {
                text: 'New members'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
                return 'New members in <b>'+ this.x +
                    '</b>: <b>'+ this.y +'</b>';
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        series: [{
            name: '',
            data: [5, 10, 12, 14, 18, 21, 0, 0, 12, 21, 1, 0]
        }]
    });

    $('#chart-members-faculties').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        credits: {
            enabled: false
        },
        title: {
            text: ''
        },
        tooltip: {
            enabled: false
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            data: [
                ['ФИНКИ',   45.0],
                ['ПМФ',       26.8],
                ['ФЕИТ',       26.8],
                ['МФС',    8.5]
            ]
        }]
    });

    $('#chart-members-years').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        legend: {
            enabled: false
        },
        xAxis: {
            categories: [
                '2008', '2009', '2010', '2011', '2012', '2013', '2014'
            ]
        },
        credits: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Members'
            }
        },
        tooltip: {
            formatter: function() {
                return 'Members in <b>'+ this.x +
                    '</b>: <b>'+ this.y +'</b>';
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            data: [135, 128, 199, 190, 180, 194, 160]

        }]
    });
});