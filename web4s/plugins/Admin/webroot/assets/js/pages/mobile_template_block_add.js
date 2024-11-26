"use strict";

var nhMobileBlock = function () {

	var formEl;
	var validator;

	var initValidation = function() {

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 255
				},
				type: {
					required: true,
				},				
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                type: {
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
	}

	var initSubmit = function() {
		var btn = $('.btn-save');
		btn.on('click', function(e) {
			e.preventDefault();

			var btn_save = $(this);
			if (validator.form()) {
				KTApp.progress(btn_save);
				KTApp.blockPage(blockOptions);

				nhMain.callAjax({
					url: formEl.attr('action'),
					data: formEl.serialize()
				}).done(function(response) {
					KTApp.unprogress(btn_save);
					KTApp.unblockPage();
					
				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	var codeBlock = typeof(data.code) != _UNDEFINED ? data.code : null;

		        	toastr.clear();
		            if (code == _SUCCESS) {		            	
		            	toastr.info(message);
		            	if(codeBlock.length > 0){
		            		window.location.href = adminPath + '/mobile-app/block/update/' + codeBlock;
		            	}else{
		            		location.reload();
		            	}
		            } else {
		            	toastr.error(message);
		            }
				});
			}
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');			
			initValidation();
			initSubmit();

			$('.kt-selectpicker').selectpicker();		
		}
	};
}();


$(document).ready(function() {
	nhMobileBlock.init();
});
