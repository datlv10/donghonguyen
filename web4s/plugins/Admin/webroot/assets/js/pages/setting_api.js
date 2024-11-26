"use strict"

var generateCode = {
	init: function() {
		var self = this;

		$('.nh-random').on('click', function(e) {
			var secretKey = $('#secret_key');
			secretKey.val(self.getRandomString(50));
		});
	},

	getRandomString: function(length){
		var self = this;

		var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	    var result = '';
	    for ( var i = 0; i < length; i++ ) {
	        result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
	    }
	    return result;
	}
}

var nhApi = function() {
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
			generateCode.init();
			initSubmit();
		}
	};
}();

$(document).ready(function() {
	nhApi.init();
});