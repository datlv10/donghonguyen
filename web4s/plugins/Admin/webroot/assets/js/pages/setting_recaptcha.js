"use strict";

var nhRecaptcha = function () {

    var formEl;
    var validator;

    var initValidation = function() {
        validator = formEl.validate({
            ignore: ":hidden",
            rules: {
                site_key: {
                    required: true,
                },
                secret_key: {
                    required: true,
                }
            },
            messages: {
                site_key: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
                },
                secret_key: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
                }
            },

            errorPlacement: function(error, element) {
                var messageRequired = element.attr('message-required');
                if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
                    error.text(messageRequired);
                }
                error.addClass('invalid-feedback')

                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error);
                }else if(element.hasClass('select2-hidden-accessible')){
                    element.closest('.form-group').append(error);
                }else{
                    element.after(error);
                }
            },

            invalidHandler: function(event, validator) {
                KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
            },
        });
    }

    var initSubmit = function() {
        $(document).on('click', '.btn-save', function(e) {
            e.preventDefault();
            if (validator.form()) {
                nhMain.initSubmitForm(formEl, $(this));
            }
        });
    }

    return {
        init: function() {
            formEl = $('#main-form');
            initValidation();
            initSubmit();
        }
    };
}();

$(document).ready(function() {
    nhRecaptcha.init();
});