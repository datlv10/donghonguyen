"use strict";

var changeStatus = {
	idWarp: '#wrap-payment-confirm',
	idForm: '#payment-confirm-form',
	idModal: '#payment-confirm-modal',
	idBtnConfirm: '#btn-payment-confirm',
	init: function(){
		var self = this;

		var formEl = $(self.idForm);
	    
		$(document).on('click', self.idModal + ' ' + self.idBtnConfirm, function(e) {
			KTApp.blockPage(blockOptions);

			var formData = formEl.serialize();
			nhMain.callAjax({
				url: formEl.attr('action'),
				data: formData
			}).done(function(response) {
				KTApp.unblockPage();
				$(self.idModal).modal('hide');

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
			});
		});


		$(document).on('click', '.btn-confirm-payment', function(e) {
			$(self.idModal).modal('show');
		});

		$(self.idModal).on('hidden.bs.modal', function () {
		  	self.clearModal();
		});
	},

	clearModal: function(){
		var self = this;
		$(self.idModal).find('input:not([type=hidden])').val('');
	}
}

var nhPaymentDetail = function () {

	var initSubmit = function() {
		var self = this;
		$(document).on('click', '.btn-cancel-payment', function(e) {
			$('#cancel-payment-modal').modal('show');
		});

		$(document).on('click', '#btn-confirm-cancel-payment', function(e) {
			var payment_id = $(this).data('payment-id');
			if(payment_id > 0){
				KTApp.blockPage(blockOptions);
				nhMain.callAjax({
					url: adminPath + '/payment/change-status/' + payment_id,
					data: {
						status: 0
					}
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
		            
		            $('#cancel-payment-modal').modal('hide');
				});
			}			
		});
	}

	return {
		init: function() {
			initSubmit();
			changeStatus.init();
		}
	};
}();


$(document).ready(function() {
	nhPaymentDetail.init();
});
