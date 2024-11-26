"use strict";

var nhSettingPrint = function () {

    var formElTemplate;

    var templatePrint = {
        formElTemplate: null,
        init: function(){
            var self = this;

            self.formElTemplate = $('#update-template-form');
            if(self.formElTemplate.length == 0) return false;

            var validator = self.formElTemplate.validate({
                ignore: ':hidden',
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    template: {
                        required: true
                    }
                },
                messages: {
                    name: {
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

            self.formElTemplate.on('change', 'select#print-template', function() {
                self.loadInfoTemplate($(this).val());
            });

            $(document).on('click', '#btn-update-template', function(e) {
                e.preventDefault();

                if (validator.form()) {
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
        loadInfoTemplate: function(template_code = null) {
            var self = this;

            KTApp.blockPage(blockOptions);
            nhMain.callAjax({
                url: adminPath + '/setting/print-template/load-info',
                data:{
                    code: template_code
                },
                dataType: 'html',
            }).done(function(response) {
                $('#wrap-form-template').html(response);
                $('.kt-selectpicker').selectpicker();
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

    var editTemplatePrint = {
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
                    url: adminPath + '/setting/print-template/view-content',
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

    var testPrint = {
        formElement: null,
        init: function(){

            self.formElement = $('#test-print-form');
            if(self.formElement.length == 0) return false;


            var validatorTestPrint = self.formElement.validate({
                ignore: ':hidden',
                rules: {
                    template_code: {
                        required: true,
                    },
                    id_record: {
                        required: true,
                    }
                },
                messages: {
                    template_code: {
                        required: nhMain.getLabel('vui_long_chon_thong_tin')
                    },
                    id_record: {
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
            });

            $('#btn-test-print').on('click', function(e){
                e.preventDefault();
                if (validatorTestPrint.form()) {
                    var templateCode = self.formElement.find('select[name="template_code"]').val();
                    var idRecord = self.formElement.find('input[name="id_record"]').val();

                    if(templateCode == _UNDEFINED || templateCode.length == 0) return;
                    if(idRecord == _UNDEFINED || idRecord.length == 0) return;
                    
                    var url = adminPath + '/print?code=' + templateCode + '&id_record=' + idRecord;
                    window.open(url, '_blank'); 
                }
            });
        }
    }

    return {
        init: function() { 
            templatePrint.init();
            editTemplatePrint.init();
            testPrint.init();

            $('.kt-selectpicker').selectpicker();
        }
    };
}();

$(document).ready(function() {
    nhSettingPrint.init();
});