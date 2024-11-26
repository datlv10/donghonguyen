"use strict";

var nhListSupplier = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/supplier/list/json',
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
				field: 'name',
				title: nhMain.getLabel('ten_nha_cung_cap'),
				autoHide: false,
				template: function(row) {
					var urlDetail = adminPath + '/supplier/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ row.name +'</a>\
							</div>\
						</div>';
				}
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_nha_cung_cap'),
				width: 200,
				template: function(row) {
					var code = '';
					if(KTUtil.isset(row, 'code') && row.code != null){
						code = row.code;
					}
					return code;
				},
			},  

			{
				field: 'phone',
				title: nhMain.getLabel('so_dien_thoai'),
				width: 110,
				template: function(row) {
					var phone = '';
					if(KTUtil.isset(row, 'phone') && row.phone != null){
						phone = row.phone;
					}
					return phone;
				},
			},  
			{
				field: 'email',
				title: nhMain.getLabel('email'),
				width: 120,
				template: function(row) {
					var email = '';
					if(KTUtil.isset(row, 'email') && row.email != null){
						email = row.email;
					}
					return email;
				}
			},
			{
				field: 'supplier_item_id',
				title: '',
				width: 50,
				autoHide: false,
				sortable: false,
				template: function(row){
					var urlEdit = adminPath + '/supplier/update/' + row.id;
					var urlDetail = adminPath + '/supplier/detail/' + row.id;
					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right">\
							<a class="dropdown-item text-primary" href="' + urlDetail + '">'+ nhMain.getLabel('xem_chi_tiet') +'</a>\
							<a class="dropdown-item text-warning" href="' + urlEdit + '">'+ nhMain.getLabel('sua') +'</a>\
							<div class="dropdown-divider"></div>\
							<a href="javascript:;" class="dropdown-item text-danger nh-delete" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
						</div>\
					</div>';
				}
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh-status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });		 

		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/supplier/delete'
			    }
		    });		
		    $('.kt-selectpicker').selectpicker();    
		}
	};
}();

jQuery(document).ready(function() {
	nhListSupplier.listData();
});

