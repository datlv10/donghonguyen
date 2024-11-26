"use strict";

var nhCustomer = function () {

	var formEl;
	var validator;

	var initValidation = function() {

		nhMain.validation.phoneVn();
		validator = formEl.validate({
			ignore: ':hidden',
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
				},
				phone: {
					required: true,
					phoneVN: true
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
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});

		$(document).on('change', '#create-account', function(e) {
			$('#wrap-create-account').collapse($(this).is(':checked') ? 'show' : 'hide');
			if($(this).is(':checked')) {
				$('#username').rules('add', {
					required: true,
					minlength: 10,
                    maxlength: 255,
                    messages: {
                    	required: nhMain.getLabel('vui_long_nhap_tai_khoan'),
	                    minlength: nhMain.getLabel('ten_dang_nhap_nhap_qua_ngan'),
	                    maxlength: nhMain.getLabel('ten_dang_nhap_nhap_qua_dai')
                    }
				});
				$('#password').rules('add', {
					required: true,
                    messages: {
                    	required: nhMain.getLabel('vui_long_nhap_mat_khau')
                    }
				});
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
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initValidation();
			initSubmit();	

			nhMain.location.init({
				idWrap: ['#main-form']
			});

			nhMain.input.inputMask.init($('input[name="email"]'), 'email');

			$('input[name="birthday"]').inputmask('99/99/9999', {
		        'placeholder': 'dd/mm/yyyy',
		    });

		    $('.kt-selectpicker').selectpicker();

		    
		}
	};
}();


$(document).ready(function() {
	nhCustomer.init();
});
