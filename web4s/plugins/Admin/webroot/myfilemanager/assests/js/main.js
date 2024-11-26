"use strict";

var nhMain = {
	callAjax: function(params = {}){
		var self = this;

		var options = {
			headers: {
				'X-CSRF-Token': csrfToken,
		        'token': fileManagerToken,
		    },
	        async: typeof(params.async) != _UNDEFINED ? params.async : true,
	        url: typeof(params.url) != _UNDEFINED ? params.url : '',
	        type: typeof(params.type) != _UNDEFINED ? params.type : 'POST',
	        dataType: typeof(params.dataType) != _UNDEFINED ? params.dataType : 'json',
	        data: typeof(params.data) != _UNDEFINED ? params.data : {},    
	        cache: typeof(params.cache) != _UNDEFINED ? params.cache : false
	    };

	    if(typeof(params.processData) != _UNDEFINED){
	    	options.processData = params.processData;
	    }

	    if(typeof(params.contentType) != _UNDEFINED){
	    	options.contentType = params.contentType;
	    }

		var ajax = $.ajax(options).fail(function(jqXHR, textStatus, errorThrown){

			if(jqXHR.status == 403){
				return false;
			}

	    	if(typeof(params.not_show_error) == _UNDEFINED){
	    		// log error
	    		// console.log(textStatus + ': ' + errorThrown);	    	
	    	}
		});
		
	    return ajax;
	},
	getLabel: function(key = null){
		if(typeof(locales[key]) == _UNDEFINED){
			return key;
		}
		
		return locales[key];
	},
	utilities: {
		parseBytelabel: function(bytes = 0, decimals = 2){
			var self = this;

			if (!+bytes) return '0 Bytes'

		    const k = 1024;
		    const dm = decimals < 0 ? 0 : decimals;
		    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

		    const i = Math.floor(Math.log(bytes) / Math.log(k));

		    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
		},
		parseIntToDateString: function(number = null){
			var self = this;
			var date_string = '';
			var int_number = parseInt(number);
			if(int_number > 0){
				var date = new Date(int_number * 1000);	
				date_string = date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
			}
			return date_string;
		},
		parseIntToDateTimeString: function(number = null){
			var self = this;

			var date_string = '';
			var int_number = parseInt(number);
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
		isJson: function(str = null){
			try {
		        JSON.parse(str);
		    } catch (e) {
		        return false;
		    }
		    return true;
		},
		getThumbs: function(url = null, size = null){

			if(
				url == null || typeof(url) == _UNDEFINED || url.length == 0 || 
				size == null || $.inArray(size, [50, 150, 250, 350, 500, 720]) == -1
			) return url;
			
			var path = url.split('/');
			
			path[1] = 'media_thumbs';
			var extension = url.replace(/^.*\./, '');
			var fileName = path[path.length - 1];
			var name = fileName.substr(0,fileName.lastIndexOf('.'));

			if(
				typeof(name) == _UNDEFINED || name.length == 0 || 
				typeof(extension) == _UNDEFINED || extension.length == 0
			) return url;

			var fileName = name + '_thumb_' + size + '.' + extension;
			path[path.length - 1] = fileName;
			return path.join('/');
		}
	}
}