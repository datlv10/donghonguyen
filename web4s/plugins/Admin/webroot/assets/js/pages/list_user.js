"use strict";

var nhListUser = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/user/list/json',
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
				width: 30,
				type: 'number',
				selector: {class: 'select-record kt-checkbox bg-white'},
				textAlign: 'center',
			},
			{
				field: 'full_name',
				title: nhMain.getLabel('ho_ten'),
				autoHide: false,
				width: 350,
				template: function(row) {
					var urlEdit = adminPath + '/user/update/' + row.id;
					var urlDetail = adminPath + '/user/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">'+ row.full_name +'</a>\
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
				field: 'image_avatar',
				title: '<div class="text-center"><i class="fa fa-image fa-lg"></i></div>',
				sortable: false,
				width: 40,
				template: function(row) {
					var image_avatar = _NO_IMAGE;
					var images = '';

					if(KTUtil.isset(row, 'image_avatar') && row.image_avatar != null){
						image_avatar = cdnUrl + row.image_avatar;
					}
					
					var templateImage = '\
						<div class="symbol-group symbol-hover">\
							<a class="symbol symbol-circle" href="'+ image_avatar +'" data-lightbox="'+ row.id +'">\
								<img src="'+ image_avatar +'">\
							</a>';

					templateImage += '</div>';
					return templateImage;
				}
			},
			{
				field: 'role_id',
				title: nhMain.getLabel('phan_quyen'),
				template: function(row) {
					var role = KTUtil.isset(row, 'role') ? row.role : {};
					return role ? role.name : '';
				}
			},
			{
				field: 'email',
				title: 'Email',				
			},
			{
				field: 'phone',
				title: nhMain.getLabel('so_dien_thoai'),
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

		    $('#role_id').on('change', function() {
		      	datatable.search($(this).val(), 'role_id');
		    });		   
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/user/delete',
			    	status: adminPath + '/user/change-status',
			    }
		    });

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListUser.listData();
});

