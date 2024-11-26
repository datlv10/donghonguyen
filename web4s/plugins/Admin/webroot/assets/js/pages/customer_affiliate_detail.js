"use strict";
var customer_id = $('#customer_id').val();

var nhCustomerAffiliate = {
    init: function(){
    	var self = this;     

    	self.statistics.init();

    	$('.kt_datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true,
            endDate: '0d'
		}); 
    },
    statistics: {
        wrapElement: null,
        init: function(){
            var self = this;

            self.wrapElement = $('#wrap-dashboard-statistic-element');
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
        },
        loadStatistic: function(filter_date = null){
            var self = this;

            nhMain.callAjax({
                url: adminPath + '/customer/affiliate/load-statistic-dashboard',
                dataType: 'html',
                data: {
                	customer_id: customer_id,
                    filter_date: filter_date
                }                    
            }).done(function(response) {
                self.wrapElement.html(response);

                $('.kt_datepicker').datepicker({
		            format: 'dd/mm/yyyy',
		            todayHighlight: true,
		            autoclose: true,
		            endDate: '0d'
				});
                return false
            });
        },
    },
}

var nhListOrderAffiliate = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/affiliate/list-order/json/' + customer_id,
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
			scroll: false,
			footer: false,
			class: 'table-hover',
		},

		sortable: true,

		pagination: true,
		extensions: {
			checkbox: true
		},
		search: {
			input: $('#nh-keyword'),
		},

		translate: {
            records: {
                processing: nhMain.getLabel('vui_long_cho') +  ' ...',
                noRecords: nhMain.getLabel('khong_co_ban_ghi_nao'),
            }
        },

		columns: [
			{
				field: 'code',
				title: nhMain.getLabel('ma_don_hang'),
				sortable: false,
				width: 150,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var detailOrderUrl = adminPath + '/order/detail/' + row.order_id;

					return '\
					<div class="code-time nh-weight">\
						<div> <i class="fas fa-qrcode"></i> <a href="' + detailOrderUrl + '">' + code +'</a></div>\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateTimeString(row.created) +'</div>\
					</div>';
				}
			},
			{
				field: 'total',
				title: nhMain.getLabel('gia_tri_don_hang'),
				width: 150,
				class: 'text-center',
				template: function (row) {
					var total = '';
					if(KTUtil.isset(row, 'total') && row.total != null){
						total = nhMain.utilities.parseNumberToTextMoney(row.total);
					}
					return total;
				}
			},
			{
				field: 'profit_value',
				title: nhMain.getLabel('phan_tram_hoa_hong'),
				sortable: false,
				width: 150,
				class: 'text-center',
				template: function(row){
					var profit_value = typeof(row.profit_value) != _UNDEFINED && row.profit_value != null ? parseInt(row.profit_value) : '';

					return '<span class="kt-font-bold text-primary">' + profit_value +'%</span>';
				}
			},
			{
				field: 'profit_money',
				title: nhMain.getLabel('hoa_hong_doi_tac'),
				width: 150,
				class: 'text-center',
				template: function (row) {
					var profit_money = 0;
					var profit_point = 0;

					if(KTUtil.isset(row, 'profit_money') && row.profit_money != null){
						profit_money = nhMain.utilities.parseNumberToTextMoney(row.profit_money);
					}

					if(KTUtil.isset(row, 'profit_point') && row.profit_point != null){
						profit_point = nhMain.utilities.parseNumberToTextMoney(row.profit_point);
					}
					return '\
						<div>'+ profit_money +' VNĐ</div>\
						<div>= '+ profit_point + ' ' +nhMain.getLabel('diem') +'</div>';
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				sortable: false,
				width: 120,
				autoHide: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.statusOrders(row.status);
					}
					if(KTUtil.isset(row, 'customer_cancel') && row.customer_cancel == 1){
						status = nhList.template.statusOrders(_CUSTOMER_CANCEL);
					}
					return status;
				},
			}
		]
	}

	return {
		listData: function() {
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            endDate: '0d'
  			});

			var datatable = $('#list_order_affiliate').KTDatatable(options);

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#price_from').on('change', function() {
		      	datatable.search($(this).val(), 'price_from');
		    });

		    $('#price_to').on('change', function() {
		      	datatable.search($(this).val(), 'price_to');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	
			
		    $('.kt-selectpicker').selectpicker();
		}
	};
}();


$(document).ready(function() {
	nhCustomerAffiliate.init();
	nhListOrderAffiliate.listData();
});
