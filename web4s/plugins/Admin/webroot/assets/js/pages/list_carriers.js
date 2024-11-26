"use strict";

var nhShippingCarriers = function () {
    var wizardEl;
    var validator;
    var wizard;
    var formCarrier;

    var initWizard = function () {
        wizard = new KTWizard('kt_wizard', {
            startStep: 1,
            clickableSteps: true
        });
    }

    var initSubmit = function() {
        $(document).on('click', '.btn-save', function(e) {
            var formCarrier = $('.kt-wizard-v2__content[data-ktwizard-state=current] form');
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

            validator = formCarrier.validate({
                ignore: ':hidden',
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
                $('textarea').each(function() {
                    var name = $(this).attr('id');
                    $('#' + name).val(tinymce.get(name).getContent());
                })
                nhMain.initSubmitForm(formCarrier, $(this));
            }
        }); 
    }

    var ghnCarrier = {
        formElement: $('#ghn-form'),
        init: function(){
            var self = this;
            if(self.formElement.length == 0) return false;

            self.event();
        },
        event: function(){
            var self = this;

            $(document).on('click', '[btn-action="sync-cities"]', function(e) {
                self.syncData('cities');
            });

            $(document).on('click', '[btn-action="sync-districts"]', function(e) {
                self.syncData('districts');
            });

            $(document).on('click', '[btn-action="sync-wards"]', function(e) {
                self.syncData('wards');
            });

            $(document).on('click', '[btn-action="sync-stores"]', function(e) {
                self.syncData('stores');
            });

            $(document).on('click', '[btn-action="initialization-cities"]', function(e) {
                self.initializationData('cities');
            });

            $(document).on('click', '[btn-action="initialization-districts"]', function(e) {
                self.initializationData('districts');
            });

            $(document).on('click', '[btn-action="initialization-wards"]', function(e) {
                self.initializationData('wards');
            });
        },
        initializationData: function(type = null) {
            var self = this;

            var url = null;
            switch(type){
                case 'cities':
                    url = adminPath + '/setting/carriers/ghn-initialization-cities';
                break;

                case 'districts':
                    url = adminPath + '/setting/carriers/ghn-initialization-districts';
                break;

                case 'wards':
                    url = adminPath + '/setting/carriers/ghn-initialization-wards';
                break;
            }

            if(url == null) return false;

            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: url
            }).done(function(response) {
                KTApp.unblockPage();

                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                toastr.clear();
                if (code == _SUCCESS) {
                    toastr.info(message);                    
                } else {
                    toastr.error(message);
                }
            });
        },
        syncData: function(type = null) {
            var self = this;

            var url = null;
            switch(type){
                case 'cities':
                    url = adminPath + '/setting/carriers/ghn-sync-cities';
                break;

                case 'districts':
                    url = adminPath + '/setting/carriers/ghn-sync-districts';
                break;

                case 'wards':
                    url = adminPath + '/setting/carriers/ghn-sync-wards';
                break;

                case 'stores':
                    url = adminPath + '/setting/carriers/ghn-sync-stores';
                break;
            }

            if(url == null) return false;

            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: url
            }).done(function(response) {
                KTApp.unblockPage();

                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                toastr.clear();
                if (code == _SUCCESS) {
                    toastr.info(message);
                    location.reload();
                } else {
                    toastr.error(message);
                }
            });
        }
    }

    var ghtkCarrier = {
        formElement: $('#ghtk-form'),
        init: function(){
            var self = this;
            if(self.formElement.length == 0) return false;

            self.event();
        },
        event: function(){
            var self = this;

            $(document).on('click', '[btn-action="ghtk-sync-stores"]', function(e) {
                self.syncData('stores');
            });
        },
        syncData: function(type = null) {
            var self = this;

            var url = null;
            switch(type){
                case 'stores':
                    url = adminPath + '/setting/carriers/ghtk-sync-stores';
                break;
            }

            if(url == null) return false;

            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: url
            }).done(function(response) {
                KTApp.unblockPage();

                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                toastr.clear();
                if (code == _SUCCESS) {
                    toastr.info(message);
                    location.reload();
                } else {
                    toastr.error(message);
                }
            });
        }
    }

    return {
        init: function() {
            wizardEl = KTUtil.get('kt_wizard');
            initWizard();
            initSubmit();
            ghnCarrier.init();
            ghtkCarrier.init();

            $('.kt-selectpicker').selectpicker();         
        }

    };
}();

$(document).ready(function() {
    nhShippingCarriers.init();
});