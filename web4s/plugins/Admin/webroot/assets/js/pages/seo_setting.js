"use strict";

var nhSeoInfo = {
    init: function() {
        var self = this;

        self.event();

        nhMain.selectMedia.dropzoneUpload({
            id: 'robots',
            url: adminPath + '/seo-setting/upload-file-robots',
            ext: 'text/plain',
            maxFile: 1,
        }, null, function() {
            location.reload();
        });
    },
    event: function(){
        var self = this;        

        $(document).on('click', '#btn-save-sitemap', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm($('#sitemap-form'), $(this));
        });

        $(document).on('click', '#btn-save-url', function(e) {
            e.preventDefault();
            
            nhMain.initSubmitForm($('#url-form'), $(this));
        });


        var formElement = $('#tag-form');
        if(formElement == null || formElement == _UNDEFINED || formElement.length == 0){
            return false;
        }

        $.validator.addMethod('regexTag', function(value, element) {
            return value.match(/^[a-z-0-9-/-]+$/);
        }, nhMain.getLabel('the_khong_duoc_chua_ky_tu_dac_biet'));

        var validator = formElement.validate({
            ignore: ':hidden',
            rules: {
                prefix_url: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                    regexTag: true
                },
                prefix_seo_title: {
                    maxlength: 100
                },
                suffixes_seo_title: {
                    maxlength: 100
                },
            },
            messages: {
                prefix_url: {
                    minlength: nhMain.getLabel('thong_tin_nhap_qua_ngan'),
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                prefix_seo_title: {
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                suffixes_seo_title: {
                    
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
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
                validator.errorList[0].element.focus();
            },
        });

        $(document).on('click', '#btn-save-tag', function(e) {
            e.preventDefault();
            if (validator.form()) {
                nhMain.initSubmitForm($('#tag-form'), $(this));
            }
        });

        $(document).on('keyup keypress paste focus', "#prefix-url", function(e) {
            if(nhMain.utilities.notEmpty($('#label-prefix-url'))){
                $("#label-prefix-url").html($(this).val());
            }
        });

        $(document).on('keyup keypress paste focus', "#prefix-seo-title", function(e) {
            if(nhMain.utilities.notEmpty($('.label-prefix-seo-title'))){
                $(".label-prefix-seo-title").html($(this).val());
            }
        });

        $(document).on('keyup keypress paste focus', "#suffixes-seo-title", function(e) {
            if(nhMain.utilities.notEmpty($('.label-suffixes-seo-title'))){
                $(".label-suffixes-seo-title").html($(this).val());
            }
        });
    }
}

$(document).ready(function() {
    nhSeoInfo.init();
});