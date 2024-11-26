"use strict";

$(document).on('click', '.btn-save', function(e) {
	e.preventDefault();

	var formEl = $(this).closest('form');
	nhMain.initSubmitForm(formEl, $(this));
});