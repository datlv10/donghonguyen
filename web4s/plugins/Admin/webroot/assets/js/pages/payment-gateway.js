"use strict";

var nhPaymentGateway = function () {
    var wizardEl;
    var validator;
    var wizard;
    var formPayment;

    var initWizard = function () {
        wizard = new KTWizard('kt_wizard', {
            startStep: 1,
            clickableSteps: true
        });
    }

    var initSubmit = function() {
        $(document).on('click', '.btn-save', function(e) {
            var formPayment = $('.kt-wizard-v2__content[data-ktwizard-state=current] form');
            e.preventDefault();

            var rules = {};
            var messages = {};
            $('input.check-required').each(function() {
                var name = $(this).attr('name');

                rules[name] = {
                    required: true,
                    maxlength: 255
                }

                messages[name] = {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                }
            });

            validator = formPayment.validate({
                ignore: ":hidden",
                rules,
                messages,

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

            if (validator.form()) {
                $('textarea.check-required').each(function() {
                    var name = $(this).attr('id');
                    $('#' + name).val(tinymce.get(name).getContent());
                })
                nhMain.initSubmitForm(formPayment, $(this));
            }
        }); 
    }

    var initRepeater = function() {
        $('#kt_repeater').repeater({
            show: function () {
                $(this).slideDown();
            },

            hide: function (deleteElement) {                
                $(this).slideUp(deleteElement);                 
            }   
        });
    }

    var events = function() {
        $(document).on('click', 'a[role="option"]', function(e) {
            var bankName = $(this).find('.text').text();
            $(this).closest('[data-repeater-item]').find('[bank-name][type="hidden"]').val(bankName);
        });

        $(document).on('change', '[data-repeater-item] > select', function(e) {
            var bankCode = $(this).val();
            var bankName = $(this).find('option[value="'+ bankCode +'"]').text();

            $(this).closest('[data-repeater-item]').find('[bank-name][type="hidden"]').val(bankName);
        });

        
    }

    return {
        init: function() {
            wizardEl = KTUtil.get('kt_wizard');
            initWizard();
            initSubmit();
            initRepeater();
            events();

            $('.kt-selectpicker').selectpicker();
            nhMain.tinyMce.simple();
        }

    };
}();

$(document).ready(function() {
    nhPaymentGateway.init();
});