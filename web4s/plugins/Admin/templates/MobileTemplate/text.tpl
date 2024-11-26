{assign var = url_list value = "{ADMIN_PATH}/mobile-app/dashboard"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-default">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/save-text" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'nhan_giao_dien')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <span id="add-new-text" class="btn btn-sm btn-success">
                        <i class="fa fa-tag"></i>
                        {__d('admin', 'them_nhan_moi')}
                    </span>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div id="wrap-text" class="row">                    
                    {if !empty($text)}
                        {foreach from = $text key = text_code item = text_name}
                            {$this->element("../MobileTemplate/item_text", [
                                'text_code' => $text_code,
                                'text_name' => $text_name
                            ])}
                        {/foreach}
                    {else}
                        {$this->element("../MobileTemplate/item_text", [
                            'text_code' => '',
                            'text_name' => ''
                        ])}
                    {/if}
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="text-info-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cap_nhat_nhan_giao_dien')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="form-add-text" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma')}
                        </label>
                        <input id="code" value="" class="form-control form-control-sm" type="text">
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'noi_dung')}
                        </label>
                        <input id="text" value="" class="form-control form-control-sm" type="text">
                    </div>

                    <input id="index" value="" type="hidden">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-save-text" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>