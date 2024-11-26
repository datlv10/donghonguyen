<div class="kt-subheader   kt-grid__item" id="kt_subheader">
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
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_chung')}
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-4 col-xl-4 col-sm-4 col-6">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-language" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/language" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_ngon_ngu')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_danh_sach_ngon_ngu_he_thong')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'dich_tu_dong')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'dich_tu_dong_khi_tao_moi_bai_viet')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="auto_translate"  value="1" {if !empty($setting.auto_translate)}checked{/if}> 
                                {__d('admin', 'hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="auto_translate" value="0" {if !isset($setting.auto_translate) || empty($setting.auto_translate)}checked{/if}> 
                                {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>