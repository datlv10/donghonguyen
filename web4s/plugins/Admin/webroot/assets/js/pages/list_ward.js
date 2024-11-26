"use strict";
var districtId = $('#nh-district-id').val();
var nhListWard = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/ward/list/json/' + districtId,
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
			input: $('#nh-keyword')
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
				title: 'ID',
				width: 50,
				autoHide: false,
				textAlign: 'center',
			},
			{
				field: 'name',
				title: nhMain.getLabel('phuong_xa'),
				autoHide: false,
				width: 400,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var districtId = typeof(row.district_id) != _UNDEFINED && row.district_id != null ? row.district_id : '';
					var urlEdit = adminPath + '/ward/update/' + districtId + '/' + row.id;
					
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
				field: 'position',
				title: nhMain.getLabel('vi_tri'),
				width: 100,
				textAlign: 'center',
				template: function (row) {
					var position = '';
					if(KTUtil.isset(row, 'position') && row.position != null){
						position = nhMain.utilities.parseNumberToTextMoney(parseInt(row.position));
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
					if(KTUtil.isset(row, 'status') && row.status != null && row.draft != 1){
						status = nhList.template.statusProduct(row.status);
					}

					return status;

				},
			}]

	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);   	
			// event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/ward/delete',
			    	status: adminPath + '/ward/change-status',
			    	quickChange: adminPath + '/ward/change-position'
			    }
		    });
		}
	};
}();

jQuery(document).ready(function() {
	nhListWard.listData();
});

