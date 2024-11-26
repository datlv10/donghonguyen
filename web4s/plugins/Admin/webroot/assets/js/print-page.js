"use strict";

var nhPrintPage = {
	wrapElement: $('#nh-print-page'),
	init: function(params = {}){
		var self = this;
		if(self.wrapElement.length == 0) return;

		self.loadContentPage();
	},
	loadContentPage: function(callback = null){
		var self = this;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

		var view = typeof(self.wrapElement.attr('nh-view')) != _UNDEFINED && self.wrapElement.attr('nh-view').length > 0 ? self.wrapElement.attr('nh-view') : null;
		var id_record = typeof(self.wrapElement.attr('nh-id-record')) != _UNDEFINED && self.wrapElement.attr('nh-id-record').length > 0 ? self.wrapElement.attr('nh-id-record') : null;
		nhMain.callAjax({
    		async: true,
    		dataType: 'html',
			url: adminPath + '/print/get-content',
			data: {
				view: view,
				id_record: id_record
			}
		}).done(function(response) {
			self.wrapElement.html(response);
			self.wrapElement.printThis({
				'loadCSS': templatePath + '/assets/css/print-page.css'
			});
		});
	}
}

$(document).ready(function() {
	nhPrintPage.init();
});
