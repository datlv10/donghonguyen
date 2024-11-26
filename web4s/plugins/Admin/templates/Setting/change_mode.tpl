<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="kt-form">
                    <div class="kt_radio-list mt-20">
                        <div class="form-group row mb-20">
                            <div class="col-12">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="type" {if empty($website_mode.type) || (!empty($website_mode.value) && $website_mode.value eq {DEVELOP})}checked="checked"{/if} value="{DEVELOP}"> 
                                    Develope
                                    <span></span>
                                </label>

                                <span>
                                    {__d('admin', 'che_do_danh_cho_nha_phat_trien')}. 
                                    {__d('admin', 'o_che_do_nay_he_thong_se_vo_hieu_hoa_viec_tao_cache_html_va_bo_nen_tai_nguyen_duoc_tai')}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row mb-20">
                            <div class="col-12">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" {if !empty($website_mode.value) && $website_mode.value eq {LIVE}}checked="checked"{/if} name="type" value="{LIVE}"> 
                                    Live
                                    <span></span>
                                </label>

                                <span>
                                    {__d('admin', 'che_do_hoan_thien')}. 
                                    {__d('admin', 'giup_website_tang_toc_do_tai_va_hien_thi')}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
