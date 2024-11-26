"use strict";

var nhSetting = function () {

	var formEl;
	var validator;

	var initValidation = function() {

		validator = formEl.validate({
			ignore: ":hidden",

			// Validation rules
			rules: {
				company_name: {
					maxlength: 255
				},
				phone: {
					minlength: 10
				},
				email: {
					maxlength: 255
				}
			},
			messages: {
				company_name: {
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                phone: {
                	minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan')
                },

                email: {
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

	var initRepeater = function() {
        $('.kt_repeater').repeater({
            initEmpty: false,
             
            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }   
        });
    }

    var selectFavicon = function(){
    	$(document).on('change', 'input#favicon-select', function(e) {
    		e.preventDefault();

            if (this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                	$('.favicon-preview').css('background-image', 'url(' + e.target.result + ')');
                	$('input[name="favicon_select"]').val(e.target.result);
                }
                reader.readAsDataURL(this.files[0]);                
            }
    	});
    }

	return {
		init: function() {
			formEl = $('#main-form');			
			
			nhMain.selectMedia.single.init();
			initRepeater();
			selectFavicon();
			
			initValidation();
			initSubmit();
		}
	};
}();

$(document).ready(function() {
	nhSetting.init();
});