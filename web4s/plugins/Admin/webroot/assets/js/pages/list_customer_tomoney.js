"use strict";

var nhCustomerPointToMoney = {
	form: $('#quick-save-request-withdrawal'),
	init: function(params = {}){
		var self = this;
		if(self.form.length == 0) return false;

		self.initInput();
		self.event();
		self.suggestItem.init();
	},
	initInput: function(){
		var self = this;
		
		self.form.find('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		self.form.find('.kt-selectpicker').selectpicker();
	},
	event: function(){
		var self = this;

		var validatorForm = self.form.validate({
			ignore: ':hidden',
			rules: {
				bank_id: {
					required: true,
				},
				point: {
					required: true,
				}
			},
			messages: {
                bank_id: {
                    required: nhMain.getLabel('vui_long_chon_ngan_hang')
                },
                point: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin_diem')
                }
            },

            errorPlacement: function(error, element) {
                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error.addClass('invalid-feedback'));
                }else{                	
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTop();
			},
		});

		$(document).on('keyup keypess keydown', '#point', function(e) {
			var pointToMoney = $(this).attr('nh-point-money');
			var pointMax = $(this).attr('nh-point-max');
			var point = $(this).val().replace(',', '');

			if(parseInt(pointMax) < parseInt(point)) {
				point = pointMax;
				$(this).val(pointMax);
			}

			var wrapElement = $(this).closest('.input-group');

			var labelPointToMoney = wrapElement.find('.point-to-money');

			labelPointToMoney.text(nhMain.utilities.parseNumberToTextMoney(pointToMoney * point));	
			$('#money').val(pointToMoney * point);
		});

		$(document).on('click', '#quick-add-request-withdrawal', function(e) {
			e.preventDefault();
			if (!validatorForm.form()){
  				return;
  			};
			nhMain.initSubmitForm(self.form, $(this));
		});
	},
	suggestItem: {
		wrapElement: null,
		type: null,
		init: function(){
			var self = this;

			self.event();
		},
		event: function(){
			var self = this;
			$(document).on('keyup keypress paste focus', 'input[suggest-item]', function(e) {
				self.wrapElement = $(this).closest('[nh-wrap-select]');
				if(self.wrapElement.length == 0) return;

				var inputSuggest = 'input[suggest-item="customer"]';
				
				nhMain.autoSuggest.basic({
					inputSuggest: inputSuggest,
					url: adminPath + '/customer/auto-suggest',
					fieldLabel: 'full_name_phone',
					filter: {
						is_partner_affiliate: 1
					},
					getParams: {
						get_point: true
					}
				}, function(response){
					if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED){
						self.addItemSelected(response);
					}
				});
				
				if(e.type == 'focusin'){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('click', '[nh-wrap-select] .tagify__tag__removeBtn', function(e) {
				$(this).closest('.tagify__tag').remove();
			});
		},
		addItemSelected: function(item = {}){
			var self = this;
			
			if($.isEmptyObject(item)) return;
			if(self.wrapElement == null || self.wrapElement.length == 0) return;
			
			var point = 0;
			if(KTUtil.isset(item, 'point') && item.point != null){
				point = nhMain.utilities.parseNumberToTextMoney(item.point);
			}

			var class_view = 'text-primary';
			if (point == 0) {
				class_view = 'text-danger';
			}

			var itemHtml = 
			'<p class="mb-0"><label class="fw-600">' + nhMain.getLabel('ho_ten') + ': </label> ' + item.full_name + '</p>\
			<p class="mb-0"><label class="fw-600 mb-0">' + nhMain.getLabel('diem_hien_tai') + ': </label><span class="'+ class_view +' fw-600"> ' + point + '</span></p>';
			self.wrapElement.find('[nh-item-selected]').html(itemHtml);
			self.wrapElement.find('input[name*=customer_id]').val(item.id);
			$('.withdrawable-points').html(point);
			$('#point').attr('nh-point-max', item.point);

			nhCustomerPointToMoney.loadListBank(item.id);
		}		
	},
	loadListBank: function(customer_id = null){
		if(typeof(customer_id) == _UNDEFINED || customer_id == null) return;

		var bankSelect = $('#bank_id');
		bankSelect.find('option:not([value=""])').remove();
		bankSelect.selectpicker('refresh');

		var _data = {};
		_data[_PAGINATION] = {};
		_data[_PAGINATION][_PERPAGE] = 100;

		nhMain.callAjax({
    		async: false,
			url: adminPath + '/customer/affiliate/point-tomoney/list-bank/json/' + customer_id,
			data: _data,
		}).done(function(response) {
			var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
        	if (code == _SUCCESS) {
            	// append option
                if (!$.isEmptyObject(data)) {
                	var listOption = '';
			        $.each(data, function (key, item) {
			        	if(item.bank_name == null) return;
			            listOption += '<option value="' + item.id + '">' + item.bank_name + ' - ' + item.bank_branch + '</option>';
			        });
			        bankSelect.append(listOption);
			        bankSelect.selectpicker('refresh');
                }		                    
            } else {
            	toastr.error(message);
            }
		});
	}
};

var nhListCustomerPointToMoney = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/customer/point-tomoney/list/json',
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
				width: 250,
				autoHide: false,
				template: function(row) {
					var name = typeof(row.full_name) != _UNDEFINED && row.full_name != null ? row.full_name : '';
					var email = typeof(row.email) != _UNDEFINED && row.email != null ? row.email : '';
					var phone = typeof(row.phone) != _UNDEFINED && row.phone != null ? row.phone : '';

					var urlDetail = adminPath + '/customer/detail/' + row.customer_id;
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
				field: 'created',
				title: nhMain.getLabel('thong_tin_yeu_cau'),				
				width: 210,
				template: function(row) {
					var created = typeof(row.created) != _UNDEFINED && row.created != null ? row.created : '';
					var note = typeof(row.note) != _UNDEFINED && row.note != null ? row.note : '...';
					var money = 0;
					var point = 0;

					if(KTUtil.isset(row, 'money') && row.money != null){
						money = nhMain.utilities.parseNumberToTextMoney(row.money);
					}

					if(KTUtil.isset(row, 'point') && row.point != null){
						point = nhMain.utilities.parseNumberToTextMoney(row.point);
					}

					return '\
					<div class="code-time nh-weight">\
						<div> <i class="far fa-clock"></i> '+ nhMain.utilities.parseIntToDateTimeString(row.created) +'</div>\
						<div><span class="kt-font-bolder">'+ nhMain.getLabel('so_tien') + ':</span><span class="kt-font-bolder text-primary"> '+ money +' VND</span></div>\
						<div class="kt-font-bolder text-primary">= '+ point + ' ' + nhMain.getLabel('diem') +'</div>\
						<div><span class="kt-font-bolder">'+ nhMain.getLabel('noi_dung') + ':</span> ' + note +'</div>\
					</div>';				
				},
			},
			{
				field: 'bank',
				title: nhMain.getLabel('ngan_hang'),
				width: 250,
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
				field: 'note_admin',
				title: nhMain.getLabel('ghi_chu_nhan_vien'),				
				width: 200,
				template: function(row) {
					var time_confirm = typeof(row.time_confirm) != _UNDEFINED && row.time_confirm != null ? row.time_confirm : '';
					var note_admin = typeof(row.note_admin) != _UNDEFINED && row.note_admin != null ? row.note_admin : '...';

					var _htmlTimeConfirm = '\
						<div><span class="kt-font-bolder">'+ nhMain.getLabel('ngay_duyet') +': </span>\
							'+ nhMain.utilities.parseIntToDateTimeString(row.time_confirm) +'\
						</div>';

					return '\
						'+ _htmlTimeConfirm +'\
						<div class="nh-note-staff">\
							' + nhList.template.changeNote(row.id, 'note_admin', note_admin, nhMain.getLabel('ghi_chu_nhan_vien')) + '\
						</div>';					
				}
			},
			{
				field: 'status',
				title: nhMain.getLabel('trang_thai'),				
				width: 110,
				template: function(row) {
					var status = typeof(row.status) != _UNDEFINED && row.status != null ? row.status : 0;
					status = nhList.template.statusCustomerPointHistory(status);

					return status;					
				}
			},
			{
				field: 'action',
				title: '',
				width: 30,
				autoHide: false,
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
			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            endDate: '0d'
  			});

  			$('.kt-selectpicker').selectpicker();

			var datatable = $('.kt-datatable').KTDatatable(options);

			$('#nh_status').on('change', function() {
		      	datatable.search($(this).val(), 'status');
		    });

		    $('#create_from').on('change', function() {
		      	datatable.search($(this).val(), 'create_from');
		    });

		    $('#create_to').on('change', function() {
		      	datatable.search($(this).val(), 'create_to');
		    });	

		    $('#confirm_from').on('change', function() {
		      	datatable.search($(this).val(), 'confirm_from');
		    });

		    $('#confirm_to').on('change', function() {
		      	datatable.search($(this).val(), 'confirm_to');
		    });	

		    // event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
		    		note: adminPath + '/customer/point-tomoney/change-note',
			    	delete: adminPath + '/customer/point-tomoney/delete',
			    	status: adminPath + '/customer/point-tomoney/change-status',
			    }
		    });
		}
	};
}();

$(document).ready(function() {
	nhCustomerPointToMoney.init();
	nhListCustomerPointToMoney.listData();
});