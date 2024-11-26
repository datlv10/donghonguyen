"use strict";

var nhSetting = function () {

    var formEl;
    var formElTemplate;
    var validator;

    var initValidation = function() {
        validator = formEl.validate({
            ignore: ":hidden",
            rules: {
                email: {
                    required: true,
                    pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    minlength: 10,
                    maxlength: 255
                },
                application_password: {
                    required: true,
                },
                email_administrator: {
                    pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    minlength: 10,
                    maxlength: 255
                }
            },
            messages: {
                email: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    pattern: nhMain.getLabel('email_chua_dung_dinh_dang'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                application_password: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin')
                },
                email_administrator: {
                    pattern: nhMain.getLabel('email_chua_dung_dinh_dang'),
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
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
        });
    }

    var configEmail = {
        init: function() {

            $(document).on('change', '#smtp_host', function() {
                $('#wrap-another-config').toggleClass('d-none', $(this).val() != 'other' ? true : false)                
            });  

            $(document).on('click', '#btn-save-config', function(e) {
                e.preventDefault();
                if (validator.form()) {
                    nhMain.initSubmitForm(formEl, $(this));
                }
            });            
        }
    }

    var templateEmail = {
        formElTemplate: null,
        validator: null,
        init: function(){
            var self = this;

            self.formElTemplate = $('#update-template-form');
            if(self.formElTemplate.length == 0) return false;

            self.formElTemplate.on('change', 'select#email-template', function() {
                self.loadInfoTemplate($(this).val());
            });

            $(document).on('click', '#btn-update-template', function(e) {
                e.preventDefault();

                if (self.validator.form()) {
                    var btnEl = $(this);
                    KTApp.progress(btnEl);
                    KTApp.blockPage(blockOptions);

                    var formData = self.formElTemplate.serialize();
                    nhMain.callAjax({
                        url: self.formElTemplate.attr('action'),
                        data: formData
                    }).done(function(response) {
                        KTApp.unprogress(btnEl);
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
                }                
            });
        },
        initLibrary: function(){
            var self = this;

            $('.kt-selectpicker').selectpicker();

            if($('input[name="cc_email"]').length > 0){
                var tagifyCC = new Tagify($('input[name="cc_email"]')[0], {
                    pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    delimiters: ', ',
                    maxTags: 3
                });
            }            

            if($('input[name="bcc_email"]').length > 0){
                var tagifyBCC = new Tagify($('input[name="bcc_email"]')[0], {
                    pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    delimiters: ', ',
                    maxTags: 3
                });
            }
        },
        loadInfoTemplate: function(template_code = null) {
            var self = this;

            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: adminPath + '/setting/email-template/load-info',
                data:{
                    code: template_code
                },
                dataType: 'html',
            }).done(function(response) {
                $('#wrap-form-template').html(response);
                self.initLibrary();                
                self.validateForm();

                KTApp.unblockPage();
            });
        },
        validateForm: function(){
            var self = this;
            
            self.validator = self.formElTemplate.validate({
                ignore: ':hidden',
                rules: {
                    title_email: {
                        required: true,
                        maxlength: 255
                    },
                    template: {
                        required: true
                    },              
                },
                messages: {
                    title_email: {
                        required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                        maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                    },
                    template: {
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
    }

    var editTemplateEmail = {
        formElEditTemplate: null,
        editorTemplate: null,
        init: function(){
            var self = this;

            self.formElEditTemplate = $('#edit-template-form');
            if(self.formElEditTemplate.length == 0) return false;

            self.formElEditTemplate.on('change', 'select#view-template', function() {
                var template = $(this).val();

                KTApp.blockPage(blockOptions);
                nhMain.callAjax({
                    url: adminPath + '/setting/email-template/view-content',
                    data:{
                        template: template
                    },
                    dataType: 'html',
                }).done(function(response) {
                    $('#wrap-content-template').html(response);
                    self.initLibrary();

                    KTApp.unblockPage();
                });
            });

            $(document).on('click', '#btn-edit-view', function(e) {
                e.preventDefault();

                var btnEl = $(this);
                KTApp.progress(btnEl);
                KTApp.blockPage(blockOptions);

                var htmlTemplate = $.trim(self.editorTemplate.getValue());
                self.formElEditTemplate.find('input[name="template_content"]').val(htmlTemplate);

                var formData = self.formElEditTemplate.serialize();
                nhMain.callAjax({
                    url: self.formElEditTemplate.attr('action'),
                    data: formData
                }).done(function(response) {
                    KTApp.unprogress(btnEl);
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
            });
        },
        initLibrary: function(){
            var self = this;

            if($('#editor-template').length > 0){
                self.editorTemplate = ace.edit('editor-template', {
                    mode: 'ace/mode/smarty',
                    theme: 'ace/theme/monokai',
                    enableBasicAutocompletion: true,
                    enableSnippets: true,
                    enableLiveAutocompletion: true,
                    showPrintMargin: false
                });
                self.editorTemplate.setValue($('input[name="template_content"]').val());
            }            
        },
    }

    var emailSendTry = {
        formSendTry: null,
        init: function(){

            self.formSendTry = $('#send-try-form');
            if(self.formSendTry.length == 0) return false;


            var validatorSendTry = self.formSendTry.validate({
                ignore: ':hidden',
                rules: {
                    email_send_try: {
                        required: true,
                        pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    }
                },
                messages: {
                    email_send_try: {
                        required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                        pattern: nhMain.getLabel('email_chua_dung_dinh_dang')
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
            });

            self.formSendTry.on('change', 'input[name="type_send_try"]', function() {
                var type = $(this).val();

                self.formSendTry.find('#wrap-type-content').toggleClass('d-none', type != 'content');
                self.formSendTry.find('#wrap-type-template').toggleClass('d-none', type != 'template');
            });

            $('#btn-send-try').on('click', function(e){
                e.preventDefault();
                if (validatorSendTry.form()) {
                    nhMain.initSubmitForm(self.formSendTry, $(this));
                }
            });
        }
    }

    return {
        init: function() {
            formEl = $('#main-form');         
            initValidation();            
            configEmail.init();
            templateEmail.init();
            editTemplateEmail.init();
            emailSendTry.init();

            $('.kt-selectpicker').selectpicker();
        }
    };
}();

$(document).ready(function() {
    nhSetting.init();    
});