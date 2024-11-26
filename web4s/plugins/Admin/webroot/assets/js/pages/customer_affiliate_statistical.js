"use strict";

var nhStatisticAffiliate = {
    init: function(){
        var self = this;        
        self.order.statistics.init();
        self.order.chart.init();
        self.partner.init();
        self.settingCommissions.init();
    },
    order: {
        statistics: {
            wrapElement: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-order-statistics');
                if(self.wrapElement == 0) return false;

                self.event();
                self.loadStatistic();
            },
            event: function(){
                var self = this;

                $(document).on('click', '[filter-date]', function(e) {
                    var filter_date = $(this).attr('filter-date');

                    self.loadStatistic(filter_date);
                });

                $(document).on('click', '[load-statistics-order]', function(e) {
                    var type = $(this).attr('load-statistics-order');

                    self.loadStatistic(type);
                });
            },
            loadStatistic: function(filter_date = null){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/customer/affiliate/statistics/order',
                    dataType: 'html',
                    data: {
                        filter_date: filter_date
                    }                    
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);
                });
            },
        },
        chart: {
            wrapElement: null,
            chartElement: null,
            chartData: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-order-chart');
                if(self.wrapElement == 0) return false;

                self.loadChart();
            },
            loadChart: function(){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/customer/affiliate/chart/order',
                    dataType: 'html'
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);

                    self.initChart();                    
                });
            },
            initChart: function(){
                var self = this;

                self.chartElement = self.wrapElement.find('#chart-order');
                if (self.chartElement.length == 0) return false;

                var inputData = $('#data-chart-order');
                if (inputData.length == 0) return false;

                self.chartData = nhMain.utilities.parseJsonToObject(inputData.val());
                if($.isEmptyObject(self.chartData)) return false;

                var color = Chart.helpers.color;
                var barChartData = {
                    labels: typeof(self.chartData.labels) != _UNDEFINED ? self.chartData.labels : [],
                    datasets : [
                        {
                            fill: true,
                            label: nhMain.getLabel('thang_nay'),
                            backgroundColor: 'rgba(93, 120, 255, 0.6)',
                            borderColor : 'rgba(93, 120, 255, 0)',                         
                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: 'rgba(93, 120, 255, 1)',
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: typeof(self.chartData.money_this_month) != _UNDEFINED ? self.chartData.money_this_month : []
                        },
                        {
                            fill: true,
                            label: nhMain.getLabel('thang_truoc'),
                            backgroundColor: 'rgba(253, 57, 149, 0.6)',
                            borderColor : 'rgba(253, 57, 149, 0)',                          
                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: 'rgba(253, 57, 149, 1)',
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: typeof(self.chartData.money_previous_month) != _UNDEFINED ? self.chartData.money_previous_month : []
                        }
                    ]
                };

                var ctx = self.chartElement[0].getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        scales: {
                            x: {
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                gridLines: false,
                                ticks: {
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: 'rgba(175, 180, 212, 1)',
                                    fontSize: 13,
                                    padding: 10
                                }
                            },
                            y: {
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                gridLines: {
                                    color: 'rgba(217, 223, 250, 1)',
                                    drawBorder: false,
                                    offsetGridLines: false,
                                    drawTicks: false,
                                    borderDash: [3, 4],
                                    zeroLineWidth: 1,
                                    zeroLineColor: 'rgba(217, 223, 250, 1)',
                                    zeroLineBorderDash: [3, 4]
                                },
                                ticks: {
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: 'rgba(175, 180, 212, 1)',
                                    fontSize: 13,
                                    padding: 10,
                                    callback: function(value, index, values) {
                                        return nhMain.utilities.parseNumberToTextMoney(value);
                                    }
                                }
                            }
                        },
                        title: {
                            display: true
                        },
                        hover: {
                            mode: 'index'
                        },
                        tooltips: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            bodySpacing: 5,
                            yPadding: 10,
                            xPadding: 10, 
                            caretPadding: 0,
                            displayColors: false,
                            backgroundColor: 'rgba(93, 120, 255, 1)',
                            titleFontColor: '#ffffff', 
                            cornerRadius: 4,
                            footerSpacing: 0,
                            titleSpacing: 0,
                            callbacks: {
                                label: function(context) {
                                    var label = '';

                                    if (nhMain.utilities.notEmpty(context.value)) {
                                        label = nhMain.utilities.parseNumberToTextMoney(parseFloat(context.value));
                                    }
                                    return label + ' VND';
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 5,
                                bottom: 5
                            }
                        }
                    }
                });
            }
        }
    },
    partner: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-partner-statistics');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/customer/affiliate/statistics/top-partner',
                dataType: 'html'                   
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    },
    settingCommissions: {
        wrapElement: null,
        init: function(){
            var self = this;

            self.wrapElement = $('#wrap-setting-commissions');
            if(self.wrapElement == 0) return false;

            self.loadInfo();
        },
        loadInfo: function(){
            var self = this;
            
            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/customer/affiliate/statistics/new-partner',
                dataType: 'html'
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    }
}

$(document).ready(function() {
    nhStatisticAffiliate.init();
});
