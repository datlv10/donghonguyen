"use strict";

var nhNotification = function () {

	var formEl;
	var validator;

	var initValidation = function() {

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				title: {
					required: true,
					maxlength: 255
				},
				body: {
					required: true,
					maxlength: 255,
				}
			},
			messages: {
				title: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                body: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                }
            },

            errorPlacement: function(error, element) {
            	var messageRequired = element.attr('message-required');
            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
            		error.text(messageRequired);
            	}
            	error.addClass('invalid-feedback')

                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error);
                }else if(element.hasClass('select2-hidden-accessible')){
            		element.closest('.form-group').append(error);
                }else{
                	element.after(error);
                }
            },

			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			},
		});
	}

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			if (validator.form()) {
				nhMain.initSubmitForm(formEl, $(this));
			}
		});

		$(document).on('click', '.btn-save-draft', function() {
			formEl.find('input[name="draft"]').val(1);
			$('#btn-save').trigger('click');
		});	
	}

	return {
		init: function() {
			formEl = $('#main-form');				
			initValidation();
			initSubmit();
			
			nhMain.selectMedia.single.init();
			
			$('.kt-selectpicker').selectpicker();
		}
	};
}();


$(document).ready(function() {
	nhNotification.init();
});
