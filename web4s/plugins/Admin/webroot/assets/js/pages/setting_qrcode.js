"use strict";

var nhSettingQrCode = {
	formElement: $('#main-form'),

	init: function(){
		var self = this;
		if(self.formElement.length == 0) return;

		self.initLibrary();
		self.events();

		self.previewQrCode();
	},
	initLibrary: function(){
		var self = this;

		$('.kt-selectpicker').selectpicker({
			dropupAuto: false
		});

		$('.js-minicolors').minicolors({
	        control: $(this).attr('data-control') || 'hue',
	        defaultValue: $(this).attr('data-default') || '',
	        format: $(this).attr('data-format') || 'hex',
	        keywords: $(this).attr('data-keywords') || '',
	        inline: $(this).attr("data-inline") === 'true',
	        letterCase: $(this).attr('data-letter-case') || 'lowercase',
	        opacity: $(this).attr('data-opacity'),
	        position: $(this).attr('data-position') || 'bottom',
	        swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
	        change: function (value, opacity) {
	            if (!value) return;
	            if (opacity) value += ', ' + opacity;
	        },
	        theme: 'bootstrap',
	    });

	    nhMain.selectMedia.single.init();
	},
	events: function(){
		var self = this;

		$(document).on('click', '.btn-preview', function(e) {
			e.preventDefault();

			self.previewQrCode();
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			nhMain.initSubmitForm(self.formElement, $(this));
		});
	},
	previewQrCode: function(){
		var self = this;

		var formData = self.formElement.serialize();

		nhMain.callAjax({
    		async: false,
			url: adminPath + '/setting/preview-setting-qr-bank',
			data: formData,
		}).done(function(response) {
			var code = response.code || _ERROR;
        	var message = response.message || '';
        	var data = response.data || {};
        	var url = data.url || '';
        	
        	if (code == _SUCCESS && url.length > 0) {
            	$('img#image-preview').attr('src', url);
            } else {
            	toastr.error(message);
            }
		});
	}
};

$(document).ready(function() {
    nhSettingQrCode.init();
});