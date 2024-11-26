"use strict";

var nhSettingGenerateQ = {	
	init: function(){
		var self = this;

		self.initLibrary();
		self.events();
	},
	initLibrary: function(){
		var self = this;

		$('.kt-selectpicker').selectpicker({
			dropupAuto: false
		});		
	},
	events: function(){
		var self = this;

		$(document).on('click', '[nh-btn="generate-qrcode"]', function(e) {
			e.preventDefault();

			self.generateQrcode();
		});

		$(document).on('click', '[nh-btn="download-qr"]', function(e){
			var url = $(this).attr('data-src') || '';
			if(url.length == '') return;

			var a = document.createElement('a');

			a.href = url;
			a.download = `${$.now()}.png`;
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		});
	},
	generateQrcode: function(){
		var self = this;

		var tabActive = $('[nh-tab="generate-qr"] .tab-pane.active');
		if(tabActive.length == 0) return;

		var formElement = tabActive.find('form');
		if(formElement.length == 0) return;

		var formData = formElement.serialize();

		nhMain.callAjax({
    		async: false,
			url: adminPath + '/setting/ajax-generate-qr',
			data: formData,
		}).done(function(response) {
			var code = response.code || _ERROR;
        	var message = response.message || '';
        	var data = response.data || {};
        	var url = data.url || '';
        	
        	if (code == _SUCCESS && url.length > 0) {
            	$('img#image-qr').attr('src', url);
            	self.clearValueInput(formElement);
            	$('[nh-btn="download-qr"]').attr('data-src', url).removeClass('d-none');
            } else {
            	toastr.error(message);
            }
		});
	},
	clearValueInput: function(formElement = null){
		var self = this;

		if(formElement == null || formElement.length == 0) return;

		formElement.find('input:not(:hidden)').val('');
		formElement.find('textarea').val('');
		formElement.selectpicker('refresh');
	}
};

$(document).ready(function() {
    nhSettingGenerateQ.init();
});