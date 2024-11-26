"use strict";

var nhCustomer = function () {

	var formChangePassword;
	var formAccount;
	var formAccountStatus;

	var initSubmit = function() {
		$(document).on('click', '.btn-password-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm(formChangePassword, $(this));
		});

		$(document).on('click', '.btn-account-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm(formAccount, $(this));
		});

		$(document).on('click', '.btn-account-status', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm(formAccountStatus, $(this));
		});
	}

	return {
		init: function() {
			formChangePassword = $('#change-pass');
			formAccount = $('#account-form');
			formAccountStatus = $('#account-status-form');
			initSubmit();
			$('.kt-selectpicker').selectpicker();
		}
	};
}();

$(document).ready(function() {
	nhCustomer.init();
});
