"use strict";

var nhCustomerAddress = {
	address: {
		idModal: '#add-address-modal',
		idForm: '#add-address-form',
		idBtnSave: '#save-address-btn',
		classBtnEdit: '.btn-edit-customer-address',
		classBtnAdd: '.btn-add-customer-address'
	},
	formEl: null,
	validatorAddress: null,
	init: function(formEl){
		
		var self = this;
		self.formEl = formEl;

		nhMain.validation.phoneVn();
		var validatorAddress = $(self.address.idForm).validate({
			rules: {
				name: {
					required: true,
					minlength: 6,
					maxlength: 255
				},
				phone: {
					required: true,
					phoneVN: true
				}				
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                phone: {
                	required: nhMain.getLabel('vui_long_nhap_thong_tin')
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
				KTUtil.scrollTo(validatorAddress.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});

		$(document).on('click', self.address.idBtnSave, function(e) {
			e.preventDefault();
			var _form = $(self.address.idForm);
			if(validatorAddress.form()){
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

		$(document).on('click', self.address.classBtnEdit, function(e) {
			self.clearInputAddressModal();
			var addressInfo = $(this).data('address');		
			self.loadInfoAddressModal(addressInfo);
			$(self.address.idModal).modal('show');
		});

		$(document).on('click', self.address.classBtnAdd, function(e) {
			self.clearInputAddressModal();
			var address_id = $(this).data('address-id');
			$(self.address.idForm).attr('action', adminPath + '/customer/save-address/' + address_id);
			$(self.address.idModal).modal('show');
			
		});

		$(document).on('click', '.btn-is-default', function(e) {
			var _id = $(this).attr('data-id');
			var _customer_id = $(this).attr('data-customer-id');
			if(typeof(_id) == _UNDEFINED || _id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }

			nhMain.callAjax({
	            url: adminPath + '/customer/set-default',
	            type: 'POST',
	            data: {
	            	id: _id,
	            	customer_id: _customer_id
	            }
	        }).done(function(response) {
	            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	            var data = typeof(response.data) != _UNDEFINED ? response.data : '';
	            if (code != _SUCCESS) {	  
	            	toastr.error(message);          	
	            }      
	        })
		});	

		$(document).on('click', '.btn-delete-customer-address', function(e) {
			var _id = $(this).attr('data-id');
			var btn_delete = $(this);
			if(typeof(_id) == _UNDEFINED || _id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }

			swal.fire({
		        title: nhMain.getLabel('xoa_dia_chi'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_ban_ghi_nay'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
					nhMain.callAjax({
			            url: adminPath + '/customer/delete-address',
			            type: 'POST',
			            data: {
			            	id: _id,
			            }
			        }).done(function(response) {
			            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			            var data = typeof(response.data) != _UNDEFINED ? response.data : '';
			            if (code == _SUCCESS) {
		            		btn_delete.closest('tr').remove();
			            	toastr.info(message);
			            } else {
		                    toastr.error(message);
		                }     
			        })
		    	}
		    });
			return false;
		});
	},

	clearInputAddressModal: function(){
		var self = this;

		var _modal = $(self.address.idModal);		
		_modal.find('input').val('');

		var citySelect = _modal.find('select#city_id');
		var districtSelect = _modal.find('select#district_id');
		var wardSelect = _modal.find('select#ward_id');

		
		citySelect.selectpicker('destroy');
		citySelect.val('');
		citySelect.selectpicker('render');
		

		districtSelect.find('option:not([value=""])').remove();
		districtSelect.selectpicker('refresh');

		wardSelect.find('option:not([value=""])').remove();
		wardSelect.selectpicker('refresh');
	},
	loadInfoAddressModal: function(addressInfo = {}){
		var self = this;

		if(typeof(addressInfo.customer_id) == _UNDEFINED || addressInfo.customer_id.length == 0){
	    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
	    	return false;
	    }
	    $(self.address.idForm).attr('action', adminPath + '/customer/save-address/' + addressInfo.customer_id);

		var _modal = $(self.address.idModal);
		_modal.find('input[name="address_id"]').val(typeof(addressInfo.id) != _UNDEFINED ? addressInfo.id : '');
		_modal.find('input[name="name"]').val(typeof(addressInfo.address_name) != _UNDEFINED ? addressInfo.address_name : '');
		_modal.find('input[name="phone"]').val(typeof(addressInfo.phone) != _UNDEFINED ? addressInfo.phone : '');
		_modal.find('input[name="zip_code"]').val(typeof(addressInfo.zip_code) != _UNDEFINED ? addressInfo.zip_code : '');
		_modal.find('input[name="address"]').val(typeof(addressInfo.address) != _UNDEFINED ? addressInfo.address : '');

		_modal.find('select#city_id').val(typeof(addressInfo.city_id) != _UNDEFINED ? addressInfo.city_id : '');
		_modal.find('select#city_id').selectpicker('render');

		var city_id = typeof(addressInfo.city_id) != _UNDEFINED ? nhMain.utilities.parseInt(addressInfo.city_id) : null;
		var district_id = typeof(addressInfo.district_id) != _UNDEFINED ? nhMain.utilities.parseInt(addressInfo.district_id) : null;
		
		if(city_id > 0){
			var _data = {};
			_data[_PAGINATION] = {};
			_data[_PAGINATION][_PERPAGE] = 200;

			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/district/json/' + city_id,
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
				            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
				        });
				        _modal.find('select#district_id').append(listOption);
				        _modal.find('select#district_id').selectpicker('destroy');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});
		}

		if(district_id > 0){
			var _data = {};
			_data[_PAGINATION] = {};
			_data[_PAGINATION][_PERPAGE] = 200;

			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/ward/json/' + district_id,
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
				            listOption += '<option value="' + item.id + '">' + item.name + '</option>';
				        });
				        _modal.find('select#ward_id').append(listOption);
				        _modal.find('select#ward_id').selectpicker('destroy');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});
		}

		_modal.find('select#district_id').val(typeof(addressInfo.district_id) != _UNDEFINED ? addressInfo.district_id : '');
		_modal.find('select#district_id').selectpicker('render');

		_modal.find('select#ward_id').val(typeof(addressInfo.ward_id) != _UNDEFINED ? addressInfo.ward_id : '');
		_modal.find('select#ward_id').selectpicker('render');
	}
}

