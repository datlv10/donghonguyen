"use strict";

var nhAddOrderSource = {
	data: {
		idModal: '#add-order-source',
		idForm: '#add-order-source-form',
		idBtnSave: '#btn-save-order-source',
		idBtnAdd: '#btn-add-order-source',
		btnEdit: '.nh-change-edit'
	},
	formEl: null,
	validatorAddOrderSource: null,

	init: function(formEl){
		var self = this;
		self.formEl = formEl;

		nhMain.validation.phoneVn();
		var validatorAddOrderSource = $(self.data.idForm).validate({
			rules: {
				name: {
					required: true,
					minlength: 3,
					maxlength: 255
				}			
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
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
				KTUtil.scrollTo(validatorAddOrderSource.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});

		$(document).on('click', self.data.btnEdit, function(e) {
			self.clearInputModal();
			var _id = $(this).data('id');
			var _name = $(this).data('name');
			var _code = $(this).data('code');
			$(self.data.idForm).attr('action', adminPath + '/source/save/' + _id);

			if(typeof(_name) == _UNDEFINED || _name.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }
			var _modal = $(self.data.idModal);
			_modal.find('input[name="name"]').val(typeof(_name) != _UNDEFINED ? _name : '');
			_modal.find('input[name="code"]').val(typeof(_code) != _UNDEFINED ? _code : '');

			$(self.data.idModal).modal('show');
		});

		$(document).on('click', self.data.idBtnAdd, function(e) {
			self.clearInputModal();
		});

		$(document).on('click', self.data.idBtnSave, function(e) {
			var _form = $(self.data.idForm);
			if(validatorAddOrderSource.form()){
				KTApp.blockPage(blockOptions);

				var formData = _form.serialize();
				nhMain.callAjax({
					url: _form.attr('action'),
					data: formData
				}).done(function(response) {
					KTApp.unblockPage();

				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	toastr.clear();

		            if (code == _SUCCESS) {
		            	toastr.info(message);		 
		            	location.reload();           
		            } else {
		            	toastr.error(message);
		            }

		            $(self.address.idModal).modal('hide');
				});
			}

			return false;
		});
	},

	clearInputModal: function(){
		var self = this;
		var _modal = $(self.data.idModal);		
		_modal.find('input').val('');
	},
}

var nhListOrderSource = function() {
	var options = {
		data: {
			type: 'remote',
			source: {
				read: {
					url: adminPath + '/source/list/json',
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
				title: nhMain.getLabel('ten_nguon_don_hang'),
				template: function(row) {
					var isSystem = {
						0: {'check': ''},
						1: {'check': 'd-none'}						
					};
					var isDefault = '';
					if(row.is_system == 0) {
						var listDefault = {
							0: {'check': ''},
							1: {'check': 'd-none'}						
						};
						isDefault = listDefault[row.is_default].check
					}

					return '\
						<div class="kt-user-card-v2 kt-user-card-v2--uncircle">\
							<div class="kt-user-card-v2__details">\
								<div class="kt-user-card-v2__name">'+ row.name +'</div>\
								<span class="kt-user-card-v2__desc action-entire">\
									<a href="javascript:;" class="action-item nh-change-edit '+ isSystem[row.is_system].check + isDefault +'" data-id="'+ row.id +'" data-name="' + row.name + '" data-code="' + row.code + '">'+ nhMain.getLabel('sua') +'</a>\
									<a href="javascript:;" class="action-item text-success nh-delete '+ isSystem[row.is_system].check + isDefault +'" data-id="'+ row.id +'">'+ nhMain.getLabel('xoa') +'</a>\
								</span>\
							</div>\
						</div>';
				}
			},	
			{
				field: 'code',
				title: nhMain.getLabel('ma_nguon_don_hang'),
				width: 200			
			},
			{
				field: 'is_default',
				title: nhMain.getLabel('mac_dinh'),
				textAlign: 'center',
				width: 100,
				template: function(row) {
					var isDefault = {
						0: {'check': ''},
						1: {'check': 'checked="checked"'}						
					};
					return '\
						<div>\
							<label class="kt-radio kt-radio--tick kt-radio--success" style="top:-4px;">\
								<input type="radio" class="nh-is-default" data-id="'+ row.id +'" name="is_default" ' + isDefault[row.is_default].check + ' >\
								<span></span>\
							</label>\
						</div>';
				},
			}]

	};


	return {
		listData: function() {
			var formAddOrderSource = $('#add-order-source-form');
			nhAddOrderSource.init(formAddOrderSource);

			var datatable = $('.kt-datatable').KTDatatable(options);

			$(document).on('click', '.nh-is-default', function() {
				var _id = $(this).data('id');
				if(_id.length == 0){
			    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
			    	return false;
			    }
				swal.fire({
			        title: nhMain.getLabel('chon_nguon_ban_hang_mac_dinh'),
			        text: nhMain.getLabel('ban_co_chac_chan_muon_chon_nguon_hang_nay_lam_mac_dinh'),
			        type: 'question',
			        
			        confirmButtonText: nhMain.getLabel('dong_y'),
			        confirmButtonClass: 'btn btn-sm btn-danger',

			        showCancelButton: true,
			        cancelButtonText: nhMain.getLabel('huy_bo'),
			        cancelButtonClass: 'btn btn-sm btn-default'
			    }).then(function(result) {
			    	if(typeof(result.value) != _UNDEFINED && result.value){
			    		nhMain.callAjax({
							url: adminPath + '/source/is-default',
							data:{
								id: _id
							}
						}).done(function(response) {
							var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
						    var message = typeof(response.message) != _UNDEFINED ? response.message : '';

						    if (code == _SUCCESS) {
				            	$('.kt-datatable').KTDatatable('reload');
				            } else {
				            	toastr.error(message);
				            }            
						})
			    	}  
			    });
				return false;
			});  

			// event delete and change status on list
		    nhList.eventDefault(datatable, {
		    	url: {
			    	delete: adminPath + '/source/delete',
			    }
		    }); 
		}
	};
}();

jQuery(document).ready(function() {
	nhListOrderSource.listData();
});

