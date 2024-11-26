'use strict';

var nhMain = {
	callAjax: function(params = {}){
		var self = this;

		var options = {
			headers: {
		        'X-CSRF-Token': csrfToken
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

		var ajax = $.ajax(options).fail(function(jqXHR, textStatus, errorThrown){});
	    return ajax;
	}
}
