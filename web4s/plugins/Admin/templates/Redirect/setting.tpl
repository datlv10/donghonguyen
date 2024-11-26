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
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'chuyen_huong_301')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <form id="redirect-form" action="{ADMIN_PATH}/setting/save/redirect_301" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chuyen_huong_301')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="redirect_301" value="1" {if !empty($redirect.redirect_301)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="redirect_301" value="0" {if empty($redirect.redirect_301)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'chuyen_huong_https')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="redirect_https" value="1" {if !empty($redirect.redirect_https)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="redirect_https" value="0" {if empty($redirect.redirect_https)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

                        <div class="row">
                            <div class="col-12">
                                <button id="btn-save-redirect" type="button" class="btn btn-brand btn-sm">
                                    {__d('admin', 'luu_cau_hinh')}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'chuyen_huong_trang_error')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <form id="redirect-page-error" action="{ADMIN_PATH}/setting/save/redirect_page_error" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chuyen_huong_trang_error')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="redirect_page_error" nh-redirect-error value="1" {if !empty($redirect_page_error.redirect_page_error)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="redirect_page_error" nh-redirect-error value="0" {if empty($redirect_page_error.redirect_page_error)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>                    
                        <div class="form-group">
                            <label>
                                {__d('admin', 'trang_dich')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="redirect_page_type" {if empty($redirect_page_error.redirect_page_error)}disabled="disabled"{/if} value="home" {if !empty($redirect_page_error.redirect_page_type) && $redirect_page_error.redirect_page_type == 'home'}checked{/if}> 
                                    {__d('admin', 'trang_chu')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="redirect_page_type" {if empty($redirect_page_error.redirect_page_error)}disabled="disabled"{/if} value="404" {if empty($redirect_page_error.redirect_page_type) || (!empty($redirect_page_error.redirect_page_type) && $redirect_page_error.redirect_page_type == '404')}checked{/if}> 
                                    {__d('admin', 'trang_404')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

                        <div class="row">
                            <div class="col-12">
                                <button id="btn-save-redirect-page-error" type="button" class="btn btn-brand btn-sm">
                                    {__d('admin', 'luu_cau_hinh')}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>