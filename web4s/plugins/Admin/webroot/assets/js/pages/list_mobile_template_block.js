"use strict";

var nhListMobileBlock = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/mobile-app/block/list/json',
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
				title: nhMain.getLabel('tieu_de'),
				autoHide: false,
				width: 350,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var urlEdit = adminPath + '/mobile-app/block/update/' + row.code;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="kt-user-card-v2__name">'+ name +'</a>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-change-status" data-id="'+ row.id +'" data-status="1">'+ nhMain.getLabel('hoat_dong') +'</a>\
									<a href="javascript:;" class="action-item nh-change-status" data-id="'+ row.id +'" data-status="0">'+ nhMain.getLabel('ngung_hoat_dong') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_block'),
				template: function(row) {
					var code = KTUtil.isset(row, 'code') && row.code != null ? row.code : '';
					return '<b>'+ code +'</b>';
				}
			},
			{
				field: 'type',
				title: nhMain.getLabel('loai_block'),
				template: function(row) {
					var type = KTUtil.isset(row, 'type_label') && row.type_label != null ? row.type_label : '';
					return '<b>'+ type +'</b>';
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

		    $('#type').on('change', function() {
		      	datatable.search($(this).val(), 'type');
		    });	   
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/mobile-app/block/delete',
			    	status: adminPath + '/mobile-app/block/change-status',
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListMobileBlock.listData();
});

