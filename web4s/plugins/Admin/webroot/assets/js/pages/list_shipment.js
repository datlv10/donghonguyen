"use strict";
var nhListShipment = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/shipment/list/json',
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
				title: nhMain.getLabel('ma_van_don'),
				width: 100,
				sortable: true,
				template: function(row) {
					var urlDetail = adminPath + '/shipment/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ row.code +'</a>\
							</div>\
						</div>';
				}
			},

			{
				field: 'info',
				title: nhMain.getLabel('nguoi_nhan'),
				sortable: false,
				template: function(row) {
					if(KTUtil.isset(row, 'full_name') && row.full_name != null || KTUtil.isset(row, 'phone') && row.phone != null || KTUtil.isset(row, 'full_address') && row.full_address != null){
						return row.full_name + ' - ' + row.phone + '<br />' + row.full_address;
					}				
				},
			},

			{
				field: 'cod_money',
				title: nhMain.getLabel('tien_thu_ho'),
				width: 120,
				template: function(row) {
					return nhMain.utilities.parseNumberToTextMoney(nhMain.utilities.parseFloat(row.cod_money));
						
				},
			},

			{
				field: 'shipping_fee',
				width: 120,
				title: nhMain.getLabel('phi_van_chuyen'),	
				template: function(row) {
					return nhMain.utilities.parseNumberToTextMoney(nhMain.utilities.parseFloat(row.shipping_fee));
				},			
			},

			{
				field: 'shipping_method',
				title: nhMain.getLabel('phuong_thuc_van_chuyen'),
				template: function(row) {
					var shipping_method = '';

					var method = {
						'received_at_store': nhMain.getLabel('nhan_tai_cua_hang'),
						'normal_shipping': nhMain.getLabel('tu_van_chuyen'),
						'carrier_shipping': nhMain.getLabel('gui_qua_hang_van_chuyen'),
					};
					if(KTUtil.isset(row, 'shipping_method') && row.shipping_method != null){
						shipping_method = '<span class="">' + method[row.shipping_method] + '</span>';
					}
					return shipping_method;
				}
			},

			{
				field: 'created',
				title: nhMain.getLabel('ngay_tao'),
				width: 120,				
				template: function(row) {
					if(KTUtil.isset(row, 'created') && row.created != null){
						return nhMain.utilities.parseIntToDateTimeString(row.created);
					}
				},
			},

			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 150,
				textAlign: 'right',
				autoHide: false,
				template: function(row) {
					var status = '';
					var statusOptions = {};
					statusOptions[_WAIT_DELIVER] = {'title': nhMain.getLabel('cho_lay_hang'), 'class': ' kt-badge kt-badge--danger kt-badge--inline kt-badge--rounded'};
					statusOptions[_DELIVERY] = {'title': nhMain.getLabel('dang_giao_hang'), 'class': ' kt-badge kt-badge--brand kt-badge--inline kt-badge--rounded'};
					statusOptions[_DELIVERED] = {'title': nhMain.getLabel('da_giao_hang'), 'class': ' kt-badge kt-badge--success kt-badge--inline kt-badge--rounded'};
					statusOptions[_CANCEL_PACKAGE] = {'title': nhMain.getLabel('huy_dong_goi'), 'class': ' kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded'};
					statusOptions[_CANCEL_WAIT_DELIVER] = {'title': nhMain.getLabel('huy_giao_va_cho_nhan'), 'class': ' kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded'};
					statusOptions[_CANCEL_DELIVERED] = {'title': nhMain.getLabel('huy_giao_va_da_nhan'), 'class': ' kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded'};

					if(KTUtil.isset(row, 'status') && row.status != null){
						return '<span class="kt-badge ' + statusOptions[row.status].class + ' kt-badge--inline kt-badge--pill">' + statusOptions[row.status].title + '</span>';
					}
				},
			},
		]
	}

	return {
		listData: function() {
			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
  			});

  			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			var datatable = $('.kt-datatable').KTDatatable(options);

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });	

		    $('#cod_money_from').on('change', function() {
		      	datatable.search($(this).val(), 'cod_money_from');
		    });	

		    $('#cod_money_to').on('change', function() {
		      	datatable.search($(this).val(), 'cod_money_to');
		    });	

		    $('#shipping_fee_from').on('change', function() {
		      	datatable.search($(this).val(), 'shipping_fee_from');
		    });	

		    $('#shipping_fee_to').on('change', function() {
		      	datatable.search($(this).val(), 'shipping_fee_to');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });

		    $('#shipping_method').on('change', function() {
		      	datatable.search($(this).val(), 'shipping_method');
		    });
		    
			$('.kt-selectpicker').selectpicker();

			nhList.eventDefault(datatable);
		}
	};
}();

jQuery(document).ready(function() {
	nhListShipment.listData();
});

