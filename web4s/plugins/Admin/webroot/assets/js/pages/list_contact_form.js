"use strict";

var nhListContactForm = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/contact/form/list/json',
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
				field: 'name',
				title: nhMain.getLabel('ten_form'),
				sortable: false,
				template: function(row){

					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var urlEdit = adminPath + '/contact/form/update/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<span class="kt-user-card-v2__name">'+ name +'</span>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},

			{
				field: 'code',
				title: nhMain.getLabel('ma_form'),
				sortable: false,
				width: 220,
				template: function(row){
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';
					return '\
					<div class="code-time nh-weight">\
						<div>' + code + '</div>\
					</div>';
				}
			},			

			{
				field: 'send_email',
				title: nhMain.getLabel('gui_email'),
				sortable: false,
				width: 120,
				template: function(row) {
					var send_email = '';
					var statusOptions = {
						0: {'title': nhMain.getLabel('khong_gui'), 'class': 'kt-badge--dark kt-font-bold'},
						1: {'title': nhMain.getLabel('co_gui'), 'class': 'kt-badge--success kt-font-bold'},
					};
					if(KTUtil.isset(row, 'send_email') && row.send_email != null){
						send_email = '<span class="kt-badge ' + statusOptions[row.send_email].class + ' kt-badge--inline kt-badge--pill">' + statusOptions[row.send_email].title + '</span>';
					}
					return send_email;
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
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });
		    
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/contact/form/delete',
			    }
		    });

			$('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListContactForm.listData();
});

