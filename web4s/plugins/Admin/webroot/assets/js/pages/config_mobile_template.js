"use strict";

var nhConfig = function () {

	var formEl;

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			var formEl = $(this).closest('form');
			nhMain.initSubmitForm(formEl, $(this));
		});
	}
	
	var collapse = function(){
		var collapseGroup = $('.collapse-group');
		if(!nhMain.utilities.notEmpty(collapseGroup)) return false;

		collapseGroup.find('.collapse-btn').on('change', function(e) {
	      	if($(this).val() == 0){
				$(this).closest(collapseGroup).find('.collapse').collapse('show')
			}else{
				$(this).closest(collapseGroup).find('.collapse').collapse('hide')
				$(this).closest(collapseGroup).find('input[type="checkbox"]').prop('checked', false);
			}
	    });
	}

	return {
		init: function() {
			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$(".js-minicolors").each(function () {
			    $(this).minicolors({
			        control: $(this).attr("data-control") || "hue",
			        defaultValue: $(this).attr("data-defaultValue") || "",
			        format: $(this).attr("data-format") || "hex",
			        keywords: $(this).attr("data-keywords") || "",
			        inline: $(this).attr("data-inline") === "true",
			        letterCase: $(this).attr("data-letterCase") || "lowercase",
			        opacity: $(this).attr("data-opacity"),
			        position: $(this).attr("data-position") || "bottom",
			        swatches: $(this).attr("data-swatches") ? $(this).attr("data-swatches").split("|") : [],
			        change: function (value, opacity) {
			            if (!value) return;
			            if (opacity) value += ", " + opacity;
			            if (typeof console === "object") {
			                // console.log(value);
			            }
			        },
			        theme: "bootstrap",
			    });
			});
				
			initSubmit();                
			collapse();                
		}
	};
}();

$(document).ready(function() {
	nhConfig.init();
});
