"use strict";

var nhStorePartner = function() {
    var wizardEl;;
    var wizard;

    var initWizard = function () {
        wizard = new KTWizard('kt_wizard', {
            startStep: 1,
            clickableSteps: true,
        }); 
    }
    var storepartner = {
        param: {},
        pagination: {},
        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();
                var formElement = $('.kt-wizard-v2__content[data-ktwizard-state="current"] form');
                nhMain.initSubmitForm(formElement, $(this));
            });

            $(document).on('click', '[btn-action="sync-stores"]', function(e) {
                var self = this;

                var url = adminPath + '/setting/sync-stores-kiotviet';
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
            });
            $(document).on('click', '.nh-is-default', function() {
                var _id = $(this).data('id');
                if(_id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi_da_chon'));
                    return false;
                }
                swal.fire({
                    title: nhMain.getLabel('chon_cua_hang_mac_dinh'),
                    text: nhMain.getLabel('ban_co_chac_chan_muon_chon_cua_hang_nay_lam_mac_dinh'),
                    type: 'warning',
                    
                    confirmButtonText: nhMain.getLabel('dong_y'),
                    confirmButtonClass: 'btn btn-sm btn-danger',

                    showCancelButton: true,
                    cancelButtonText: nhMain.getLabel('huy_bo'),
                    cancelButtonClass: 'btn btn-sm btn-default'
                }).then(function(result) {
                    if(typeof(result.value) != _UNDEFINED && result.value){
                        nhMain.callAjax({
                            url: adminPath + '/setting/store-default-kiotviet',
                            data:{
                                id: _id
                            }
                        }).done(function(response) {
                            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                            var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                            if (code == _SUCCESS) {
                                toastr.info(message);
                                location.reload();
                            } else {
                                toastr.error(message);
                            }
                        })
                    }       
                });
                return false;
            });

            
        },
        registerWebhook: function(){

            $(document).on('click', '[btn-action="register-webhook"]', function(e) {
                var wrapElement = $(this).closest('.item');
                let type_webhook = wrapElement.find('[nh-type-webhook]').val();
                let url_webhook = wrapElement.find('[nh-url-webhook]').val();

                var self = this;
                nhMain.callAjax({
                    url: adminPath + '/setting/register-kiotviet',
                    data: {
                        type_webhook: type_webhook,
                        url_webhook: url_webhook
                    }
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
            });
        },
        listWebhook: function(){
            $(document).on('click', '[btn-action="list-webhook"]', function(e) {
                   var self = this;
                nhMain.callAjax({
                    url: adminPath + '/setting/list-kiotviet'
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
            });
        },
        deleteWebhook: function(){

            $(document).on('click', '[btn-action="delete-webhook"]', function(e) {
                var self = this;
                nhMain.callAjax({
                    url: adminPath + '/setting/delete-kiotviet'
                    
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
            });
        },
        attributeWebhook: function(){

            $(document).on('click', '[btn-action="attribute-webhook"]', function(e) {
                var self = this;
                nhMain.callAjax({
                    url: adminPath + '/setting/sync-attribute-kiotviet'
                    
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
            });
        },
    }

    var syncAllProduct = {
        init: function() {
            var self = this;

            $(document).on('click', '[nh-sync]', function(e) {
                var btnSync = $(this);
                var wrapElement = $(this).closest('#sync-all-product-modal');

                let product = wrapElement.find('[nh-product]:checked').val();
                let allow_add_new = typeof(product) !== _UNDEFINED ? 1 : 0;

                // show loading
                btnSync.find('.icon-spinner').removeClass('d-none');
                $('[nh-sync]').addClass('disabled');

                var sync_store = typeof($(this).attr('nh-sync')) != _UNDEFINED ? $(this).attr('nh-sync') : '';
                self.syncData(0, allow_add_new, function(e) {
                    // remove loading

                    btnSync.find('.icon-spinner').addClass('d-none');
                    $('[nh-sync]').removeClass('disabled');
                });

                return false;
            });
            
            $('#sync-kiot-modal').on('hidden.bs.modal', function () {
                location.reload();
            });
            
        },
        syncData: function(current_item = 0, allow_add_new = 0, callback = null){
            var self = this;

            if (typeof(callback) != 'function') {
                callback = function () {};
            }

            nhMain.callAjax({
                url: adminPath + '/product/kiotviet-sync-all-product',
                data: {
                    current_item: current_item,
                    allow_add_new: allow_add_new
                }
            }).done(function(response) {
                var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                var message = typeof(response.message) != _UNDEFINED ? response.message : '';
                var data = typeof(response.data) != _UNDEFINED ? response.data : '';
                if (code == _SUCCESS) {
                    if(typeof(data.current_item) != _UNDEFINED && data.current_item > 0){
                        $('#label-sync').html(data.current_item);
                    }
                    if(typeof(data.total_product) != _UNDEFINED && data.total_product > 0){
                        $('#total_product').html(data.total_product);
                    }
                    if(typeof(data.continue) != _UNDEFINED && data.continue){
                        self.syncData(data.current_item, data.allow_add_new, callback);
                    }else{              
                        toastr.success(message);
                        callback(response);
                    }
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
            storepartner.submitEvent();
            storepartner.registerWebhook();
            storepartner.listWebhook();
            storepartner.deleteWebhook();
            storepartner.attributeWebhook();
            syncAllProduct.init();
        }
    }
}();

$(document).ready(function() {
    nhStorePartner.init();    
});