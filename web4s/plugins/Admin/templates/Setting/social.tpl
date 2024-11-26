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
                        {__d('admin', 'facebook')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Facebook App ID
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-facebook"></i>
                                    </span>
                                </div>
                                <input name="facebook_app_id" value="{if !empty($social.facebook_app_id)}{$social.facebook_app_id}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', '{0}_su_dung_de_cau_hinh_ham_khoi_tao_cho_cac_ung_dung_cua_facebook_nhung_vao_website', ['Facebook App ID'])}
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Facebook secret
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input name="facebook_secret" value="{if !empty($social.facebook_secret)}{$social.facebook_secret}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="form-group">
                    <label>
                        {__d('admin', 'tai_thu_vien_facebook')} (Facebook SDK)
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="facebook_load_sdk" value="1" {if !empty($social.facebook_load_sdk)}checked{/if}>
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="facebook_load_sdk" value="0" {if empty($social.facebook_load_sdk)}checked{/if}>
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'thoi_gian_cho_tai_thu_vien')}
                        (ms)
                    </label>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div id="facebook-time-delay" class="kt-nouislider--drag-danger mt-10"></div>
                        </div>

                        <div class="col-md-1 col-12">
                            {assign var = facebook_sdk_delay value = 0}

                            {if !isset($social.facebook_sdk_delay)}
                                {assign var = facebook_sdk_delay value = 2000}
                            {/if}

                            {if !empty($social.facebook_sdk_delay)}
                                {assign var = facebook_sdk_delay value = $social.facebook_sdk_delay}
                            {/if}
                            
                            <input name="facebook_sdk_delay" id="facebook-input" value="{$facebook_sdk_delay}" type="text" class="form-control" readonly="true">
                        </div>                            
                    </div>
                </div>

                <div class="form-group">
                    <i class="text-danger fs-12">
                        {__d('admin', 'luu_y')}:
                        {__d('admin', 'tuy_chinh_thoi_gian_tai_thu_vien_phu_hop_giup_website_cua_ban_rut_ngan_thoi_gian_tai_ban_dau')}
                    </i>
                </div>
            </div>
        </div>

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'google')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Google Client ID
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fab fa-google"></i>
                                    </span>
                                </div>
                                <input name="google_client_id" value="{if !empty($social.google_client_id)}{$social.google_client_id}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Google secret
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input name="google_secret" value="{if !empty($social.google_secret)}{$social.google_secret}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'tai_thu_vien_google')} (Google Platform)
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="google_load_sdk" value="1" {if !empty($social.google_load_sdk)}checked{/if}>
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="google_load_sdk" value="0" {if empty($social.google_load_sdk)}checked{/if}>
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'thoi_gian_cho_tai_thu_vien')} (ms)
                    </label>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div id="google-time-delay" class="kt-nouislider--drag-danger mt-10"></div>
                        </div>

                        <div class="col-md-1 col-12">
                            {assign var = google_sdk_delay value = 0}

                            {if !isset($social.google_sdk_delay)}
                                {assign var = google_sdk_delay value = 2000}
                            {/if}

                            {if !empty($social.google_sdk_delay)}
                                {assign var = google_sdk_delay value = $social.google_sdk_delay}
                            {/if}
                            
                            <input name="google_sdk_delay" id="google-input" value="{$google_sdk_delay}" type="text" class="form-control" readonly="true">
                        </div>                            
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>
