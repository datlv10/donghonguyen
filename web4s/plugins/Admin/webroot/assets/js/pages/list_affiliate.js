"use strict";

var nhListCustomer = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/affiliate/list/json',
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
				field: 'full_name',
				title: nhMain.getLabel('ten_doi_tac'),
				width: 220,
				autoHide: false,
				template: function(row) {
					var name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';

					var urlDetail = adminPath + '/customer/affiliate/detail/' + row.customer_id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details lh-auto">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">\
									'+ name +'\
								</a>\
							</div>\
						</div>';
				}
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_gioi_thieu'),
				class: 'text-center',
				sortable: false,
				template: function(row) {
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';

					return '<span class="kt-font-bold text-primary">'+ code +'</span>';	
				}
			},
			{
				field: 'level',
				title: nhMain.getLabel('hang_doi_tac'),
				class: 'text-center',
				sortable: false,
				template: function(row) {
					var level = typeof(row.level) != _UNDEFINED && row.level != null ? row.level : nhMain.getLabel('chua_co_hang');

					return level;	
				}
			},
			{
				field: 'order',
				title: nhMain.getLabel('don_hang'),
				sortable: false,
				template: function(row) {
					var number_order = typeof(row.number_referral) != _UNDEFINED && row.number_referral != null ? parseInt(row.number_referral) : 0;

					var number_order_success = typeof(row.number_order_success) != _UNDEFINED && row.number_order_success != null ? parseInt(row.number_order_success) : 0;
					var total_order_success = typeof(row.total_order_success) != _UNDEFINED && row.total_order_success != null ? nhMain.utilities.parseNumberToTextMoney(row.total_order_success) : 0;

					var number_order_failed = typeof(row.number_order_failed) != _UNDEFINED && row.number_order_failed != null ? parseInt(row.number_order_failed) : 0;
					var total_order_failed = typeof(row.total_order_failed) != _UNDEFINED && row.total_order_failed != null ? nhMain.utilities.parseNumberToTextMoney(row.total_order_failed) : 0;

					return '\
						<div>\
							<span class="kt-font-bold">'+ nhMain.getLabel('tong_don_hang') +': </span>\
							'+ number_order +'\
						<div>\
						<div>\
							<span class="kt-font-bold">'+ nhMain.getLabel('don_thanh_cong') +': </span>\
							<span class="kt-font-bold text-success">'+ number_order_success +' = '+ total_order_success +' VND</span>\
						</div>\
						<div>\
							<span class="kt-font-bold">'+ nhMain.getLabel('don_that_bai') +': </span>\
							<span class="kt-font-bold text-danger">'+ number_order_failed +' = '+ total_order_failed +' VND</span>\
						</div>\
					';	
				}
			},
			{
				field: 'number_referral',
				title: nhMain.getLabel('luot_gioi_thieu'),
				class: 'text-center',
				sortable: false,
				template: function(row) {
					var number_referral = typeof(row.number_referral) != _UNDEFINED && row.number_referral != null ? parseInt(row.number_referral) : 0;

					return '<span class="kt-font-bold text-primary">'+ number_referral +'</span>';	
				}
			},
			{
				field: 'total_point',
				title: nhMain.getLabel('hoa_hong'),		
				class: 'text-center',
				template: function(row) {
					var total_point_to_money = 0;
					var total_point = 0;

					if(KTUtil.isset(row, 'total_point_to_money') && row.total_point_to_money != null){
						total_point_to_money = nhMain.utilities.parseNumberToTextMoney(row.total_point_to_money);
					}

					if(KTUtil.isset(row, 'total_point') && row.total_point != null){
						total_point = nhMain.utilities.parseNumberToTextMoney(row.total_point);
					}
					return '\
						<div class="kt-font-bold">'+ total_point_to_money +' VNĐ</div>\
						<div class="">= '+ total_point + ' ' +nhMain.getLabel('diem') +'</div>';
				}
			}
		]
	}

	return {
		listData: function() {
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			var datatable = $('.kt-datatable').KTDatatable(options);

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });
			
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/customer/affiliate-request/delete',
			    	status: adminPath + '/customer/affiliate-request/change-status',
			    }
		    });
		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListCustomer.listData();
});