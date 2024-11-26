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
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_chung')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-8 col-xs-8">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'su_dung')} "reCAPTCHA v3"
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="use_recaptcha" value="1" {if !empty($recaptcha.use_recaptcha)}checked{/if}> {__d('admin', 'co')}
                                    <span></span>
                                </label>
                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="use_recaptcha" value="0" {if empty($recaptcha.use_recaptcha)}checked{/if}> {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                Site Key
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="site_key" value="{if !empty($recaptcha.site_key)}{$recaptcha.site_key}{/if}" class="form-control form-control-sm required" type="text">
                        </div>

                        <div class="form-group">
                            <label>
                                Secret Key
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="secret_key" value="{if !empty($recaptcha.secret_key)}{$recaptcha.secret_key}{/if}" class="form-control form-control-sm required" type="text">
                        </div>

                        <a href="https://www.google.com/recaptcha/admin/create?hl=en" target="_blank">
                            <i class="fa fa-info-circle fs-16"></i>
                            {__d('admin', 'tao_recaptcha_moi_tai_day')}
                        </a>
                    </div>
                </div>                
            </div>
        </div>
    </form>
</div>
