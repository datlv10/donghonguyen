"use strict";

var nhOrderDetail = function () {

	var formEl;
	var validator;

	var initSubmit = function() {
		$(document).on('click', '.btn-cancel-order', function(e) {
			$('#cancel-order-modal').modal('show');
		});

		$(document).on('click', '#btn-confirm-cancel-order', function(e) {
			var order_id = $(this).data('order-id');
			if(order_id > 0){
				KTApp.blockPage(blockOptions);
				nhMain.callAjax({
					url: adminPath + '/order/cancel/' + order_id
				}).done(function(response) {
					KTApp.unblockPage();

				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	toastr.clear();

		            if (code == _SUCCESS) {
		            	toastr.info(message);

		            	location.reload();
		            } else {
		            	toastr.error(message);
		            }
		            
		            $('#cancel-order-modal').modal('hide');
				});
			}			
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			var status = typeof($(this).data('status')) != _UNDEFINED ? $(this).data('status') : '';
			$('input[name="status"]').val(status);

			nhMain.initSubmitForm(formEl, $(this));
		});	
	}

	var validatorContact;
	var formElContact;
	var updateContact = function(){

		validatorContact = formElContact.validate({
			rules: {
				full_name: {
					required: true,
					minlength: 6,
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
				KTUtil.scrollTo(validatorContact.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});


		$(document).on('click', '#save-contact-order-btn', function(e) {
			e.preventDefault();
			var _modal = $(this).closest('.modal');

			if(validatorContact.form()){
				KTApp.blockPage(blockOptions);

				var formData = formElContact.serialize();
				nhMain.callAjax({
					url: formElContact.attr('action'),
					data: formData,
				}).done(function(response) {
					KTApp.unblockPage();

				   	var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
		        	var message = typeof(response.message) != _UNDEFINED ? response.message : '';
		        	var data = typeof(response.data) != _UNDEFINED ? response.data : {};
		        	toastr.clear();

		            if (code == _SUCCESS) {
		            	toastr.info(message);

		            	location.reload();            
		            } else {
		            	toastr.error(message);
		            }

		            _modal.modal('hide');
				});
			}

			return false;
		});
	}
	
	return {
		init: function() {
			formEl = $('#main-form');
			nhBillCustomer.init(formEl);
			nhPaymentConfirm.init();
			nhBillShipping.init(formEl);
			initSubmit();

			formElContact = $('#update-contact-form');
			updateContact();

			nhMain.location.init({
				idWrap: ['#add-address-form']
			});

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$('.kt-selectpicker').selectpicker();

			$('.select-datetime').datetimepicker({
	            format: 'hh:ii - dd/mm/yyyy',
	            showMeridian: true,
	            todayHighlight: true,
	            autoclose: true,
	            pickerPosition: 'bottom-right'
	        });
		}
	};
}();


$(document).ready(function() {
	nhOrderDetail.init();
});
