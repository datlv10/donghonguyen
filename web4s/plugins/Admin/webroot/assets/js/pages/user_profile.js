"use strict";

var nhUser = function () {
	var profile = {
		wrapProfile: '#wrap-profile',
		init: function() {
			var self = this;

			var formElement = $('#profile-form');
			if(formElement == null || formElement == _UNDEFINED || formElement.length == 0){
				return false;
			}

			nhMain.validation.phoneVn();
			var validator = formElement.validate({
				ignore: ':hidden',
				rules: {
					full_name: {
						required: true,
						minlength: 6,
						maxlength: 255
					},
					phone: {
						phoneVN: true
					}
				},
				messages: {
	                phone: {
	                    phoneVN: nhMain.getLabel('so_dien_thoai_chua_dung_dinh_dang'),
	                },

	                full_name: {
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
					KTUtil.scrollTop();
				}
			})

			formElement.on('click', '.btn-save', function(e){
		  		e.preventDefault();
		  		if (!validator.form()) return false;

				nhMain.initSubmitForm(formElement, $(this));				
			});
		}
	}

	var changePassword = {
		wrapProfile: '#wrap-profile',
		init: function(){
			var self = this;

			var formElement = $('#change-password-form');
			if(formElement == null || formElement == _UNDEFINED || formElement.length == 0){
				return false;
			}

		  	var validator = formElement.validate({
				ignore: ':hidden',
				rules: {
					old_password: {
						minlength: 6,
						maxlength: 255,
						required: true,
					},
					new_password: {
						minlength: 6,
						maxlength: 255,
						required: true,
					},
					re_password: {
	                    equalTo: '#new_password'
	                }
				},
				messages: {
	                old_password: {
	                    required: nhMain.getLabel('vui_long_nhap_mat_khau'),
	                    minlength: nhMain.getLabel('mat_khau_nhap_qua_ngan'),
	                    maxlength: nhMain.getLabel('mat_khau_nhap_qua_dai')
	                },

	                new_password: {
	                    required: nhMain.getLabel('vui_long_nhap_mat_khau_moi'),
	                    minlength: nhMain.getLabel('mat_khau_nhap_qua_ngan'),
	                    maxlength: nhMain.getLabel('mat_khau_nhap_qua_dai')
	                },

	                re_password: {
	                    equalTo: nhMain.getLabel('xac_nhan_mat_khau_chua_chinh_xac')
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
				}
			});

		  	formElement.on('click', '.btn-save', function(e){
		  		e.preventDefault();
		  		if (!validator.form()) return false;

				nhMain.initSubmitForm(formElement, $(this));				
			});
		}
	}

	var languageAdmin = {
		init: function() {
			var self = this; 

			var formElement = $('#language-admin-form');
			if(formElement == null || formElement == _UNDEFINED || formElement.length == 0){
				return false;
			}

			$(document).on('click', '.btn-save', function(e){
		  		e.preventDefault();
				nhMain.initSubmitForm(formElement, $(this));				
			});
		}
	}


	return {
		init: function() {		
			profile.init();
			changePassword.init();
			languageAdmin.init();

			nhMain.selectMedia.single.init();
			nhMain.validation.phoneInput();
			nhMain.input.inputMask.init($('input[name="email"]'), 'email');

			$('.kt-selectpicker').selectpicker();
		}
	};
}();

$(document).ready(function() {
	nhUser.init();

	$('input[name="birthday"]').inputmask('99/99/9999', {
        'placeholder': 'dd/mm/yyyy',
    });
});
