"use strict";

var nhDashboard = {
    init: function(){
        var self = this;        
        self.order.statistics.init();
        self.order.chart.init();
        self.product.init();
        self.article.init();
        self.website.infoWebsite.init();
        self.website.expiry.init();
        self.website.duration.init();
        self.website.setting.init();
        self.website.seo.init();
        self.customer.init();
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

                $(document).on('click', '[load-statistics-order]', function(e) {
                    var type = $(this).attr('load-statistics-order');

                    self.loadStatistic(type);
                });
            },
            loadStatistic: function(type = null){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/statistics/order',
                    dataType: 'html',
                    data: {
                        type: type
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
                    url: adminPath + '/dashboard/chart/order',
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
                            backgroundColor: color(KTApp.getStateColor('brand')).alpha(0.6).rgbString(),
                            borderColor : color(KTApp.getStateColor('brand')).alpha(0).rgbString(),                            
                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('brand'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: typeof(self.chartData.data_this_month) != _UNDEFINED ? self.chartData.data_this_month : []
                        },
                        {
                            fill: true,
                            label: nhMain.getLabel('thang_truoc'),
                            backgroundColor: color(KTApp.getStateColor('danger')).alpha(0.6).rgbString(),
                            borderColor : color(KTApp.getStateColor('danger')).alpha(0).rgbString(),                            
                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: typeof(self.chartData.data_previous_month) != _UNDEFINED ? self.chartData.data_previous_month : []
                        }
                    ]
                };

                var ctx = self.chartElement[0].getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        scales: {
                            xAxes: [{
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                gridLines: false,
                                ticks: {
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: KTApp.getBaseColor('shape', 3),
                                    fontSize: 13,
                                    padding: 10
                                }
                            }],
                            yAxes: [{
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                gridLines: {
                                    color: KTApp.getBaseColor('shape', 2),
                                    drawBorder: false,
                                    offsetGridLines: false,
                                    drawTicks: false,
                                    borderDash: [3, 4],
                                    zeroLineWidth: 1,
                                    zeroLineColor: KTApp.getBaseColor('shape', 2),
                                    zeroLineBorderDash: [3, 4]
                                },
                                ticks: {
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: KTApp.getBaseColor('shape', 3),
                                    fontSize: 13,
                                    padding: 10,
                                    callback: function(value, index, values) {
                                        return nhMain.utilities.parseNumberToTextMoney(value);
                                    }
                                }
                            }]
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
                            backgroundColor: KTApp.getStateColor('brand'),
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
                                    return label;
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
    product: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-product-statistics');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/dashboard/statistics/product',
                dataType: 'html'                   
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    },
    article: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-article-statistics');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/dashboard/statistics/article',
                dataType: 'html'
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    },
    website: {
        infoWebsite:{
            wrapElement: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-website-info');
                if(self.wrapElement == 0) return false;

                self.loadInfo();
            },
            loadInfo: function(){
                var self = this;
                
                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/info-website',
                    dataType: 'html'
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);
                });
            }
        },
        expiry:{
            wrapElement: null,
            chartSpaceElement: null,
            dataChart: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-website-expiry');
                if(self.wrapElement == 0) return false;

                self.loadExpiry();
            },
            loadExpiry: function(){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/expiry-website',
                    dataType: 'html'
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);
                });
            }
        },
        duration:{
            wrapElement: null,
            chartSpaceElement: null,
            dataChart: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-website-duration');
                if(self.wrapElement == 0) return false;

                self.loadDuration();

                $(document).on('click', '.btn-check-capacity', function(e) {
                    self.loadDuration({check_cdn: 1});
                });
            },
            loadDuration: function(params = {}){
                var self = this;
                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/duration-website',
                    dataType: 'html',
                    data: {
                        check_cdn: typeof(params.check_cdn) != _UNDEFINED ? params.check_cdn : 0
                    }
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);

                    self.initChartSpace();
                });
            },           
            initChartSpace: function(){
                var self = this;
                self.chartSpaceElement = self.wrapElement.find('#chart-website-space');
                if(!nhMain.utilities.notEmpty(self.chartSpaceElement)) return false;

                var inputData = $('#data-chart-space');
                if (inputData.length == 0) return false;

                self.dataChart = nhMain.utilities.parseJsonToObject(inputData.val());
                if($.isEmptyObject(self.dataChart)) return false;

                var data = [
                    {label: 'Còn trống', data: typeof(self.dataChart.capacity) != _UNDEFINED ? self.dataChart.capacity : 0, color: '#fd7e14'},
                    {label: 'Đã dùng', data: typeof(self.dataChart.used) != _UNDEFINED ? self.dataChart.used : 0, color:  '#6c757d'}
                ];
 
                $.plot(self.chartSpaceElement, data, {
                    series: {
                        pie: {
                            show: true
                        }
                    }
                });
            }
        },
        setting:{
            wrapElement: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-website-setting');
                if(self.wrapElement == 0) return false;

                self.loadSettingInfo();
            },
            loadSettingInfo: function(){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/setting-website',
                    dataType: 'html'
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);
                });
            }
        },
        seo:{
            wrapElement: null,
            init: function(){
                var self = this;

                self.wrapElement = $('#wrap-website-seo');
                if(self.wrapElement == 0) return false;

                self.loadSeoInfo();
            },
            loadSeoInfo: function(){
                var self = this;

                KTApp.block(self.wrapElement[0], blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/dashboard/seo-website',
                    dataType: 'html'
                }).done(function(response) {
                    self.wrapElement.html(response);
                    KTApp.unblock(self.wrapElement[0]);
                });
            }
        }
    },
    customer: {
        wrapElement: null,
        chartCustomerElement: null,
        chartDataCustomer: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-customer');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/dashboard/statistics/customer',
                dataType: 'html'
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);

                self.initChart();
            });
        },
        initChart: function(){
            var self = this;

            self.chartCustomerElement = self.wrapElement.find('#chart-customer')
            if(self.chartCustomerElement.length == 0) return false;


            var inputData = $('#data-chart-customer');
            if (inputData.length == 0) return false;

            self.chartDataCustomer = nhMain.utilities.parseJsonToObject(inputData.val());
            if($.isEmptyObject(self.chartDataCustomer)) return false;

            var chartData = {
                labels: typeof(self.chartDataCustomer.labels) != _UNDEFINED ? self.chartDataCustomer.labels : [],
                datasets: [{
                    backgroundColor: KTApp.getStateColor('success'),
                    data: typeof(self.chartDataCustomer.data_customers) != _UNDEFINED ? self.chartDataCustomer.data_customers : []
                }]
            };

            var chart = new Chart(self.chartCustomerElement[0], {
                type: 'bar',
                data: chartData,
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        intersect: false,
                        mode: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    barRadius: 4,
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            stacked: true
                        }],
                        yAxes: [{
                            display: false,
                            stacked: true,
                            gridLines: false
                        }]
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    }
                }
            });
        }
    }
}

$(document).ready(function() {
    nhDashboard.init();
});
