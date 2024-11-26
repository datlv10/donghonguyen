'use strict';

var nhUtilities = {
	notEmpty: function(value = null){
		if(typeof(value) == _UNDEFINED){
			return false;
		}

		if(value == null){
			return false;
		}

		if(value.length == 0){
			return false;
		}

		return true;
	},
	parseNumberToTextMoney: function(number = null){
		if (typeof(number) != 'number' || isNaN(number) || typeof(number) == _UNDEFINED) {
	        return 0;
	    }	    
    	return number.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
	},
	parseTextMoneyToNumber: function(text_number = null){
		if (typeof(text_number) == _UNDEFINED) {
	        return 0;
	    }

		var number = parseFloat(text_number.toString().replace(/,/g, ''));
		if(isNaN(number)) number = 0;
		
		return number;
	},
	parseFloat: function(number = null){
		if (isNaN(number) || typeof(number) == _UNDEFINED || number == null) {
	        return 0;
	    }	

		number = parseFloat(number);
		if (isNaN(number)) {
	        return 0;
	    }
	    return number;
	},
	parseInt: function(number = null){
		if (isNaN(number) || typeof(number) == _UNDEFINED || number == null) {
	        return 0;
	    }	

		number = parseInt(number);
		if (isNaN(number)) {
	        return 0;
	    }
	    return number;
	},
	parseIntToDateString: function(number = null){
		var self = this;
		var date_string = '';
		var int_number = nhLayout.utilities.parseInt(number);
		if(int_number > 0){
			var date = new Date(int_number * 1000);	
			date_string = date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
		}
		return date_string;
	},
	parseIntToDateTimeString: function(number = null){
		var self = this;
		var date_string = '';
		var int_number = nhLayout.utilities.parseInt(number);
		if(int_number > 0){
			var date = new Date(int_number * 1000);
			var minutes = date.getMinutes();
			if(minutes < 10){
				minutes = '0' + minutes;
			}				

			var hours = date.getHours();
			if(hours < 10){
				hours = '0' + hours;
			}

			date_string = hours + ':' + minutes + ' - ' +  date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
		}
		return date_string;
	},
	parseJsonToObject: function(json_string = null){
		var result = {};
		try {
	        result = JSON.parse(json_string);
	    } catch (e) {
	        return {};
	    }
	    return result;
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
	getParamInUrl: function(param_name = null, url = null){
		var self = this;

		if(!self.notEmpty(param_name)) return null;
		if(!self.notEmpty(url)) {
			url = nhLayout.fullUrl
		}

		param_name = param_name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + param_name + "(=([^&#]*)|&|#|$)");
	    var results = regex.exec(url);

	    if (!results) return null;
	    if (!results[2]) return '';

	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	},
	getThumbImage: function(url = null, size = 150){
		var self = this;

		if(!self.notEmpty(url)) return '';
		if($.inArray(size, [50, 150, 250, 350]) == -1) size = 150;

		var urlSplit = url.split('/');
		urlSplit[1] = 'thumbs';

		var fileName = self.getFileName(url);
		var ext = fileName.split('.').pop();

		if(!self.notEmpty(ext)) return '';
		
		var newFile = fileName.replace('.' + ext, '');
		newFile += '_thumb_' + size + '.' + ext;

		urlSplit[urlSplit.length - 1] = newFile;

		return urlSplit.join('/');
	},
	showLoading: {
		htmlTemplate: '\
			<div class="bg-overlay"></div>\
			<div class="sk-flow">\
				<div class="sk-flow-dot"></div>\
				<div class="sk-flow-dot"></div>\
				<div class="sk-flow-dot"></div>\
			</div>',
		block: function(element = null) {
			var self = this;
			if(element == null || typeof(element) == _UNDEFINED || element.length == 0){
				nhLayout.showLog(nhLayout.getLabel('doi_tuong_hien_thi_loading_khong_ton_tai'));
				return false;
			}
			var htmlLoading = $('<div nh-loading class="loading-block">').append(self.htmlTemplate)
			element.append(htmlLoading);
		},
		page: function(){
			var self = this;
			var htmlLoading = $('<div nh-loading class="loading-page">').append(self.htmlTemplate);
			$('body').append(htmlLoading);
		},
		remove: function(element = null){
			var wrapElement = $(document);
			if(element != null && element != _UNDEFINED && element.length > 0){
				wrapElement = element;
			}
			wrapElement.find('div[nh-loading]').each(function( index ) {
			  	$(this).remove();
			});
		}
	}
}
