"use strict";

var nhListNotification = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/notification/sent/list/json',
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
			status: $('#nh-status').val()
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
				field: 'title',
				title: nhMain.getLabel('thong_bao'),
				autoHide: false,
				width: 300,
				template: function(row) {
					var notification = typeof(row.Notifications) != _UNDEFINED && row.Notifications != null ? row.Notifications : {};
					var title = typeof(notification.title) != _UNDEFINED ? notification.title : '';
					return title;
				}
			},
			{
				field: 'platform',
				title: nhMain.getLabel('nen_tang'),
				autoHide: false,
				width: 100,
				template: function(row) {
					var listPlatform = {
						all: 'All',
						web: 'Website',
						ios: 'IOS',
						android: 'Android',
						token: 'Token'
					}

					var platform = KTUtil.isset(row, 'platform') && row.platform != null ? row.platform : '';
					var platformText = typeof(listPlatform[platform]) != _UNDEFINED ? listPlatform[platform] : '';
					return platformText;
				}
			},
			{
				field: 'created_by',
				title: nhMain.getLabel('nguoi_tao'),
				width: 120,
				sortable: false,
				template: function(row) {
					return nhList.template.createdBy(row);
				}
			}
		]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		}
	};
}();


jQuery(document).ready(function() {
	nhListNotification.listData();
});

