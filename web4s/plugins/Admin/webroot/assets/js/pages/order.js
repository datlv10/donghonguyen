"use strict";

var nhOrder = function () {

	var formEl;
	var validator;

	var initSubmit = function() {
		$.validator.addMethod('regexUser', function(code, element) {
			if (typeof(code) == _UNDEFINED || code == null || code == '') return true;
			return code.match(/^[a-zA-Z0-9]+$/);
		}, nhMain.getLabel('ma_don_hang_khong_duoc_chua_ky_tu_dac_biet'));

		var validator = formEl.validate({
			ignore: ':hidden',
			rules: {
				code: {
					regexUser: true,
					minlength: 4
				},
			},
			messages: {
				code: {
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan')
                },
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
			}
		})

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			// set value order to input hidden			
			var items = typeof(nhBillForm.data.items) != _UNDEFINED ? nhBillForm.data.items : [];
			$('input[name="items"]').val(JSON.stringify(items));			

			$('input[name="voucher_code"]').val(typeof(nhBillForm.data.voucher_code) != _UNDEFINED ? nhBillForm.data.voucher_code : '');
			$('input[name="voucher_value"]').val(typeof(nhBillForm.data.voucher_value) != _UNDEFINED ? nhBillForm.data.voucher_value : '');
			$('input[name="coupon_code"]').val(typeof(nhBillForm.data.coupon_code) != _UNDEFINED ? nhBillForm.data.coupon_code : '');
			$('input[name="discount_note"]').val(typeof(nhBillForm.data.discount_note) != _UNDEFINED ? nhBillForm.data.discount_note : '');
			$('input[name="discount_type"]').val(typeof(nhBillForm.data.discount_type) != _UNDEFINED ? nhBillForm.data.discount_type : '');
			$('input[name="discount_value"]').val(typeof(nhBillForm.data.discount_value) != _UNDEFINED ? nhBillForm.data.discount_value : '');

			if(typeof($(this).data('status')) != _UNDEFINED){
				$('input[name="status"]').val($(this).data('status'));	
			}
			
			var validate = true;
	  		if (!validator.form()){
  				validate = false;
  				return;
  			};

	  		if(validate){
	  			nhMain.initSubmitForm(formEl, $(this));
	  		}
		});	
	}

	return {
		init: function() {
			formEl = $('#main-form');
			
			nhBillCustomer.init(formEl);
			nhBillForm.init();
			nhBillStaff.init();
			nhPaymentConfirm.init();
			nhBillShipping.init(formEl);
			nhPromotion.init(formEl);
			initSubmit();

			nhMain.quickAdd.init({
				idModal: '#quick-add-product-modal',
				idButtonSubmit: '#quick-add-product-btn',
				idForm: '#quick-add-product-form'
			}, function(response){
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	if (code == _SUCCESS) {
	            	nhBillForm.loadInfoProductToList(data);
	            	nhBillForm.parseData();
					nhBillForm.updateLabelOrder();
	            } else {
	            	toastr.error(message);
	            }
			});

			nhMain.quickAdd.init({
				idModal: '#quick-add-customer-modal',
				idButtonSubmit: '#quick-add-customer-btn',
				idForm: '#quick-add-customer-form'
			}, function(response){
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	if (code == _SUCCESS) {
	        		nhBillCustomer.showInfo(data);
	            } else {
	            	toastr.error(message);
	            }
			}, function(formEl){
				$.validator.addMethod('checkExistPhone', function (phone, element) {
		                var check = true;
		                if(phone.length > 0){
							nhMain.callAjax({
								async: false,
								url: adminPath + '/customer/check-exist/phone',
								data: {
									phone: phone
								}
							}).done(function(response) {
								var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
					        	if (code == _SUCCESS && typeof(data.exist) != _UNDEFINED && data.exist) {
					        		check = false;			        		
					            }
							});
						}
		                return check;
		            }, nhMain.getLabel('so_dien_thoai_da_ton_tai')
		        );

		        $.validator.addMethod('checkExistEmail', function (email, element) {
		                var check = true;
		                if(email.length > 0){
							nhMain.callAjax({
								async: false,
								url: adminPath + '/customer/check-exist/email',
								data: {
									email: email
								}
							}).done(function(response) {
								var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
					        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
					        	if (code == _SUCCESS && typeof(data.exist) != _UNDEFINED && data.exist) {
					        		check = false;			        		
					            }
							});
						}
		                return check;
		            }, nhMain.getLabel('email_da_ton_tai')
		        );

				return formEl.validate({
					ignore: ':hidden',
					focusInvalid: true,
        			onkeyup: false,
					rules: {
						full_name: {
							required: true,
							maxlength: 255
						},
						phone: {
							checkExistPhone: true
						},
						email: {
							checkExistEmail: true,
							pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
						}
					},
					messages: {
						full_name: {
		                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
		                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
		                },
		                email: {
		                	pattern: nhMain.getLabel('email_chua_dung_dinh_dang')
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
						KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
					}
				});
			});

			nhMain.location.init({
				idWrap: ['#quick-add-customer-form', '#add-address-form']
			});

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$('.kt-selectpicker').selectpicker();

			$('.select-datetime').datetimepicker({
	            format: 'hh:ii - dd/mm/yyyy',
	            showMeridian: true,
	            todayHighlight: true,
	            autoclose: true,
	            pickerPosition: 'top-right'
	        });

		}
	};
}();


$(document).ready(function() {
	nhOrder.init();
});
