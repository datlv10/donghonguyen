"use strict";

var nhListNotificationSubscribe = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/notification/subscribe/list/json',
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

		data_filter: {
			lang: nhMain.lang,
			keyword: $('#nh-keyword').val(),
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
				field: 'id',
				title: '',
				width: 18,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},
			{
				field: 'token',
				title: 'Token Device',
				autoHide: false,
				width: 600,
				template: function(row) {
					var token = KTUtil.isset(row, 'token') && row.token != null ? row.token : '';
					var urlDetail = adminPath + '/notification/subscribe/detail/' + row.id;
					return token;
				}
			},
			{
				field: 'platform',
				title: nhMain.getLabel('nen_tang'),
				sortable: false,
				textAlign: 'center',
				width: 90,
				template: function(row) {
					var platform = nhMain.utilities.notEmpty(row.platform) ? row.platform : '';
					var listPlatform = {
						web: 'Website',
						ios: 'IOS',						
						android: 'Android'
					};

					var platformText = typeof(listPlatform[platform]) != _UNDEFINED ? listPlatform[platform] : '';
					return '\
						<span class="kt-section__content kt-section__content--solid mr-10">\
							<span title="'+ platformText +'">' + platformText + '</span>\
						</span>'
				}
			},
			{
				field: 'created',
				title: nhMain.getLabel('ngay_dang_ky'),
				sortable: false,
				width: 150,
				template: function(row) {
					var created = '';
					if(KTUtil.isset(row, 'created') && row.created != null){
						created = nhMain.utilities.parseIntToDateTimeString(row.created);
					}
					return created;
				},
			},
			{
				field: 'action',
				title: '',
				width: 30,
				autoHide: false,
				sortable: false,
				template: function(row){
					var _htmlAddAccount = '';
					if(!KTUtil.isset(row, 'username') && row.username == null){
						_htmlAddAccount = '\
						<a class="dropdown-item" nh-add-account href="javascript:;" data-id="'+ row.id +'" data-toggle="modal" data-target="#modal-add-account">\
							<span class="text-primary"><i class="fas fa-user-plus fs-14 mr-10"></i>'
								+ nhMain.getLabel('them_tai_khoan') +
							'</span>\
						</a>';
					}
					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">\
							<a class="dropdown-item nh-delete" href="javascript:;" data-id="'+ row.id +'">\
								<span class="text-danger"><i class="fas fa-trash-alt fs-14 mr-10"></i>'
									+ nhMain.getLabel('xoa') +
								'</span>\
							</a>\
						</div>\
					</div>';
				}
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/notification/subscribe/delete'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListNotificationSubscribe.listData();
});

