{assign var = url_list value = "{ADMIN_PATH}/template/block/list"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>

            <div class="btn-group">
                <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-plus"></i>
                    {__d('admin', 'them_moi')} (F1)
                </span>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/template/block/create" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'loai_block')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {$this->Form->select('type', $this->TemplateAdmin->getTypeBlockForDropdown(), ['id' => 'type', 'empty' => "{__d('admin', 'chon')}", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_block')}
                                <span class="kt-font-danger">*</span>
                            </label>

                            <input name="name" value="" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>
                </div>
                                
            </div>
        </div>
    </form>
</div>