"use strict";

var nhListSeoRedirect = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/redirect-301/json',
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
			keyword: $('#nh-keyword').val()
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
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				width: 18,
				textAlign: 'center',
				autoHide: false,
				sortable: false,
			},
			{
				field: 'url',
				title: nhMain.getLabel('duong_dan_cu'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var url = KTUtil.isset(row, 'url') && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/redirect-301/update/' + row.id;
					var host = window.location.origin;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a target="_blank" href="'+ host +  '/'  + url +'" class="kt-user-card-v2__name">'+ host +  '/'  + url +'</a>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'redirect',
				title: nhMain.getLabel('duong_dan_moi'),				
				width: 400,
				sortable: false,
				template: function(row) {
					var redirect = KTUtil.isset(row, 'redirect') && row.redirect != null ? row.redirect : '';
					var host = window.location.origin;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a target="_blank" href="'+ host +  '/'  + redirect +'" class="kt-user-card-v2__name">'+ host +  '/'  + redirect +'</a>\
							</div>\
						</div>';
				}
			},			
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;					
				},
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);   		   
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/redirect-301/delete',
			    	status: adminPath + '/redirect-301/change-status'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListSeoRedirect.listData();
});

