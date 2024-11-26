"use strict";

var nhSendMessege = {
	init: function(){
		var self = this;

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});
	}
}

$(document).ready(function() {
	nhSendMessege.init();
});
