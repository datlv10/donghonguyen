"use strict";

var nhPermissionSetup = function () {

	var formEl;
	var validator;

	var initValidation = function() {

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

		$(document).on('change', '.check-for-controller', function(e) {
			var wrapTh = $(this).closest('th');
			var wrapTable = $(this).closest('table.permisson-controller');

			var index = wrapTh.index();
			var checked = $(this).is(':checked') ? true :  false;

			wrapTable.find('tr').each(function(i) {
				$(this).find('td').eq(index).find('input[type="checkbox"]').prop('checked', checked);
			});
		});

		$(document).on('change', '.check-all-column', function(e) {
			var wrapTd = $(this).closest('td');
			var wrapForm = $(this).closest('form#main-form');

			var index = wrapTd.index();
			var checked = $(this).is(':checked') ? true :  false;

			wrapForm.find('table.permisson-controller').each(function(table_i) {
				$(this).find('tr').each(function(tr_i) {
					$(this).find('td').eq(index).find('input[type="checkbox"]').prop('checked', checked);
				});
			});
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
		}
	};
}();


$(document).ready(function() {
	nhPermissionSetup.init();
});
