{assign var = url_list value = "{ADMIN_PATH}/currency"}
{assign var = url_add value = "{ADMIN_PATH}/currency/add"}
{assign var = url_edit value = "{ADMIN_PATH}/currency/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/currency/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">

        <div class="row">
            <div class="col-lg-12 col-xs-12">

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_chinh')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ten_tien_te')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <input name="name" value="{if !empty($currency.name)}{$currency.name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                </div>
                            </div>
                        </div>
                        
                        {if empty($currency.code)}
                            <div class="row">
                                <div class="col-xl-2 col-lg-3">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'ma_tien_te')}
                                            <span class="kt-font-danger">*</span>
                                        </label>
                                        <input name="code" value="{if !empty($currency.code)}{$currency.code}{/if}" class="form-control form-control-sm" type="text" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        {else}
                            <input name="code" value="{if !empty($currency.code)}{$currency.code}{/if}" type="hidden">
                        {/if}

                        <div class="row">
                            <div class="col-xl-2 col-lg-3">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'don_vi')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <input name="unit" value="{if !empty($currency.unit)}{$currency.unit}{/if}" class="form-control form-control-sm" type="text" maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-2 col-lg-3">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ti_le_chuyen_doi')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <input name="exchange_rate" value="{if !empty($currency.exchange_rate)}{$currency.exchange_rate}{/if}" class="form-control form-control-sm" type="text">
                                    <span class="form-text text-muted">
                                        {__d('admin', 'ti_le_chuyen_doi_cua_tien_te_hien_tai_so_voi_tien_te_mac_dinh')}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>