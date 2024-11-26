"use strict";

var nhListCustomer = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/list/json',
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
				field: 'full_name',
				title: nhMain.getLabel('khach_hang'),
				width: 300,
				autoHide: false,
				template: function(row) {
					var name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';
					var email = typeof(row.email) != _UNDEFINED && row.email != null ? row.email : '';
					var phone = typeof(row.phone) != _UNDEFINED && row.phone != null ? row.phone : '';

					var urlEdit = adminPath + '/customer/update/' + row.id;
					var urlDetail = adminPath + '/customer/detail/' + row.id;
					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details lh-1-5">\
								<a href="'+ urlDetail +'" class="kt-user-card-v2__name">\
									<span class="kt-font-bolder">'+ nhMain.getLabel('ho_ten') +': </span>\
									'+ name +'\
								</a>\
								<p class="mb-0">\
									<span class="kt-font-bolder">'+ nhMain.getLabel('so_dien_thoai') +': </span>\
									'+ phone +'\
								</p>\
								<p class="mb-0">\
									<span class="kt-font-bolder">'+ nhMain.getLabel('email') +': </span>\
									'+ email +'\
								</p>\
							</div>\
						</div>';
				}
			},
			{
				field: 'code',
				title: nhMain.getLabel('ma_khach_hang'),
				width: 150,
				sortable: false,
				template: function(row) {
					var code = typeof(row.code) != _UNDEFINED && row.code != null ? row.code : '';

					return '<span class="kt-font-bolder text-primary">' + code + '</span>';
				}
			},
			{
				field: 'username',
				title: nhMain.getLabel('tai_khoan'),
				width: 200,
				sortable: false,
				template: function(row) {
					var status_account = KTUtil.isset(row, 'account_status') && row.account_status != null ? row.account_status : null;
					var username = typeof(row.username) != _UNDEFINED && row.username != null ? row.username : '';

					var _htmlAccount = '<span class="text-danger">'+ nhMain.getLabel('chua_thiet_lap') +'</span>';

					if (typeof(row.username) != _UNDEFINED && row.username != null && status_account == 2) {
						_htmlAccount = '<span class="text-warning">'+ username +'</span>';
					}

					if (typeof(row.username) != _UNDEFINED && row.username != null && status_account == 1) {
						_htmlAccount = '<span class="text-success">'+ username +'</span>';
					}


					return _htmlAccount;
				}
			},
			{
				field: 'full_address',
				title: nhMain.getLabel('dia_chi'),
				sortable: false
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),				
				width: 110,
				autoHide: false,
				template: function(row) {
					var status = '';
					if(KTUtil.isset(row, 'status') && row.status != null){
						status = nhList.template.status(row.status);
					}
					return status;					
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

					if(!KTUtil.isset(row, 'account_id') && row.account_id == null){
						_htmlAddAccount = '\
						<a class="dropdown-item" nh-add-account href="javascript:;" data-id="'+ row.id +'" data-toggle="modal" data-target="#modal-add-account">\
							<span class="text-primary"><i class="fas fa-user-plus fs-14 mr-10"></i>'
								+ nhMain.getLabel('them_tai_khoan') +
							'</span>\
						</a>';
					}

					if(KTUtil.isset(row, 'account_id') && row.account_id != null && row.account_status == 2){
						_htmlAddAccount = '\
						<a class="dropdown-item" nh-active-account href="javascript:;" data-id="'+ row.id +'" data-toggle="modal" data-target="#modal-account-status">\
							<span class="text-success"><i class="fas fa-user-plus fs-14 mr-10"></i>'
								+ nhMain.getLabel('kich_hoat_tai_khoan') +
							'</span>\
						</a>';
					}

					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">\
							<a class="dropdown-item" href="' + adminPath + '/customer/detail/' + row.id + '">\
								<span class="text-primary"><i class="fas fa-eye fs-14 mr-10"></i>'
									+ nhMain.getLabel('xem_thong_tin') +
								'</span>\
							</a>\
							' + _htmlAddAccount + '\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="1">\
								<span class="text-success"><i class="fas fa-check-circle fs-14 mr-10"></i>'
									+ nhMain.getLabel('hoat_dong') +
								'</span>\
							</a>\
							<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="0">\
								<span class="text-warning"><i class="fas fa-times-circle fs-14 mr-10"></i>'
									+ nhMain.getLabel('ngung_hoat_dong') +
								'</span>\
							</a>\
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
	}

	return {
		listData: function() {
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			var datatable = $('.kt-datatable').KTDatatable(options);

			$('#nh_phone').on('keyup', function() {
		      	datatable.search($(this).val(), 'phone');
		    });

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });
			
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/customer/delete',
			    	status: adminPath + '/customer/change-status',
			    	quickChange: adminPath + '/customer/quick-change',
			    }
		    });

		    $(document).on('click', '[nh-export]', function(e) {
                e.preventDefault();
                KTApp.blockPage(blockOptions);
                var nhExport = typeof($(this).attr('nh-export')) != _UNDEFINED ? $(this).attr('nh-export') : '';
                var page = typeof(datatable.getCurrentPage()) != _UNDEFINED ? datatable.getCurrentPage() : 1;

                var data_filter = {
					lang: nhMain.lang,
					keyword: $('#nh-keyword').val(),
					status: $('[name=status]').val()
				}

                nhMain.callAjax({
                    url: adminPath + '/customer/list/json',
					data: {
						'data_filter': data_filter,
						'pagination': {page: page},
						'export': nhExport
					}
                }).done(function(response) {
                    KTApp.unblockPage();
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                    var name = typeof(response.meta.name) != _UNDEFINED ? response.meta.name : '';

                    var $tmp = $("<a>");
                    $tmp.attr("href",response.data);
                    $("body").append($tmp);
                    $tmp.attr("download", name + '.xlsx');
                    $tmp[0].click();
                    $tmp.remove();

                    if (code == _SUCCESS) {
                        toastr.info(message);
                    } else {
                        toastr.error(message);
                    }
                });
        
                return false;
            });	

		    // add account
	      	var formAccount = $('#account-form');
	      	var formAccountStatus = $('#account-status-form');

	    	$(document).on('click', '[nh-add-account]', function(e) {
		      	var customer_id = $(this).data('id');

		      	if(typeof(customer_id) == _UNDEFINED || customer_id == '') return;
		      	if(formAccount == null || formAccount.length == 0) return;

		      	formAccount.attr('action', '/admin/customer/add-account/' + customer_id);
		    });

		    $(document).on('click', '[nh-active-account]', function(e) {
		      	var customer_id = $(this).data('id');

		      	if(typeof(customer_id) == _UNDEFINED || customer_id == '') return;
		      	if(formAccountStatus == null || formAccountStatus.length == 0) return;

		      	formAccountStatus.attr('action', '/admin/customer/account-status/' + customer_id);
		    });

		    $(document).on('click', '.btn-account-save', function(e) {
				e.preventDefault();
				if(formAccount == null || formAccount.length == 0) return;

				nhMain.initSubmitForm(formAccount, $(this));
			});

			$(document).on('click', '.btn-account-status', function(e) {
				e.preventDefault();
				nhMain.initSubmitForm(formAccountStatus, $(this));
			});

		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListCustomer.listData();
});