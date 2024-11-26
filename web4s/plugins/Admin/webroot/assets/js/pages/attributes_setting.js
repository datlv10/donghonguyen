"use strict";

var nhAttributesCategory = function() {
    var wizardEl;
    var formEl;
    var formApplyAttributes;
    var wizard;

    var initWizard = function () {
        wizard = new KTWizard('kt_wizard', {
            startStep: 1,
            clickableSteps: true,
        });
    }
    var category = {
        param: {},
        pagination: {},
        type: 'product',
        listEl: KTUtil.getByID('kt_todo_list'),

        listAttributesEvent: function(){
            var self = this;
            $(document).on('click', '[nh-category-select="attribute"]', function() {
                var category_id = $(this).val();
                var type = $(this).attr('nh-type');

                if(typeof(category_id) == _UNDEFINED || category_id.length == 0 || typeof(type) == _UNDEFINED){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                self.loadListAttribute(category_id, type);
            });
        },
        loadListAttribute: function(category_id = null, type = null ) {
            if(typeof(category_id) == _UNDEFINED || category_id.length == 0 || typeof(type) == _UNDEFINED){
                toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                return false;
            }

            var wrapElement = $(`#wrap-attributes-${type}`);
            if(wrapElement.length == 0) return;

            nhMain.callAjax({
                url: adminPath + '/setting/attribute/load-attributes-by-category',
                data:{
                    category_id: category_id,
                    type: type
                },
                dataType : 'html'
            }).done(function(response){
                wrapElement.html(response);
            });
        },
        
        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();
                var formElement = $('.kt-wizard-v2__content[data-ktwizard-state="current"] form');

                KTApp.blockPage(blockOptions);
                var formData = formElement.serialize();
                
                nhMain.callAjax({
                    url: formElement.attr('action'),
                    data: formData
                }).done(function(response) {
                    // hide loading
                    KTApp.unblockPage();

                    //show message and redirect page
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                    var data = typeof(response.data) != _UNDEFINED ? response.data : {};
                    toastr.clear();
                    if (code == _SUCCESS) {
                        toastr.info(message);              
                    } else {
                        toastr.error(message);
                    }
                });

            });
        },
    }

    return {
        init: function() {
            wizardEl = KTUtil.get('kt_wizard');
            initWizard();
            category.listAttributesEvent();
            category.submitEvent();
        }
    }
}();

$(document).ready(function() {
    nhAttributesCategory.init();    
});