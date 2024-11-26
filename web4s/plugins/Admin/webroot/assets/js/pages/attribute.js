"use strict";

var nhAttribute = function () {

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
				},
				code: {
					required: true,
					maxlength: 20
				},
				attribute_type: {
					required: true,
				},
				input_type: {
					required: true
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                code: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                attribute_type: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
                },
                input_type: {
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
			},
		});
	}

	var initSubmit = function() {

		$(document).on('change', 'select[name="attribute_type"]', function(e) {
			var attribute_type = $(this).val();
			var inputTypeSelect = $('select[name="input_type"]');
			inputTypeSelect.find('option:not([value=""])').remove();
			inputTypeSelect.selectpicker('refresh');

			nhMain.callAjax({
	    		async: false,
				url: adminPath + '/setting/attribute/get-list-input',
				data: {
					attribute_type: attribute_type
				}
			}).done(function(response) {
				var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
	        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
	        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
	        	if (code == _SUCCESS) {
                    if (!$.isEmptyObject(data)) {
                    	var listOption = '';
				        $.each(data, function (key, name) {
				            listOption += '<option value="' + key + '">' + name + '</option>';
				        });
				        inputTypeSelect.append(listOption);
				        inputTypeSelect.selectpicker('refresh');
                    }		                    
	            } else {
	            	toastr.error(message);
	            }
			});
		});
		
		$(document).on('change', 'select[name="input_type"]', function(e) {
			$('#wrap-has-image').toggleClass('d-none', $(this).val() != _SPECICAL_SELECT_ITEM);
		});

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

			$('.kt-selectpicker').selectpicker();
		}
	};
}();


$(document).ready(function() {
	nhAttribute.init();
});
