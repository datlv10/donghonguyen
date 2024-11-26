'use strict';

var nhConfigBlockFrontend = {
	init: function(){
		var self = this;

		if($('[nh-block]').length == 0) return false;

		$(document).on('mouseenter', '[nh-block]', function(e) {
			var code = $(this).attr('nh-block');
			if(typeof(code) == _UNDEFINED || !code.length > 0 || $(this).find('.nh-config-action').length > 0) return false;

			var html = '\
			<div class="nh-config-action">\
				<a target="_blank" href="/admin/template/block/update/'+ code +'" target="_blank" class="inner-action">\
					<i class="fa-light fa-gear"></i>' 
					+ code +
				'</a>\
			</div>';

			$(this).append(html)
		});

		$(document).on('mouseleave', '[nh-block]', function(e) {
			$(this).find('.nh-config-action').remove();
		});
	}
}

$(document).ready(function() {
	self.nhConfigBlockFrontend.init();
});
