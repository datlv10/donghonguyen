"use strict";

var nhClearData = function () {

	var formEl;

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();

			var btn_save = $(this);

			nhMain.initSubmitForm(formEl, $(this));
		});
	}

	return {
		init: function() {
			formEl = $('#main-form');			
			initSubmit();

			$('.check-all').change(function () {
				var tableElement = $(this).closest('table');
			    if (this.checked) {
			        tableElement.find('.check-single').each(function () {
			            this.checked = true;
			        });
			    } else {
			        tableElement.find('.check-single').each(function () {
			            this.checked = false;
			        });
			    }
			});

			$('.checkSingle').click(function () {
			    if ($(this).is(':checked')) {
			        var isAllChecked = 0;
			        $('.checkSingle').each(function () {
			            if (!this.checked) isAllChecked = 1;
			        });
			        if (isAllChecked == 0) {
			            $('#checkedAll').prop('checked', true);
			        }
			    } else {
			        $('#checkedAll').prop('checked', false);
			    }
			});
		}
	};
}();


$(document).ready(function() {
	nhClearData.init();
});
