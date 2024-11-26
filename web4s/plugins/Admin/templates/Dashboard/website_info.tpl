<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <a href="{ADMIN_PATH}/setting/website-info" class="kt-portlet__head-title">
            {__d('admin', 'thong_tin_website')}
        </a>
    </div>
</div>

<div class="kt-form kt-form--label-right">
    <div class="kt-portlet__body">
        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'ten_website')}:
            </label>

            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.website_name)}
                        {$website_info.website_name}
                    {/if}
                </span>
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'ten_cong_ty')}:
            </label>

            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.company_name)}
                        {$website_info.company_name}
                    {/if}
                </span>
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'hotline')}:
            </label>
            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.hotline)}
                        {$website_info.hotline}
                    {/if}
                </span>
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'so_dien_thoai')}:
            </label>

            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.phone)}
                        {$website_info.phone}
                    {/if}
                </span>
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'email')}:
            </label>

            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.email)}
                        {$website_info.email}
                    {/if}
                </span>
            </div>
        </div>

        <div class="form-group form-group-xs row">
            <label class="col-4 col-xl-3 col-form-label">
                {__d('admin', 'dia_chi')}:
            </label>

            <div class="col-8 col-xl-9">
                <span class="form-control-plaintext kt-font-bolder">
                    {if !empty($website_info.address)}
                        {$website_info.address}
                    {/if}
                </span>
            </div>
        </div>
    </div>
</div>