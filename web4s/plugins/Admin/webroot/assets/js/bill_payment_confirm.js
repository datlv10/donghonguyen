"use strict";

var nhPaymentConfirm = {
	idWarp: '#wrap-payment-confirm',
	idCheckboxConfirm: '#cb-payment-confirm',

	idForm: '#payment-confirm-form',
	idModal: '#payment-confirm-modal',
	idBtnConfirm: '#btn-payment-confirm',
	debt: 0,
	init: function(callback){
		var self = this;

		var formEl = $(self.idForm);
		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

	    self.debt = typeof($('#debt').val()) != _UNDEFINED ? $('#debt').val() : 0;
	    
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
			$(self.idWarp).removeClass('collapse');
			
			$(self.idModal).modal('show');
		});

		$(self.idModal).on('hidden.bs.modal', function () {
		  	self.clearModal();
		});

		$(self.idModal).on('shown.bs.modal', function () {
		  	var currentdate = new Date();
		  	var hours = currentdate.getHours() >= 10 ? currentdate.getHours() : '0' + currentdate.getHours();
		  	var minutes = currentdate.getMinutes() >= 10 ? currentdate.getMinutes() : '0' + currentdate.getMinutes();
		  	var date = currentdate.getDate() >= 10 ? currentdate.getDate() : '0' + currentdate.getDate();
		  	var month = (currentdate.getMonth() + 1) >= 10 ? (currentdate.getMonth() + 1) : '0' + (currentdate.getMonth() + 1);
		  	var year = currentdate.getFullYear();		  

            var datetime = hours + ':'
            	+ minutes + ' - '
            	+ date + '/'
                + month  + '/'
                + year;
            $(this).find('input[name="payment_time"]').val(datetime);
            $(this).find('input[name="amount"]').val(self.debt > 0 ? self.debt : '');
		});

		$(document).on('keyup keydown change', self.idModal + ' input[name="amount"]', function(e) {
			var paid = nhMain.utilities.parseTextMoneyToNumber($(this).val());

			if(paid > self.debt){
				$(this).val(self.debt);
			}
		});

		$(document).on('change', self.idCheckboxConfirm, function(e) {
			$(self.idWarp).collapse($(this).is(':checked') ? 'show' : 'hide');
		});

		$(self.idWarp).on('shown.bs.collapse', function () {
			var currentdate = new Date();
		  	var hours = currentdate.getHours() >= 10 ? currentdate.getHours() : '0' + currentdate.getHours();
		  	var minutes = currentdate.getMinutes() >= 10 ? currentdate.getMinutes() : '0' + currentdate.getMinutes();
		  	var date = currentdate.getDate() >= 10 ? currentdate.getDate() : '0' + currentdate.getDate();
		  	var month = (currentdate.getMonth() + 1) >= 10 ? (currentdate.getMonth() + 1) : '0' + (currentdate.getMonth() + 1);
		  	var year = currentdate.getFullYear();		  

            var datetime = hours + ':'
            	+ minutes + ' - '
            	+ date + '/'
                + month  + '/'
                + year;            

            $(self.idWarp).find('input[name="payment_time"]').val(datetime);
            
			var amount = '';
			if(typeof(nhBillForm) != _UNDEFINED && typeof(nhBillForm.data.total) != _UNDEFINED){
				amount = nhBillForm.data.total;
			}
			$(self.idWarp).find('input[name="amount"]').val(amount);

		});

		$(self.idWarp).on('hidden.bs.collapse', function () {
		  	$(self.idWarp).find('input').val('');			
		});

		$(document).on('keyup keydown change', self.idWarp + ' input[name="amount"]', function(e) {
			var paid = nhMain.utilities.parseTextMoneyToNumber($(this).val());
			if(typeof(nhBillForm) != _UNDEFINED && typeof(nhBillForm.data.total) != _UNDEFINED && paid > nhBillForm.data.total){
				$(this).val(nhBillForm.data.total);
			}
		});

		$(document).on('click', '.icon-arrow-payment', function(e) {
			if($(this).attr('aria-expanded') == 'false'){
				$(this).removeClass('fa-caret-down').addClass('fa-caret-right');
			}else{
				$(this).removeClass('fa-caret-right').addClass('fa-caret-down');
			}
		});
	},
	clearModal: function(){
		var self = this;
		$(self.idModal).find('input:not([type=hidden])').val('');
	}
}