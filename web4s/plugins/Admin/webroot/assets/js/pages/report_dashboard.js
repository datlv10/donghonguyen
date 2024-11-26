"use strict";

var nhReportDashboard = {
    init: function(){
        var self = this;  
        self.product.init();
        self.staff.init();
        self.revenue.init();
        self.source.init();
        self.city.init();

        $(document).on('click', '[load-group-report]', function(e) {
            $('[load-group-report]').removeClass('active');
            $(this).addClass('active');
            var type_filter = $(this).attr('load-group-report');
            self.staff.loadStatistic(type_filter);
            self.revenue.loadStatistic(type_filter);
            self.source.loadStatistic(type_filter);
            self.city.loadStatistic(type_filter);
        });
    },
    product: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#kt_datatable_product');
            if(self.wrapElement == 0) return false;

            self.ktDatatableProduct();
        },
        ktDatatableProduct: function() {
            var self = this;
            var datatable = self.wrapElement.KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: adminPath + '/report/load-dashboard-product',
                            headers: {
                                'X-CSRF-Token': csrfToken
                            },
                            map: function(raw) {
                                var dataSet = raw;
                                if (typeof raw.data !== _UNDEFINED) {
                                    dataSet = raw.data;
                                }
                                return dataSet;
                            },
                        },
                    },
                    pageSize: paginationLimitAdmin,
                    serverPaging: true,
                    serverFiltering: true,
                    serverSorting: true,
                },

                layout: {
                    scroll: true,
                    height: 390,
                    footer: false
                },

                sortable: false,
                filterable: false,
                pagination: false,
                columns: [
                    {
                        field: "name_extend",
                        title: nhMain.getLabel('san_pham'),
                        width: 400,
                        autoHide: false
                    }, 
                    {
                        field: "quantity",
                        title: nhMain.getLabel('so_luong_ban'),
                        width: 90,
                        autoHide: false,
                    },
                    {
                        field: "total",
                        title: nhMain.getLabel('doanh_thu'),
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var total = KTUtil.isset(row, 'total') && row.total != null ? nhMain.utilities.parseNumberToTextMoney(row.total) : '';
                            return total;
                        }
                    }
                ]
            });

            $('[load-group-report]').on('click', function() {
                datatable.search($(this).attr('load-group-report'), 'type_filter');
            });
        }
    },
    staff: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-staff');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(type_filter = null){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/report/load-dashboard-staff',
                dataType: 'html',
                data: {
                    type_filter: type_filter
                }                   
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    },
    revenue: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-revenue');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(type_filter = null){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/report/load-dashboard-revenue',
                dataType: 'html',
                data: {
                    type_filter: type_filter
                }                   
            }).done(function(response) {
                self.wrapElement.html(response);
                KTApp.unblock(self.wrapElement[0]);
            });
        }
    },
    source: {
        wrapElement: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-source');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(type_filter = null){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/report/load-dashboard-source',
                dataType: 'html',
                data: {
                    type_filter: type_filter
                }                   
            }).done(function(response) {
                self.wrapElement.html(response);
                self.chartSource();
                KTApp.unblock(self.wrapElement[0]);
            });
        },
        chartSource: function() {
            var self = this;

            self.chartElement = self.wrapElement.find('#chart-source');
            if (self.chartElement.length == 0) return false;

            var inputData = $('#data-chart-source');
            if (inputData.length == 0) return false;

            self.chartData = nhMain.utilities.parseJsonToObject(inputData.val());
            if($.isEmptyObject(self.chartData)) return false;
            Morris.Donut({
                element: 'chart-source',
                data: typeof(self.chartData) != _UNDEFINED ? self.chartData : [],
                colors: [
                    KTApp.getStateColor('success'),
                    KTApp.getStateColor('danger'),
                    KTApp.getStateColor('brand')
                ],
            });
        },
    },
    city: {
        wrapElement: null,
        chartDataCity: null,
        init: function(){
            var self = this;
            self.wrapElement = $('#wrap-city');
            if(self.wrapElement == 0) return false;

            self.loadStatistic();
        },
        loadStatistic: function(type_filter = null){
            var self = this;

            KTApp.block(self.wrapElement[0], blockOptions);
            nhMain.callAjax({
                url: adminPath + '/report/load-dashboard-city',
                dataType: 'html',
                data: {
                    type_filter: type_filter
                }                   
            }).done(function(response) {
                self.wrapElement.html(response);
                self.chartCity();
                KTApp.unblock(self.wrapElement[0]);
            });
        },
        chartCity: function() {
            var self = this;

            self.chartElement = self.wrapElement.find('#chart-city');
            if (self.chartElement.length == 0) return false;

            var inputData = $('#data-chart-city');
            if (inputData.length == 0) return false;

            self.chartDataCity = nhMain.utilities.parseJsonToObject(inputData.val());
            if($.isEmptyObject(self.chartDataCity)) return false;

            var chartData = {
                labels: typeof(self.chartDataCity.labels) != _UNDEFINED ? self.chartDataCity.labels : [],
                datasets: [{
                    backgroundColor: KTApp.getStateColor('success'),
                    data: typeof(self.chartDataCity.data) != _UNDEFINED ? self.chartDataCity.data : []
                }]
            };

            var chart = new Chart(self.chartElement[0], {
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
                        caretPadding: 10,
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
        },
    }
}

$(document).ready(function() {
    nhReportDashboard.init();
});