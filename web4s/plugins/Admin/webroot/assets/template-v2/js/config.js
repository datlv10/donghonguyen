'use strict';

var nhConfig = {
	pathUrl: 'http://layout.local',
	domainWebsite: null,
	websiteInfo:{
		fullUrl: null,
		hostName: null,
		protocol: null,
		fullPath: null,
		cdnUrl: null,
	},
	init: function(options){
		var self = this;

		self.websiteInfo.fullUrl = window.location.href;
		self.websiteInfo.hostName = window.location.hostname;
		self.websiteInfo.protocol = window.location.protocol;
		self.websiteInfo.pathname = window.location.pathname;
		// self.websiteInfo.fullPath = self.fullUrl.replace(self.protocol + '//' + self.hostName, '');;
	},
}


$(document).ready(function() {
	nhConfig.init();
});