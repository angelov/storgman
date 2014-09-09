$(function() {

    if (typeof membersPerMonth == 'object') {

        $('#chart-new-members').highcharts({
            title: {
                text: ''
            },
            xAxis: {
                categories: membersPerMonth.months
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
                data: membersPerMonth.values
            }]
        });

    }

    if (typeof membersPerFaculty == 'object') {

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
                /*data: [
                    ['ФИНКИ',   45.0],
                    ['ПМФ',       26.8],
                    ['ФЕИТ',       26.8],
                    ['МФС',    8.5]
                ]*/
                data: membersPerFaculty
            }]
        });

    }

    if (typeof memberAttendancePerMonth == 'object') {

        $('#chart-member-attendance').highcharts({
            chart: {
                height: 200,
                events: {
                    load: function () {
                        var chart = this;
                        $(chart.series).each(function (i, serie) {
                            var elem = '<p style="float: left; margin-left: 10px;">' +
                                '<span style="width: 15px; height: 15px; ' +
                                'vertical-align: text-bottom;' +
                                'margin-right: 2px; display: inline-block; ' +
                                'background-color: ' + serie.color + '"></span> ' + serie.name + "" +
                                "</p>";

                            $("#chart-member-attendance-legend").append(elem);
                        });
                    }
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: memberAttendancePerMonth.months
            },
            yAxis: {
                title: {
                    enabled: false
                },
                allowDecimals: false
            },
            tooltip: {
                formatter: function() {
                    return 'Meetings: <b>'+ this.y +'</b>';
                }
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Total',
                data: memberAttendancePerMonth.total
            },{
                name: 'Attended',
                data: memberAttendancePerMonth.attended
            }]
        });

    }

    // experimental

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


    $('#chart-meeting-returning-members').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
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
                    enabled: false
                },
                size: 80,
                showInLegend: true
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            type: 'pie',
            data: [
                ['New',   20.0],
                ['Returning',       80.0]
            ]
        }]
    });

    if (typeof totalFeesPerMonth == 'object') {

        $('#chart-fees-per-month').highcharts({
            chart: {
                height: 200,
                events: {
                    load: function () {
                        var chart = this;
                        $(chart.series).each(function (i, serie) {
                            var elem = '<p style="float: left; margin-left: 10px;">' +
                                '<span style="width: 15px; height: 15px; ' +
                                'vertical-align: text-bottom;' +
                                'margin-right: 2px; display: inline-block; ' +
                                'background-color: ' + serie.color + '"></span> ' + serie.name + "" +
                                "</p>";

                            $("#chart-fees-per-month-legend").append(elem);
                        });
                    }
                }
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: totalFeesPerMonth.months
            },
            yAxis: {
                min: 0,
                title: {
                    enabled: false
                },
                allowDecimals: false
            },
            tooltip: {
                formatter: function() {
                    var s = [];

                    $.each(this.points, function(i, point) {
                        s.push(point.series.name + ': <strong>' + point.y +'</strong>');
                    });

                    return s.join('<br />');
                },
                shared: true
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Expected',
                data: totalFeesPerMonth.expected
            },{
                name: 'Paid',
                data: totalFeesPerMonth.paid
            }]
        });

    }

});