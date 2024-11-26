{if !empty($role)}
    {assign var = url_list value = "{ADMIN_PATH}/role"}

    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-default btn-bold">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
            <div class="kt-form kt-form--label-right">
                <div class="kt-portlet__body">
                    <div class="form-group form-group-xs row">
                        <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'nhom_quyen')}</label>
                        <div class="col-xl-9 col-lg-8">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($role.name)}
                                    {$role.name}
                                {else}
                                    ...
                                {/if}
                            </span>
                        </div>
                    </div>
                    <div class="form-group form-group-xs row">
                        <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'mo_ta_ngan')}</label>
                        <div class="col-xl-9 col-lg-8">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($role.short_description)}
                                    {$role.short_description}
                                {else}
                                    ...
                                {/if}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}</span>
{/if}