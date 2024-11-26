"use strict";

var nhSmsBrandname = {
	init: function(){
		var self = this;

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});

		$('.kt-selectpicker').selectpicker();
	}
}

$(document).ready(function() {
	nhSmsBrandname.init();
});
