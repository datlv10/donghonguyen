"use strict"

var nhNotification = function() {
	var formEl;

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			nhMain.initSubmitForm(formEl, $(this));
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');
			initSubmit();

			nhMain.selectMedia.single.init();
		}
	};
}();

$(document).ready(function() {
	nhNotification.init();
});