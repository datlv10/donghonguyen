"use strict";

var nhLogin = function () {

	var formEl;
	var validator;

	var initValidation = function() {
		validator = formEl.validate({
			ignore: ':hidden',
			rules: {
				username: {
					required: true
				},
				password: {
					required: true
				},
				token: {
					required: true
				}
			},
			messages: {
				username: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin')
	            },
	            password: {
	                required: nhMain.getLabel('vui_long_nhap_thong_tin')
	            },
	            token: {
	                required: nhMain.getLabel('vui_long_xac_nhan_thong_tin')
	            }
	        },
            errorPlacement: function(error, element) {                
                if (element.closest('.input-group').length > 0) {
                    element.closest('.input-group').append(error.addClass('invalid-feedback'));
                }else if(element.closest('.kt-checkbox').length > 0){
                	element.closest('.kt-checkbox').append(error.addClass('invalid-feedback'));
                }else{
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			},

		});
	}

	var initSubmit = function() {

		$(document).on('click', '#btn-login:not([disabled])', function(e) {
			e.preventDefault();

			var btnSave = $(this);

			if (validator.form()) {
				toastr.clear();

				KTApp.blockPage(blockOptions);

				var formData = formEl.serialize();			
				nhMain.callAjax({
					url: formEl.attr('action'),
					data: formData
				}).done(function(response) {

					KTApp.unblockPage();
				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	var urlRedirect = typeof(data.url_redirect) != _UNDEFINED ? data.url_redirect : null;		        	
		            if (code == _SUCCESS) {
		            	toastr.success(message);		            	           	
		            	if(urlRedirect.length > 0){
		            		window.location.href = urlRedirect;
		            	}else{
		            		location.reload();
		            	}
		            } else {
		            	toastr.error(message);
		            }
				});
			}
		});

		formEl.on('keydown', 'input', function(e){
	  		if(e.keyCode == 13){
	  			formEl.find('#btn-login').trigger('click');
	  			return false;
	  		}			  		
		});
	}

	return {
		init: function() {
			formEl = $('#form-login');	
			formEl.find('#btn-login').prop('disabled', false);

			initValidation();
			initSubmit();
		}
	};
}();

$(document).ready(function() {
	nhLogin.init();
});