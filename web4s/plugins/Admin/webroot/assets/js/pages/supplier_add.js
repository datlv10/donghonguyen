"use strict";

var nhSupplier = function () {

	var formEl;
	var validator;

	var initValidation = function() {

  		nhMain.validation.phoneVn();
		validator = formEl.validate({
			rules: {
				name: {
					required: true,
					maxlength: 255
				},
				address: {
					required: true,
					maxlength: 255
				},
				phone: {
					phoneVN: true
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                address: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                phone: {
                    phoneVN: nhMain.getLabel('so_dien_thoai_chua_dung_dinh_dang'),
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

			nhMain.location.init({
				idWrap: ['#main-form']
			});
			initSubmit();

			$('.kt-selectpicker').selectpicker();
		}
	};
}();

$(document).ready(function() {
	nhSupplier.init();
});
