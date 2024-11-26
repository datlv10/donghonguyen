"use strict";

var nhTemplate = function () {

	var formEl;

	var initEditor = function() {
		var editor = ace.edit('editor');
		editor.setTheme('ace/theme/monokai');

		var jsMode = ace.require('ace/mode/javascript').Mode;
		editor.session.setMode(new jsMode());
		editor.setShowPrintMargin(false);

		ace.require('ace/ext/language_tools');
		editor.setOptions({
	        enableBasicAutocompletion: true,
	        enableSnippets: true,
	        enableLiveAutocompletion: true,
	        fontSize: "14px",
	        minLines: 40,
	        maxLines: 40
	    });

	    // show full screen
		$(document).on('click', '[nh-btn="full-screen-editor"]', function(e) {
			if(!$('#editor').hasClass('ace_editor')) return;
			$('#editor').addClass('full-screen-editor');
		});

		// remove full screen
		$(document).on('keydown', function(e) {
			if($('#editor').hasClass('full-screen-editor') && e.key == 'Escape'){
				$('#editor').removeClass('full-screen-editor');
			};
		});
	}

	var initSubmit = function() {
		$('.btn-save').on('click', function(e) {
			e.preventDefault();

			// show loading
			var btn_save = $(this);
			KTApp.progress(btn_save);
			KTApp.blockPage(blockOptions);

			var editor = ace.edit('editor');
			nhMain.callAjax({
                url: formEl.attr('action'),
                data: {
                	content: editor.getValue()
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';
                toastr.clear();

                // hide loading
				KTApp.unprogress(btn_save);
				KTApp.unblockPage();
				
                if (code == _SUCCESS) {
                    self.template = data;
                    toastr.info(message);
                } else {
                    toastr.error(message);
                }            
            })
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');			
			initSubmit();      
			initEditor();
		}
	};
}();

$(document).ready(function() {
	nhTemplate.init();
});
