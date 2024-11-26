"use strict";

$( document ).ready(function() {
	$(".js-minicolors-select").each(function (e) {
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
	                $(this).closest('.form-group').find('.js-minicolors-input').val(value);
	            }
	        },
	        theme: "bootstrap",
	    });
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

	
});

var nhMobileBlock = function () {

	var formEl;
	var validator;

	var initValidation = function() {
		validator = formEl.validate({
			ignore: ":hidden",
			rules: {
				name: {
					required: true,
					maxlength: 255
				}
			},
			messages: {
				name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                }
            },

            errorPlacement: function(error, element) {
                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error.addClass('invalid-feedback'));
                }else{                	
                    element.after(error.addClass('invalid-feedback'));
                }
            },

			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			},
		});
	}

	var initSubmit = function() {
		$(document).on('click', '.btn-main-config-save', function(e) {
			e.preventDefault();

			if (validator.form()) {
				nhMain.initSubmitForm(formEl, $(this));
			}
		});
	}

	return {
		init: function() {
			formEl = $('#main-config-form');

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$('.datetime-picker').each(function() {
				$(this).datetimepicker({
		            format: 'dd/mm/yyyy - hh:ii',
		            showMeridian: true,
		            todayHighlight: true,
		            autoclose: true,
		            startDate: new Date()
		        });
			});

			initValidation();
			initSubmit();
		}
	};
}();

$(document).ready(function() {
	nhMobileBlockConfig.init();
	nhMobileBlock.init();
});
