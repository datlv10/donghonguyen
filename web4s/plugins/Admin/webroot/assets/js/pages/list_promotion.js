"use strict";

var nhListPromotion = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/promotion/list/json',
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
				field: 'code',
				title: nhMain.getLabel('ma_chuong_trinh'),
				autoHide: false,
				width: 150
			},
			{
				field: 'name',
				title: nhMain.getLabel('ten_chuong_trinh'),
				autoHide: false,
				width: 200,
				template: function(row) {
					var name = KTUtil.isset(row, 'name') && row.name != null ? row.name : '';
					var url = typeof(row.url) != _UNDEFINED && row.url != null ? row.url : '';
					var urlEdit = adminPath + '/promotion/update/' + row.id;
					var urlDetail = adminPath + '/promotion/detail/' + row.id;

					var viewTemplate = ''
					if(url.length > 0){
						viewTemplate = '<span class="view-template kt-margin-l-5"><a target="_blank" href="/'+ url +'"><i class="fa fa-eye"></i></a></span>';
					}
					
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<a href="'+ urlEdit +'" class="d-inline kt-user-card-v2__name">'+ name +'</a>' + viewTemplate + '\
							</div>\
						</div>';
				}
			},
			{
				field: 'type_discount',
				title: nhMain.getLabel('loai_chiet_khau'),
				autoHide: false,
				width: 200,
				template: function(row) {
					var type_discount = '';
					if(KTUtil.isset(row, 'type_discount') && row.type_discount != null){
						type_discount = nhList.template.typeDiscountPromotion(row.type_discount);
					}
					return type_discount;

				},
			},
			{
				field: 'value',
				title: nhMain.getLabel('gia_tri_khuyen_mai'),
				autoHide: false,
				width: 150,
				template: function(row) {
					var value = '';
					var html = '';
					
					if(KTUtil.isset(row, 'value') && row.value != null){
						value = nhMain.utilities.parseJsonToObject(row.value);

						var value_discount = typeof(value.value_discount) != _UNDEFINED && value.value_discount != null ? parseInt(value.value_discount) : '';
						var type = typeof(value.type_value_discount) != _UNDEFINED && value.type_value_discount != null ? value.type_value_discount : 'money';

						var val_type = '';
						if (type == 'money') {
							val_type = ' VND';
						} else {
							val_type = '%';
						}

						html = '<p class="mb-5">\
									'+ nhMain.getLabel('gia_tri_ck') + ': <span class="kt-font-bolder text-primary">' + nhMain.utilities.parseNumberToTextMoney(value_discount) + val_type +'</span>\
								</p>';
					}
					return html;

				},
			},
			{
				field: 'used',
				title: nhMain.getLabel('da_su_dung'),
				autoHide: false,
				class: 'text-center',
				width: 100,
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
			},
			{
				field: 'website_id',
				title: '',
				width: 30,
				autoHide: false,
				sortable: false,
				template: function(row){
					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">\
							<a class="dropdown-item" href="' + adminPath + '/promotion/update/' + row.id + '">\
								<span class="text-primary"><i class="fas fa-eye  fs-14 mr-10"></i>'
									+ nhMain.getLabel('xem_thong_tin') +
								'</span>\
							</a>\
							<a class="dropdown-item" href="' + adminPath + '/promotion/coupon/list/' + row.id + '">\
								<span class=""><i class="fa fa-tags  fs-14 mr-10"></i>'
									+ nhMain.getLabel('them_coupon') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="1">\
								<span class="text-success"><i class="fas fa-check-circle  fs-14 mr-10"></i>'
									+ nhMain.getLabel('hoat_dong') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="0">\
								<span class="text-warning"><i class="fas fa-times-circle  fs-14 mr-10"></i>'
									+ nhMain.getLabel('ngung_hoat_dong') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-delete" href="javascript:;" data-id="'+ row.id +'">\
								<span class="text-danger"><i class="fas fa-trash-alt  fs-14 mr-10"></i>'
									+ nhMain.getLabel('xoa') +
								'</span>\
							</a>\
						</div>\
					</div>';
				}
			}]
	};

	return {
		listData: function() {
			var datatable = $('.kt-datatable').KTDatatable(options);
		    $('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#nh_type_discount').on('change', function() {
		      	datatable.search($(this).val(), 'type_discount');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            endDate: '0d'
  			});
		    
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/promotion/delete',
			    	status: adminPath + '/promotion/change-status',
			    	quickChange: adminPath + '/promotion/change-position'
			    }
		    });

		    $('.kt-selectpicker').selectpicker();

		    lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });            
		}
	};
}();

jQuery(document).ready(function() {
	nhListPromotion.listData();
});

