"use strict";

var nhListComment = function() {
    var formEl;

    var comment = {
        param: {},
        pagination: {},
        listEl: KTUtil.getByID('kt_todo_list'),

        listImageAlbum: $('.list-image-album'),
        config:{
            max_number_files: 10,
            expires_cookie: 10,      
        },
        template:{
            listImageSelect: '<div class="list-image-album"></div>',
            imageSelect: '\
                <span class="kt-spinner kt-spinner--sm kt-spinner--brand kt-spinner--center item-image kt-media kt-media--lg mr-10 position-relative">\
                    <img src="" />\
                    <span class="btn-clear-image-album" title="' + nhMain.getLabel('xoa_anh') +'">\
                        <i class="fa fa-times"></i>\
                    </span>\
                </span>',
        },

        init: function() {
            var self = this;

            $('.kt-selectpicker').selectpicker();

            lightbox.option({
              'resizeDuration': 200,
              'wrapAround': true,
              'albumLabel': ' %1 '+ nhMain.getLabel('tren') +' %2'
            });
        },

        toolbarEvent: function(){
            var self = this;
            
            $(self.listEl).on('click', '.nh-select-all', function() {
                $(this).toggleClass('checked');
                var items = $('#kt_todo_list').find('.kt-todo__items .kt-todo__item');

                for (var i = 0, j = items.length; i < j; i++) {
                    var item = items[i];
                    var checkbox = KTUtil.find(item, '.kt-todo__actions .kt-checkbox input');

                    if ($(this).hasClass('checked')) {
                        KTUtil.addClass(item, 'kt-todo__item--selected item-checked');
                        $(checkbox).prop('checked', true );
                    } else {
                        KTUtil.removeClass(item, 'kt-todo__item--selected item-checked');
                        $(checkbox).prop('checked', false );
                    }
                }
            });

            $(self.listEl).on('change', '.kt-checkbox input', function(){
                var checkedNodes = $('.kt-todo__items').find('.kt-todo__item--selected.item-checked');
                var count = checkedNodes.length;

                $('#nh-selected-number').html(count);
                if (count > 0) {
                    $('#nh-group-action').collapse('show');
                } else {
                    $('#nh-group-action').collapse('hide');
                }
            });

            $(self.listEl).on('click', '.nh-change-status-all', function() {
                var _ids = [];
                $('.kt-todo__item .kt-checkbox input:checked').each(function (i, checkbox) {
                    var _id = nhMain.utilities.parseInt($(this).val());
                    if(_id > 0){
                        _ids.push(_id);
                    }
                });
                var _status = $(this).data('status');

                if(_ids.length == 0){
                    toastr.error(nhMain.getLabel('vui_long_chon_binh_luan'));
                    return false;
                }

                swal.fire({
                    title: nhMain.getLabel('thay_doi_trang_thai'),
                    text: nhMain.getLabel('ban_chac_chan_muon_thay_doi_trang_thai_ban_ghi_nay'),
                    type: 'warning',
                    confirmButtonText: nhMain.getLabel('dong_y'),
                    confirmButtonClass: 'btn btn-sm btn-success',

                    showCancelButton: true,
                    cancelButtonText: nhMain.getLabel('huy_bo'),
                    cancelButtonClass: 'btn btn-sm btn-default'
                }).then(function(result) {
                    if(typeof(result.value) != _UNDEFINED && result.value){
                        nhMain.callAjax({
                            url: adminPath + '/comment/change-status',
                            data:{
                                ids: _ids,
                                status: _status
                            }
                        }).done(function(response) {
                            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                            var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                            if (code == _SUCCESS) {
                                self.loadList();
                                $('.kt-todo__toolbar .kt-todo__check .kt-checkbox input').prop('checked', false); 
                            } else {
                                toastr.error(message);
                            }            
                        })
                    }        
                });
                return false;
            });

            $(self.listEl).on('click', '.nh-delete-all', function() {
                var _ids = [];
                $('.kt-todo__item .kt-checkbox input:checked').each(function (i, checkbox) {
                    var _id = nhMain.utilities.parseInt($(this).val());
                    if(_id > 0){
                        _ids.push(_id);
                    }
                });
                    
                if(_ids.length == 0){
                    toastr.error(nhMain.getLabel('vui_long_chon_binh_luan'));
                    return false;
                }

                swal.fire({
                    title: nhMain.getLabel('xoa_binh_luan'),
                    text: nhMain.getLabel('neu_day_la_binh_luan_cha_thi_se_xoa_tat_ca_cac_binh_luan_tra_loi_lien_quan_ban_co_chac_chan_muon_xoa_binh_luan_nay'),
                    type: 'warning',
                    
                    confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
                    confirmButtonClass: 'btn btn-sm btn-danger',

                    showCancelButton: true,
                    cancelButtonText: nhMain.getLabel('huy_bo'),
                    cancelButtonClass: 'btn btn-sm btn-default'
                }).then(function(result) {
                    if(typeof(result.value) != _UNDEFINED && result.value){
                        nhMain.callAjax({
                            url: adminPath + '/comment/delete',
                            data:{
                                ids: _ids
                            }
                        }).done(function(response) {
                            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                            var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                            if (code == _SUCCESS) {
                                toastr.success(message);
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

        filterEvent: function() {
            var self = this;

            $(document).on('click', '.kt-pagination .pages-link:not(.disabled)', function(e) {
                var page = $(this).data('page');
                self.loadList(self.param, page);
            });
            
            $(document).on('click', '.btn-search', function(e) {
                self.param['keyword'] = $('#nh-keyword').val();
                self.loadList(self.param, self.pagination);
            });

            $(document).on('change', '#nh_status', function(e) {
                self.param['status'] = $(this).val();
                self.loadList(self.param, self.pagination);
            });

            $(document).on('change', '#nh_comment_type', function(e) {
                self.param['type_comment'] = $(this).val();
                self.loadList(self.param, self.pagination);
            });

            $(document).on('click', '.btn-reload', function(e) {
                self.loadList();
                $('#nh-keyword').val('');
                $('.kt-selectpicker').val('');
                $('.kt-selectpicker').selectpicker('refresh');
            });
        },

        listCommentEvent: function(){
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
                self.loadDetailComment(id);
            });

            $(document).on('click', '.btn-list-comment', function(e) {
                var id = $(this).data('id');
                var parent_id = $(this).data('parent-id');

                if(typeof(id) == _UNDEFINED || id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                    return false;
                }

                nhMain.callAjax({
                    async: true,
                    url: adminPath + '/comment/comment-modal',
                    dataType : 'html',
                    data:{
                        id: id,
                        parent_id: parent_id
                    }
                }).done(function(response){
                    $('#list-comment-modal').find('.content-modal').html(response);
                    $('#list-comment-modal').modal('show');
                });
            });            

            $(document).on('click', '.kt-todo__item .kt-checkbox input', function() {
                var item = $(this)[0].closest('.kt-todo__item');
                if (item && $(this)[0].checked) {
                    KTUtil.addClass(item, 'kt-todo__item--selected item-checked');
                } else {
                    KTUtil.removeClass(item, 'kt-todo__item--selected item-checked');
                }
            });

            $('#list-comment-modal').on('hide.bs.modal', function () {
                $(this).find('modal-body').html('');
            });
        },

        viewCommentEvent: function(){
            var self = this;
            $(document).on('click', '.nh-change-status', function() {
                var _id = $(this).data('id');
                var _status = $(this).data('status');
                if(typeof(_id) == _UNDEFINED || _id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin'));
                    return false;
                }

                swal.fire({
                    title: nhMain.getLabel('thay_doi_trang_thai'),
                    text: nhMain.getLabel('ban_chac_chan_muon_thay_doi_trang_thai'),
                    type: 'warning',
                    confirmButtonText: nhMain.getLabel('dong_y'),
                    confirmButtonClass: 'btn btn-sm btn-success',

                    showCancelButton: true,
                    cancelButtonText: nhMain.getLabel('huy_bo'),
                    cancelButtonClass: 'btn btn-sm btn-default'
                }).then(function(result) {
                    if(typeof(result.value) != _UNDEFINED && result.value){
                        nhMain.callAjax({
                            url: adminPath + '/comment/change-status',
                            data:{
                                ids: [_id],
                                status: _status
                            }
                        }).done(function(response) {
                            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                            var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                            if (code == _SUCCESS) {
                                self.loadList();
                                self.loadDetailComment(_id)
                                $('.kt-todo__toolbar .kt-todo__check .kt-checkbox input').prop('checked', false); 
                            } else {
                                toastr.error(message);
                            }              
                        })
                    }       
                });
                return false;
            });

            $(document).on('click', '.nh-delete', function() {
                var _id = $(this).data('id');
                if(typeof(_id) == _UNDEFINED || _id.length == 0){
                    toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin'));
                    return false;
                }

                swal.fire({
                    title: nhMain.getLabel('xoa_binh_luan'),
                    text: nhMain.getLabel('neu_day_la_binh_luan_cha_thi_se_xoa_tat_ca_cac_binh_luan_tra_loi_lien_quan_ban_co_chac_chan_muon_xoa_binh_luan_nay'),
                    type: 'warning',
                    
                    confirmButtonText: '<i class="la la-trash-o"></i>' + nhMain.getLabel('dong_y'),
                    confirmButtonClass: 'btn btn-sm btn-danger',

                    showCancelButton: true,
                    cancelButtonText: nhMain.getLabel('huy_bo'),
                    cancelButtonClass: 'btn btn-sm btn-default'
                }).then(function(result) {
                    if(typeof(result.value) != _UNDEFINED && result.value){
                        nhMain.callAjax({
                            url: adminPath + '/comment/delete',
                            data:{
                                ids: [_id]
                            }
                        }).done(function(response) {
                            var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                            var message = typeof(response.message) != _UNDEFINED ? response.message : '';

                            if (code == _SUCCESS) {
                                toastr.success(message);
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

        loadDetailComment: function(id) {
            if(typeof(id) == _UNDEFINED || id.length == 0){
                toastr.error(nhMain.getLabel('khong_lay_duoc_thong_tin_ban_ghi'));
                return false;
            }

            $('#id_comment').val('');
            var blockLoading = $('#kt_todo_view')[0];
            KTApp.block(blockLoading, blockOptions);
            nhMain.callAjax({
                async: true,
                url: adminPath + '/comment/view-comment/' + id,
                dataType : 'html'
            }).done(function(response){
                KTApp.unblock(blockLoading);
                $('#comment-detail').html(response);
                $('#id_comment').val(id);
                $('.kt-selectpicker').selectpicker();
            });
        },

        loadList: function(params = {}, page = 1) {
            var self = this;
            var blockLoading = $('#kt_todo_list')[0];
            KTApp.block(blockLoading, blockOptions);
            nhMain.callAjax({
                async: true,
                url: adminPath + '/comment/list',
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
            if(typeof(id) == _UNDEFINED || id.length == 0){
                id = $('#comment-detail').data('id');
            }

            $('.kt-todo__item:not(.item-checked)').removeClass('kt-todo__item--selected');
            $('.kt-todo__item[data-id="' + id + '"]').addClass('kt-todo__item--selected');
        },

        attachmentEvent:function() {
            var self = this;

            if(self.listImageAlbum.length == 0) return false;

            $(document).on('click', '#nh-trigger-upload', function(e) {
                var boxComment = $(this).closest('.kt-todo__panel');
                if(boxComment.length == 0) return;

                boxComment.find('input.nh-input-comment-images').trigger('click');
            });

            $(document).on('change', '.nh-input-comment-images', function(e) {
                var typeComment = $('#type-comment').val();
                self.showImagesSelect(this, typeComment);
            });

            $(document).on('click', '.btn-clear-image-album', function(e) {
                $(this).closest('span.item-image').remove();
                self.setValueImages();
            });
        },

        showImagesSelect: function(input = null, typeComment = null) {
            var self = this;
            if(input == null || typeof(input.files) == _UNDEFINED){
                return false;
            }

            if(self.listImageAlbum.length == 0) return false;
            self.listImageAlbum.css('display', '');
            self.listImageAlbum.html('');

            $.each(input.files, function(index, file) {
                if(index >= self.config.max_number_files) return;

                var fileReader = new FileReader();
                fileReader.readAsDataURL(file);
                fileReader.onload = function(e) {
                    self.appendImageSelect(fileReader.result);
                }
            });

            // return false;
            $.each(input.files, function(index, file) {
                if(index >= self.config.max_number_files) return;

                var formData = new FormData();
                formData.append('file', file);
                formData.append('path', typeComment);

                nhMain.callAjax({
                    async: true,
                    url: adminPath + '/comment/upload-file',
                    data: formData,
                    contentType: false,
                    processData: false,
                }).done(function(response) {
                    var code = typeof(response.code) != _UNDEFINED ? response.code : _ERROR;
                    var data = typeof(response.data) != _UNDEFINED ? response.data : {};

                    if (code == _SUCCESS && !$.isEmptyObject(data)) {
                        var urlImage = typeof(data.url) != _UNDEFINED ? data.url : null;
                        var itemElement = self.listImageAlbum.find('span.item-image:eq('+ index +')');
                        if(itemElement.length > 0){
                            itemElement.removeClass('kt-spinner');
                            itemElement.find('img').attr('src', cdnUrl + urlImage);
                        }
                    }
                    self.setValueImages();
                });
            });            
        },

        appendImageSelect: function(urlImage = null){
            var self = this;
            
            if(self.listImageAlbum.length == 0){
                return false;
            }

            if(urlImage == null || typeof(urlImage) == _UNDEFINED || urlImage.length == 0){
                return false;
            }

            self.listImageAlbum.append(self.template.imageSelect);
            self.listImageAlbum.find('span.item-image:last-child img').attr('src', urlImage);
        },

        setValueImages: function(){
            var self = this;
            var listImages = [];

            self.listImageAlbum.find('span.item-image').each(function(index) {
                if($(this).find('img').length > 0){
                    listImages.push($(this).find('img').attr('src'));
                }
            });

            $('#images').val(JSON.stringify(listImages));
        },

        submitEvent: function() {
            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();
                // $('#content').val();

                var check = true;       
                $('.list-image-album').find('span.item-image').each(function(index){
                    if($(this).hasClass('kt-spinner')){
                        toastr.error(nhMain.getLabel('vui_long_cho_he_thong_dang_tai_anh_binh_luan'));
                        check = false;
                    }
                });

                if(check){
                    nhMain.initSubmitForm(formEl, $(this));    
                }
                
            });
        },
    }

    return {
        init: function() {
            formEl = $('#main-form');
            comment.init();
            comment.filterEvent();
            comment.toolbarEvent();
            comment.viewCommentEvent();
            comment.listCommentEvent();
            comment.activeList();
            comment.attachmentEvent();
            comment.submitEvent();
        }
    }
}();

$(document).ready(function() {
    nhListComment.init();    
});