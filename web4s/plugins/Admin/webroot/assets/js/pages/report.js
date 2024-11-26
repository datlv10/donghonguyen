"use strict";

var nhReport = {
    init: function() {
        var self = this;
        
        self.report.init();

        $('.kt-selectpicker').selectpicker();
        $('body').tooltip({ selector: '[data-toggle=kt-tooltip]' });
    },
    report: {
        wrapElement: null,
        formEl: null,
        page: 1,
        number_record: 50,
        sort_field: null,
        sort_type: null,
        init: function() {

            var self = this;
            self.wrapElement = $('#wrap-report');
            self.formEl = $('[nh-form="list-report"]');

            if(self.wrapElement == null || self.wrapElement == _UNDEFINED || self.wrapElement.length == 0){
                return false;
            }

            if(self.formEl == null || self.formEl == _UNDEFINED || self.formEl.length == 0){
                return false;
            }

            self.loadReport();

            $(document).on('click', '#btn-search', function(e) {
                self.page = 1;
                self.loadReport();
            });

            $(document).on('click', '#btn-refresh-search', function(e) {
                self.page = 1;
                self.formEl.find('input').val('');
                self.formEl.find('.kt-selectpicker').val('');
                self.formEl.find('.kt-selectpicker').selectpicker('refresh');
                self.loadReport({refresh: true});
            });

            $(document).on('click', '.kt-pagination li:not(.kt-datatable__pager-link--disabled , .kt-pagination__link--active) a', function(e){
                e.preventDefault();

                self.page = parseInt($(this).attr('nh-page-redirect'));
                self.loadReport();
            });

            $(document).on('change', '#display', function(e){
                self.loadReport();
            });

            $(document).on('change', '#number_record', function(e){
                self.number_record = $(this).val();
                self.loadReport();
            });

            $(document).on('click', '[data-field]', function(e) {
                self.sort_field = $(this).attr('data-field');
                self.sort_type = $(this).attr('data-sort');
                self.loadReport();
            });

            $(document).on('click', '[nh-export]', function(e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var formData = self.formEl.serialize();
                formData = formData + '&page=' + self.page + '&number_record=' + self.number_record + '&sort_field=' + self.sort_field + '&sort_type=' + self.sort_type + '&export=' + nhExport;
                nhMain.callAjax({
                    url: self.formEl.attr('action'),
                    data: formData
                }).done(function(response) {
                    KTApp.unblockPage();
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                    var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

                    var $tmp = $("<a>");
                    $tmp.attr("href",response.data);
                    $("body").append($tmp);
                    $tmp.attr("download", name + '.xlsx');
                    $tmp[0].click();
                    $tmp.remove();

                    if (code == _SUCCESS) {
                        toastr.info(message);
                    } else {
                        toastr.error(message);
                    }
                });
        
                return false;
            });

            $('.kt_datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                autoclose: true,
            });
        },
        loadReport: function(params = {}) {
            var self = this;

            KTApp.blockPage(blockOptions);
            var formData = self.formEl.serialize();
            formData = formData + '&page=' + self.page + '&number_record=' + self.number_record + '&sort_field=' + self.sort_field + '&sort_type=' + self.sort_type;

            if(nhMain.utilities.notEmpty(params.refresh)) {
                formData = {};
            }
            nhMain.callAjax({
                url: self.formEl.attr('action'),
                dataType: 'html',
                data: formData,
                async: true            
            }).done(function(response) {
                self.wrapElement.html(response);
                self.chartOrder();
                KTApp.unblockPage();
                return false;
            });
        },
        chartOrder: function() {
            var self = this;

            self.chartElement = self.wrapElement.find('#kt_amcharts_6');
            if (self.chartElement.length == 0) return false;

            var inputData = $('#data-chart-order');
            if (inputData.length == 0) return false;

            self.chartData = nhMain.utilities.parseJsonToObject(inputData.val());
            if($.isEmptyObject(self.chartData)) return false;

            var chart = AmCharts.makeChart("kt_amcharts_6", {
                "type": "serial",
                "theme": "light",
                "marginRight": 40,
                "marginLeft": 40,
                "marginTop": 50,
                "autoMarginOffset": 20,
                "mouseWheelZoomEnabled": true,
                "dataDateFormat": "DD/MM/YYYY",
                "language": "vi",
                "valueAxes": [{
                    "id": "v1",
                    "axisAlpha": 0,
                    "position": "left",
                    "ignoreAxisWidth": true
                }],
                "balloon": {
                    "borderThickness": 1,
                    "shadowAlpha": 0
                },
                "graphs": [{
                    "id": "g1",
                    "balloon": {
                        "drop": true,
                        "adjustBorderColor": false,
                        "color": "#ffffff"
                    },
                    "bullet": "round",
                    "bulletBorderAlpha": 1,
                    "bulletColor": "#FFFFFF",
                    "bulletSize": 5,
                    "hideBulletsCount": 50,
                    "lineThickness": 2,
                    "title": "red line",
                    "useLineColorForBulletBorder": true,
                    "valueField": "value",
                    "balloonText": "<span style='font-size:12px;'>[[value]]</span>"
                }],
                "chartScrollbar": {
                    "graph": "g1",
                    "oppositeAxis": false,
                    "offset": 30,
                    "scrollbarHeight": 80,
                    "backgroundAlpha": 0,
                    "selectedBackgroundAlpha": 0.1,
                    "selectedBackgroundColor": "#888888",
                    "graphFillAlpha": 0,
                    "graphLineAlpha": 0.5,
                    "selectedGraphFillAlpha": 0,
                    "selectedGraphLineAlpha": 1,
                    "autoGridCount": true,
                    "color": "#AAAAAA"
                },
                "chartCursor": {
                    "pan": true,
                    "valueLineEnabled": true,
                    "valueLineBalloonEnabled": true,
                    "cursorAlpha": 1,
                    "cursorColor": "#258cbb",
                    "limitToGraph": "g1",
                    "valueLineAlpha": 0.2,
                    "valueZoomable": true
                },
                "valueScrollbar": {
                    "oppositeAxis": false,
                    "offset": 50,
                    "scrollbarHeight": 10
                },
                "categoryField": "date",
                "categoryAxis": {
                    "parseDates": true,
                    "dashLength": 1,
                    "minorGridEnabled": true
                },
                "export": {
                    "enabled": true
                },
                "dataProvider": typeof(self.chartData) != _UNDEFINED ? self.chartData : []
            });

            chart.addListener("rendered", zoomChart);

            zoomChart();

            function zoomChart() {
                chart.zoomToIndexes(chart.dataProvider.length - 40, chart.dataProvider.length - 1);
            }

        }
    }
}

$(document).ready(function() {
    nhReport.init();
});
