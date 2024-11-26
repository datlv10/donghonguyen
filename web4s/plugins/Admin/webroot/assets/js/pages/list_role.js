"use strict";

var nhListRole = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/role/list/json',
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
	            title: '#',
	            sortable: false,
	            width: 30,
	            selector: {class: 'select-record kt-checkbox bg-white'},
	            textAlign: 'center',
	        },
			{
				field: 'name',
				title: nhMain.getLabel('nhom_quyen'),
				template: function(row) {
					var urlEdit = adminPath + '/role/update/' + row.id;
					var urlDetail = adminPath + '/role/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ row.name +'</a>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="' + urlEdit + '" class="text-info action-item">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},
			{
				field: 'short_description',
				title: nhMain.getLabel('mo_ta_ngan'),
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
			    	delete: adminPath + '/role/delete',
			    }
		    }); 
		}
	};
}();

jQuery(document).ready(function() {
	nhListRole.listData();
});