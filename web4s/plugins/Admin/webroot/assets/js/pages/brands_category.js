"use strict";

var nhBrandsCategory = function() {
    var formEl;

    var category = {
        param: {},
        pagination: {},
        listEl: KTUtil.getByID('kt_todo_list'),
        filterEvent: function() {
            var self = this;
            
            $(document).on('click', '.btn-search', function(e) {
                self.param['keyword'] = $('#nh-keyword').val();
                self.loadList(self.param, self.pagination);
            });

            $(document).on('click', '.btn-reload', function(e) {
                self.loadList();
                $('#nh-keyword').val('');
            });
        },
        listBrandsEvent: function(){
            var self = this;

            KTUtil.on(self.listEl, '.kt-todo__item', 'click', function(e) {
                var actionsEl = KTUtil.find(this, '.kt-todo__actions');
                var id = $(this).data('id');

                // skip actions click
                if (e.target === actionsEl || (actionsEl && actionsEl.contains(e.target) === true)) {
                    return false;
                }

                if(typeof(id) == _UNDEFINED || id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                self.activeList(id);
                self.loadListBrands(id);
            });            

            $(document).on('click', '.kt-todo__item .kt-checkbox input', function() {
                var wrapItem = $(this).closest('.kt-todo__items');
                var item = $(this).closest('.kt-todo__item');
                var id = item.data('id');

                wrapItem.find('.kt-todo__item').removeClass('kt-todo__item--selected item-checked');
                item.addClass('kt-todo__item--selected item-checked');

                if(typeof(id) == _UNDEFINED || id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                self.activeList(id);
                self.loadListBrands(id);
            });
        },
        loadListBrands: function(id) {
            if(typeof(id) == _UNDEFINED || id.length == 0){
                toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                return false;
            }

            var blockLoading = $('#kt_todo_view')[0];
            KTApp.block(blockLoading, blockOptions);
            nhMain.callAjax({
                async: true,
                url: adminPath + '/setting/brand/load-list-brands/' + id,
                dataType : 'html'
            }).done(function(response){
                KTApp.unblock(blockLoading);
                $('[nh-list-brands]').html(response);
            });
        },
        loadList: function(params = {}, page = 1) {
            var self = this;
            var blockLoading = $('#kt_todo_list')[0];
            KTApp.block(blockLoading, blockOptions);
            nhMain.callAjax({
                async: true,
                url: adminPath + '/setting/brand/brands-category',
                dataType: 'html',
                data: {
                    query: params,
                    pagination: {
                        page: page
                    }
                }
            }).done(function(response){
                KTApp.unblock(blockLoading);
                $('#nh-group-action').collapse('hide');
                $('.kt-todo__items').html(response);
                $('.nh-select-all').removeClass('checked');
                self.activeList();
            });
        },
        activeList: function(id) {
            if(typeof(id) == _UNDEFINED || id.length == 0) return false

            $('.kt-todo__actions .kt-checkbox input').prop('checked', false );
            $('.kt-todo__item').removeClass('kt-todo__item--selected item-checked');

            $('.kt-todo__item[data-id="' + id + '"]').addClass('kt-todo__item--selected item-checked');
            $('.kt-todo__item[data-id="' + id + '"]').find('.kt-checkbox input').prop('checked', true );
        },
        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();

                formEl = $(this).closest('form');
                nhMain.initSubmitForm(formEl, $(this));
            });
        },
    }

    return {
        init: function() {
            category.filterEvent();
            category.listBrandsEvent();
            category.activeList();
            category.submitEvent();
        }
    }
}();

$(document).ready(function() {
    nhBrandsCategory.init();    
});