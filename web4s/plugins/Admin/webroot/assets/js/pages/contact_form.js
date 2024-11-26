"use strict";

var nhContactForm = function () {

	var formEl;
	var validator;

	var initValidation = function() {

  		nhMain.validation.url.init();

		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 255
				}
			},
			messages: {
				name: {
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
	}

	return {
		init: function() {
			formEl = $('#main-form');			
			initValidation();
			initSubmit();

			$('#wrap-list-field').repeater({
	            initEmpty: false,	            
	            show: function () {
	                $(this).slideDown();
	            },
	            hide: function (deleteElement) {
	                $(this).slideUp(deleteElement);
	            }   
	        });
		
			$('.kt-selectpicker').selectpicker();
		}
	};
}();


$(document).ready(function() {
	nhContactForm.init();
});
