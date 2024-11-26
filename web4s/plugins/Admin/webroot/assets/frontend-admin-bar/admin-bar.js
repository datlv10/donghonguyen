"use strict";

var nhAdminBar = {
	adminPath: null,
	csrfToken: null,
	wrapElement: null,
	adminBarData: null,
	minimize: false,
	init: function(){
		var self = this;

		var scriptElement = $('[nh-script="admin-bar"]');
		if(scriptElement.length == 0) return;

		self.adminPath = scriptElement.attr('nh-admin-path');
		if(self.adminPath == 'undefined' || scriptElement.length == 0) return;
		self.csrfToken = $('html').attr('csrf-token');

		self.adminBarData = localStorage.getItem('admin-bar');
		self.adminBarData = self.adminBarData != null && self.adminBarData.length > 0 ? JSON.parse(self.adminBarData) : {};

		if (!$.isEmptyObject(self.adminBarData)) {
			self.minimize = typeof(self.adminBarData.minimize) != _UNDEFINED && self.adminBarData.minimize > 0 ? true : false;
		}

		self.loadHtmlAdminBar();

		if(self.wrapElement.length == 0) return;

		self.events();
	},
	events: function(){
		var self = this;

		self.wrapElement.on('click', '[nh-admin-bar-action="clear-cache-website"]', function(e){
			self.clearCacheWebsite();
		});

		self.wrapElement.on('click', '[nh-admin-bar-action="clear-cache-browser"]', function(e){
			self.clearCacheBrowser();
		});

		self.wrapElement.on('click', '[nh-admin-bar-action="view-config-block"]', function(e){
			self.showConfigBlock();
		});

		self.wrapElement.on('click', '[nh-admin-bar-action="minimize-admin-bar"]', function(e){
			self.minimizeBlock();
		});
	},
	minimizeBlock: function() {
		var self = this;

		self.toggleMinimize(!$('body').hasClass('admin-minimize'));
	},

	toggleMinimize: function(boolean = true) {
		var self = this;

		localStorage.setItem('admin-bar', JSON.stringify({
			minimize: boolean
        }));

		$('body').toggleClass('admin-minimize', boolean);
	},

	loadHtmlAdminBar: function(){
		var self = this;

		self.callAjax({
    		async: false,
    		dataType: 'html',
			url: self.adminPath + '/frontend/load-admin-bar',
			data: {},
		}).done(function(response) {
			$('body').prepend(response);

			self.wrapElement = $('#nh-admin-bar');

			if(self.minimize) {
				$('body').addClass('admin-minimize');
			}
		});
	},
	clearCacheWebsite: function(){
		var self = this;

		if (!confirm('Do you want to clear the website cache?')) return false;

		self.callAjax({
    		async: false,
    		dataType: 'html',
			url: self.adminPath + '/frontend/clear-cache',
			data: {},
		}).done(function(response) {
        	alert('Clear cache successfully');
        	location.reload();
		});
	},
	clearCacheBrowser: function(){
		var self = this;		
		if (!confirm('Do you want to clear the browser cache?')) return false;

		window.location.reload(true);
	},
	showConfigBlock: function(){
		var self = this;

		var url = window.location.href;
		url = self.replaceUrlParam(url, 'nh-config-block', '1');

		window.location.href = url;
	},
	replaceUrlParam: function(url = null, param = null, value = null){
		if (url == null || typeof(url) == _UNDEFINED || url.length == 0) {
	        return '';
	    }

	    if (param == null || typeof(param) == _UNDEFINED || param.length == 0) {
	        return url;
	    }

		if (value == null || typeof(param) == _UNDEFINED) {
	        value = '';
	    }

	    var pattern = new RegExp('\\b('+ param +'=).*?(&|#|$)');
	    if (url.search(pattern)>=0) {
	        return url.replace(pattern, '$1' + value + '$2');
	    }
	    url = url.replace(/[?#]$/, '');

	    return url + (url.indexOf('?')>0 ? '&' : '?') + param + '=' + value;
	},
	callAjax: function(params = {}){
		var self = this;

		var options = {
			headers: {
		        'X-CSRF-Token': self.csrfToken
		    },
	        async: typeof(params.async) != 'undefined' ? params.async : true,
	        url: typeof(params.url) != 'undefined' ? params.url : '',
	        type: typeof(params.type) != 'undefined' ? params.type : 'POST',
	        dataType: typeof(params.dataType) != 'undefined' ? params.dataType : 'json',
	        data: typeof(params.data) != 'undefined' ? params.data : {},    
	        cache: typeof(params.cache) != 'undefined' ? params.cache : false
	    };

	    if(typeof(params.processData) != 'undefined'){
	    	options.processData = params.processData;
	    }

	    if(typeof(params.contentType) != 'undefined'){
	    	options.contentType = params.contentType;
	    }

		var ajax = $.ajax(options).fail(function(jqXHR, textStatus, errorThrown){
	    	if(typeof(params.not_show_error) == 'undefined'){
	    		console.log(errorThrown)
	    	}
		});
	    return ajax;
	}

}

nhAdminBar.init();
