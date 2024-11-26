"use strict";

var nhListCountry = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/country/list/json',
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
				field: 'name',
				title: nhMain.getLabel('ten_quoc_gia'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var urlEdit = adminPath + '/country/update/' + row.id;
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>\
								<span class="d-block kt-user-card-v2__desc action-entire">\
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
				field: '',
				title: nhMain.getLabel('danh_sach_tinh_thanh'),
				textAlign: 'center',
				template: function(row) {
					return '<a href="' + adminPath + '/city/' + row.id + '" class="btn btn-sm btn-label-brand btn-bold">' + nhMain.getLabel('danh_sach_tinh_thanh') + '</a>';					
				},
			},
			{
				field: 'position',
				title: nhMain.getLabel('vi_tri'),
				width: 60,
				sortable: true,
				textAlign: 'center',
				template: function (row) {
					var position = '';
					if(KTUtil.isset(row, 'position') && row.position != null){
						position = nhMain.utilities.parseNumberToTextMoney(row.position);
					}
					return nhList.template.changeQuick(row.id, 'position', position, nhMain.getLabel('vi_tri'));
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),
				width: 110,
				autoHide: false,
				template: function(row) {
					var status = '';
					var draftArticle = '';
					if(KTUtil.isset(row, 'status') && row.status != null && row.draft != 1){
						status = nhList.template.statusProduct(row.status);
					}
					
					if((KTUtil.isset(row, 'draft') && row.draft == 1)){
						draftArticle = nhList.template.draftProduct(row.draft);
					}
					return status + draftArticle;

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
			    	delete: adminPath + '/country/delete',
			    	status: adminPath + '/country/change-status',
			    	quickChange: adminPath + '/country/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListCountry.listData();
});

