"use strict";

var nhListCustomerPoint = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/point/list/json',
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
				title: nhMain.getLabel('ten_khach_hang'),
				width: 270,
				autoHide: false,
				template: function(row) {
					var name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';
					var urlDetail = adminPath + '/customer/point/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ name +'</a>\
							</div>\
						</div>';
				}
			},
			{
				field: 'phone',
				title: nhMain.getLabel('so_dien_thoai'),
				width: 150,
				sortable: false,
				template: function(row) {
					var phone = typeof(row.phone) != _UNDEFINED && row.phone != null ? row.phone : '';
					return phone;
				}
			},
			{
				field: 'point',
				title: nhMain.getLabel('diem_hien_tai'),
				sortable: false,
				class: 'text-center',
				template: function(row) {
					var point = '';
					if(KTUtil.isset(row, 'point') && row.point != null){
						point = nhMain.utilities.parseNumberToTextMoney(row.point);
					}
					return '<span class="kt-font-bold text-success">'+ point +'</span>';					
				},
			},
			{
				field: 'point_promotion',
				title: nhMain.getLabel('diem_khuyen_mai'),
				sortable: false,
				class: 'text-center',
				template: function(row) {
					var point_promotion = '';
					if(KTUtil.isset(row, 'point_promotion') && row.point_promotion != null){
						point_promotion = nhMain.utilities.parseNumberToTextMoney(row.point_promotion);
					}
					return '<span class="kt-font-bold text-primary">'+ point_promotion +'</span>';		
				},
			},
			{
				field: 'expiration_time',
				title: nhMain.getLabel('thoi_han_diem'),				
				width: 120,
				template: function(row) {
					var expiration_time = '';
					if(KTUtil.isset(row, 'expiration_time') && row.expiration_time != null){
						expiration_time = nhMain.utilities.parseIntToDateTimeString(row.expiration_time);
					}
					return expiration_time;					
				},
			}
		]
	}

	return {
		listData: function() {
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

		    $('.kt-selectpicker').selectpicker();

		    var datatable = $('.kt-datatable').KTDatatable(options);
		}
	};
}();

jQuery(document).ready(function() {
	nhListCustomerPoint.listData();
});

