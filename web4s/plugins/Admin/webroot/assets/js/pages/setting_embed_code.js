"use strict";

var nhTemplate = function () {

	var formEl;

	var initEditor = function() {

		// init slider input
		var sliderInput = document.getElementById('input-time-delay');
		var slider = document.getElementById('slider-time-delay');

		var start = $('#input-time-delay').val();
        noUiSlider.create(slider, {
            start: [start],
            step: 1000,
            range: {
                'min': [ 0 ],
                'max': [ 9000 ]
            },
            format: wNumb({
                decimals: 0 
            })
        });

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });


        // init editor
		var editor1 = ace.edit('embed-code-header');
		var htmlMode = ace.require('ace/mode/html').Mode;
		editor1.setTheme('ace/theme/monokai');
		editor1.session.setMode(new htmlMode());
		editor1.setShowPrintMargin(false);


		var editor2 = ace.edit("embed-code-top-body");
		editor2.setTheme('ace/theme/monokai');
		editor2.session.setMode(new htmlMode());
		editor2.setShowPrintMargin(false);


    	var editor3 = ace.edit("embed-code-bottom-body");
    	editor3.setTheme('ace/theme/monokai');
		editor3.session.setMode(new htmlMode());
		editor3.setShowPrintMargin(false);
	}

	var initSubmit = function() {
		$('.btn-save').on('click', function(e) {
			e.preventDefault();

			// show loading
			var btn_save = $(this);
			KTApp.progress(btn_save);
			KTApp.blockPage(blockOptions);

			var _editor1 = ace.edit("embed-code-header");
			var _editor2 = ace.edit("embed-code-top-body");
			var _editor3 = ace.edit("embed-code-bottom-body");
	
			nhMain.callAjax({
                url: formEl.attr('action'),
                data: {
                	head: _editor1.getValue(),
                	top_body: _editor2.getValue(),
                	bottom_body: _editor3.getValue(),
                	load_embed: $('input[name="load_embed"]:checked').val(),
                	time_delay: $('input[name="time_delay"]').val()
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
