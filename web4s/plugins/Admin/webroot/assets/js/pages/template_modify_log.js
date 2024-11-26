"use strict";

var nhTemplateModifyLog = {
	textareaLog: $('#log-content'),
	textareaOrigin: $('#origin-content'),
	init: function(){
		var self = this;

		if(self.textareaLog.length == 0 || self.textareaOrigin.length == 0) return;

		self.initLib();
		self.events();
	},
	initLib: function(){
		var self = this;

		const editorDiff = new AceDiff({
			element: '[nh-editor="show-diff"]',
			// mode: 'ace/mode/smarty',
			showConnectors: false,
			left: {
				content: self.textareaLog.val(),
				editable: false,
				copyLinkEnabled: true
			},
			right: {
				content: self.textareaOrigin.val(),
				editable: false,
				copyLinkEnabled: true
			}
		});
	},
	events: function(){
		var self = this;

		$(document).on('click', '[nh-action="get-log"]', function(e) {
            var path = $(this).data('path') || '';
            if(path.length == 0) return;

            $('[nh-action="get-log"]').removeClass('history-showing');
            $(this).addClass('history-showing');

            self.getLog(path);
        });
	},	
	getLog: function(path = null){
		var self = this;

		KTApp.blockPage(blockOptions);
		nhMain.callAjax({
            url: adminPath + '/template/modify/get-log-file',
            type: 'POST',
            data: {
            	path: path
            },
            dataType: 'html'
        }).done(function(response) {
			self.textareaLog.val(response);
			self.initLib();

			KTApp.unblockPage();
        });
	}
};

$(document).ready(function() {
	nhTemplateModifyLog.init();
});