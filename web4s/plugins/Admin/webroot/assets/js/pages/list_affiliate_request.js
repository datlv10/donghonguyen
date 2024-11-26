"use strict";

var nhListCustomer = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/affiliate/request/list/json',
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
				title: nhMain.getLabel('doi_tac'),
				width: 240,
				autoHide: false,
				template: function(row) {
					var name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';
					var email = typeof(row.email) != _UNDEFINED && row.email != null ? row.email : '';
					var phone = typeof(row.phone) != _UNDEFINED && row.phone != null ? row.phone : '';

					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details lh-1-5">\
								<p class="kt-user-card-v2__name mb-0">\
									<span class="kt-font-bolder">'+ nhMain.getLabel('ho_ten') +': </span>\
									'+ name +'\
								</p>\
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
				field: 'identity_card',
				title: nhMain.getLabel('so_cmnd'),
				width: 150,
				sortable: false,
				template: function(row) {
					var identity_card_id = typeof(row.identity_card_id) != _UNDEFINED && row.identity_card_id != null ? row.identity_card_id : '';
					var identity_card_date = typeof(row.identity_card_date) != _UNDEFINED && row.identity_card_date != null ? row.identity_card_date : '';

					return '\
						<p class="mb-0">\
							'+ identity_card_id +'\
						</p>\
						<p class="mb-0">\
							'+ identity_card_date +'\
						</p>';
				}
			},
			{
				field: 'bank',
				title: nhMain.getLabel('ngan_hang'),
				width: 350,
				template: function(row) {
					var bank_name = typeof(row.bank_name) != _UNDEFINED && row.bank_name != null ? row.bank_name : '';
					var bank_branch = typeof(row.bank_branch) != _UNDEFINED && row.bank_branch != null ? row.bank_branch : '';
					var account_number = typeof(row.account_number) != _UNDEFINED && row.account_number != null ? row.account_number : '';
					var account_holder = typeof(row.account_holder) != _UNDEFINED && row.account_holder != null ? row.account_holder : '';

					return '\
						<p class="mb-0">\
							'+ bank_name + ' - ' + bank_branch +'\
						</p>\
						<p class="mb-0">\
							'+ account_number +'\
						</p>\
						<p class="mb-0">\
							'+ account_holder +'\
						</p>';
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),				
				width: 110,
				template: function(row) {
					var status = typeof(row.status) != _UNDEFINED && row.status != null ? row.status : 0;
					status = nhList.template.statusAffiliate(status);

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

					var statusHtml = '';

					if(typeof(row.status) != _UNDEFINED && row.status == 2){
						statusHtml = '<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="1">\
										<span class="text-success"><i class="fas fa-check-circle fs-14 mr-10"></i>'
											+ nhMain.getLabel('duyet_yeu_cau') +
										'</span>\
									</a>\
									<a class="dropdown-item nh-change-status" href="javascript:;" data-id="'+ row.id +'" data-status="0">\
										<span class="text-warning"><i class="fas fa-times-circle fs-14 mr-10"></i>'
											+ nhMain.getLabel('khong_duyet') +
										'</span>\
									</a>';
					}

					return '\
					<div class="dropdown dropdown-inline">\
						<button type="button" class="btn btn-clean btn-icon btn-sm btn-icon-md" data-toggle="dropdown">\
							<i class="flaticon-more"></i>\
						</button>\
						<div class="dropdown-menu dropdown-menu-right pt-5 pb-5">'
							+ statusHtml +
							'<a class="dropdown-item nh-delete" href="javascript:;" data-id="'+ row.id +'">\
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

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });
			
		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/customer/affiliate/request/delete',
			    	status: adminPath + '/customer/affiliate/request/change-status',
			    }
		    });
		    $('.kt-selectpicker').selectpicker();
		}
	};
}();

jQuery(document).ready(function() {
	nhListCustomer.listData();
});