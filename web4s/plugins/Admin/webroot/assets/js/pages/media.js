"use strict";

var nhMedia = {
	init: function(){
		var self = this;

		self.setHeightIframe();
		self.events();
	},
	setHeightIframe: function(){
		var self = this;

		var iframeElement = $('iframe#media-iframe');
		if(iframeElement.length == 0) return;

		iframeElement.css('height', window.innerHeight - 190); // 190px là chiều cao của header
	},
	events: function(){
		var self = this;

		$( window ).on('resize', function() {
		  	self.setHeightIframe();
		});
	}
}


$(document).ready(function() {
	nhMedia.init();
});
