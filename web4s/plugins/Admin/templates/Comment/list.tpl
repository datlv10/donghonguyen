<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'binh_luan')}
            </h3>
        </div>
    </div>
</div>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-grid kt-grid--desktop kt-grid--ver-desktop  kt-todo entire-comment" id="kt_todo">
        <div class="kt-grid__item kt-grid__item--fluid kt-todo__content" id="kt_todo_content">
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="kt-grid__item kt-grid__item--fluid  kt-portlet kt-portlet--height-fluid kt-todo__list" id="kt_todo_list">
                        <div class="kt-portlet__body kt-portlet__body--fit-x">
                            <div class="kt-todo__head">
                                <div class="kt-todo__toolbar">
                                    <div class="kt-searchbar">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="flaticon2-search"></i>
                                                </span>
                                            </div>
                                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" autocomplete="off">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary btn-search" type="button">{__d('admin', 'tim_kiem')}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap wrp-action-comment">
                                        <div class="kt-form__group" style="width: 150px;">
                                            <div class="kt-form__control">
                                                {$this->Form->select('type_comment', $this->CommentAdmin->typeComment(), ['id'=>'nh_comment_type', 'empty' => "-- {__d('admin', 'loai_binh_luan')} --", 'default' => "{if isset($comment.type_comment)}{$comment.type_comment}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                            </div>
                                        </div>
                                        <div class="kt-form__group kt-margin-l-10" style="width: 150px;">
                                            <div class="kt-form__control">
                                                {$this->Form->select('status', $this->CommentAdmin->listStatus(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => "{if isset($comment.status)}{$comment.status}{/if}", 'data-id'=> "{if !empty($comment.id)}{$comment.id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <button type="button" class="kt-todo__icon kt-todo__icon--light kt-margin-l-10 btn-reload" data-toggle="kt-tooltip" title="{__d('admin', 'tai_lai_danh_sach')}">
                                                <i class="flaticon2-refresh-arrow"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 kt-padding-r-25 kt-padding-l-25 collapse">
                                <div class="row align-items-center">
                                    <div class="col-xl-12">
                                        <div class="kt-form__group kt-form__group--inline">
                                            <div class="kt-form__label kt-form__label-no-wrap">
                                                <label class="kt-font-bold kt-font-danger-">
                                                    {__d('admin', 'da_chon')}
                                                    <span id="nh-selected-number">0</span> :
                                                </label>
                                            </div>
                                            <div class="kt-form__control">
                                                <div class="btn-toolbar">
                                                    <a href="javascript:;" class="btn btn-sm btn-success mobile-mb-5 mr-10 nh-select-all">
                                                        {__d('admin', 'chon_tat_ca')}
                                                    </a>
                                                    <div class="dropdown mr-10">
                                                        <button type="button" class="btn btn-brand btn-sm dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                                            {__d('admin', 'trang_thai')}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="javascript:;" class="dropdown-item nh-change-status-all" data-status="0">
                                                                {__d('admin', 'khong_duyet')}
                                                            </a>
                                                            <a href="javascript:;" class="dropdown-item nh-change-status-all" data-status="1">
                                                                {__d('admin', 'duyet')}
                                                            </a>
                                                        </div>
                                                    </div>
                                                  
                                                    <button class="btn btn-sm btn-danger nh-delete-all mobile-mb-5" type="button">
                                                        {__d('admin', 'xoa')}
                                                    </button>                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-separator kt-separator--space-lg kt-margin-t-20 kt-margin-b-0"></div>
                            <div class="kt-todo__body">
                                <div class="kt-todo__items" data-type="task">
                                    {$this->element('../Comment/list_comment_element')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="kt-grid__item kt-grid__item--fluid  kt-portlet kt-portlet--height-fluid kt-todo__view" id="kt_todo_view">
                        <div class="kt-portlet__body kt-portlet__body--fit-y">
                            <div id="comment-detail" data-id="{if !empty($first_comment.id)}{$first_comment.id}{/if}" class="kt-todo__wrapper">
                                {$this->element('../Comment/view_comment_element', ['comment' => $first_comment])}
                            </div>
                            <form id="main-form" action="{ADMIN_PATH}/comment/admin-reply" method="POST" autocomplete="off" novalidate="novalidate">
                                <div class="form-group">
                                    <h6>{__d('admin', 'nhap_binh_luan')}:</h6>
                                    <textarea name="content" class="form-control" id="content" rows="6" style="resize: none;"></textarea>
                                </div>
                                <input id="id_comment" name="id" type="hidden" value="{if !empty($first_comment.id)}{$first_comment.id}{/if}">
                                <div class="list-image-album" style="display: none"></div>
                                <div class="kt-todo__primary d-flex kt-margin-t-25 kt-margin-b-25">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary btn-save">
                                            {__d('admin', 'binh_luan')}
                                        </button>
                                    </div>
                                    <div class="kt-todo__panel ml-5">
                                        <span class="btn btn-sm btn-primary" id="nh-trigger-upload">
                                            <i class="flaticon-photo-camera kt-margin-r-0"></i>
                                        </span>
                                        <input name="files[]" type="file" class="d-none nh-input-comment-images" accept="image/*" multiple="multiple">
                                        <input name="images" id="images" type="hidden" value="">
                                    </div>
                                </div>
                            </form>                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{$this->element('../Comment/list_comment_modal')}