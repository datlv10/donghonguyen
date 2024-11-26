{assign var = url_list value = "{ADMIN_PATH}/redirect-301"}
{assign var = url_add value = "{ADMIN_PATH}/redirect-301/add"}
{assign var = url_edit value = "{ADMIN_PATH}/redirect-301/update"}


{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/redirect-301/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
         {if !empty($redirect.id)}
            <div nh-anchor="thong_tin_cap_nhat" class="kt-portlet nh-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'thong_tin_cap_nhat')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-xs-6">
                            <div class="form-group form-group-xs row">
                                <label class="col-lg-4 col-xl-4 col-form-label">
                                    {__d('admin', 'trang_thai')}
                                </label>
                                <div class="col-lg-8 col-xl-8">
                                    {if isset($redirect.status) && $redirect.status == 1}
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'hoat_dong')}
                                        </span>
                                    {elseif isset($redirect.status) && $redirect.status == 0}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'khong_hoat_dong')}
                                        </span>   
                                    {/if}
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-lg-4 col-xl-4 col-form-label">
                                    {__d('admin', 'nguoi_tao')}
                                </label>
                                <div class="col-lg-8 col-xl-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($redirect.User.full_name)}
                                            {$redirect.User.full_name}
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-lg-4 col-xl-4 col-form-label">
                                    {__d('admin', 'thoi_gian_tao')}
                                </label>
                                <div class="col-lg-8 col-xl-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($redirect.created)}
                                            {date('H:i - d/m/Y', $redirect.created)}
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-lg-4 col-xl-4 col-form-label">
                                    {__d('admin', 'cap_nhat_moi')}
                                </label>
                                <div class="col-lg-8 col-xl-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($redirect.updated)}
                                            {date('H:i - d/m/Y', $redirect.updated)}
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'duong_dan_cu')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text w-auto">
                                {$this->Utilities->getUrlWebsite()}
                            </span>
                        </div>
                        
                        <input name="url" value="{if !empty($redirect.url)}{$redirect.url}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
                    </div>

                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'duong_dan_moi')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text w-auto">
                                {$this->Utilities->getUrlWebsite()}
                            </span>
                        </div>

                        <input name="redirect" value="{if !empty($redirect.redirect)}{$redirect.redirect}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