var nhCustomer = function () {

	var formEl;
	var formAddress;
	var formNote;
	var validator;
	var validatorAddress;

	var initValidation = function() {
		nhMain.validation.phoneVn();
		validator = formEl.validate({
			rules: {
				full_name: {
					required: true,
					minlength: 6,
					maxlength: 255
				},
				email: {
					pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    minlength: 10,
                    maxlength: 255
				}			
			},
			messages: {
				full_name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                email: {
                	pattern: nhMain.getLabel('email_chua_dung_dinh_dang'),
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

			// display error
			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});
	}

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			if (validator.form()) {
				nhMain.initSubmitForm(formEl, $(this));
			}
		});	

		$(document).on('click', '.btn-note-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm(formNote, $(this));
		});

		$(document).on('click', '.btn-delete-customer-note', function(e) {
			var _id = $(this).attr('data-id');
			var _index = $(this).attr('data-index');
			var btn_delete = $(this);
			if(typeof(_id) == _UNDEFINED || _id.length == 0){
		    	toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
		    	return false;
		    }

			swal.fire({
		        title: nhMain.getLabel('xoa_ghi_chu'),
		        text: nhMain.getLabel('ban_co_chac_chan_muon_xoa_ban_ghi_nay'),
		        type: 'warning',
		        
		        confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
		        confirmButtonClass: 'btn btn-sm btn-danger',

		        showCancelButton: true,
		        cancelButtonText: nhMain.getLabel('huy_bo'),
		        cancelButtonClass: 'btn btn-sm btn-default'
		    }).then(function(result) {
		    	if(typeof(result.value) != _UNDEFINED && result.value){
					nhMain.callAjax({
			            url: adminPath + '/customer/delete-note',
			            type: 'POST',
			            data: {
			            	id: _id,
			            	index: _index,
			            }
			        }).done(function(response) {
			            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
			            var message = typeof(response.message) != _UNDEFINED ? response.message : '';
			            var data = typeof(response.data) != _UNDEFINED ? response.data : '';
			            if (code == _SUCCESS) {
		            		btn_delete.closest('tr').remove();
			            	toastr.info(message);
			            } else {
		                    toastr.error(message);
		                }     
			        })
		    	}
		    });
			return false;
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');
			formAddress = $('#add-address-modal');
			formNote = $('#add-note-form');
			nhCustomerAddress.init(formAddress);
			initValidation();
			initSubmit();

			nhMain.location.init({
				idWrap: ['#add-address-form']
			});

			$('input[name="birthday"]').inputmask('99/99/9999', {
		        'placeholder': 'dd/mm/yyyy',
		    });

			nhMain.input.inputMask.init($('input[name="email"]'), 'email');	
			$('.kt-selectpicker').selectpicker();
		}
	};
}();



$(document).ready(function() {
	nhCustomer.init();
});
