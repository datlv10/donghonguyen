{assign var = url_list value = "{ADMIN_PATH}/setting/attribute/option/{$attribute_id}"}
{assign var = url_add value = "{ADMIN_PATH}/setting/attribute/option/add/{$attribute_id}"}
{assign var = url_edit value = "{ADMIN_PATH}/setting/attribute/option/update/{$attribute_id}"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/attribute/option/save/{$attribute_id}{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_tuy_chon')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                {if !empty($list_languages)}
                    {foreach from = $list_languages item = language key = k_lang name = title_item}
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ten_tuy_chon')}
                                        ({$language})
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <div class="list-flags">
                                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                                </div>
                                            </span>
                                        </div>

                                        <input name="name[{$k_lang}]"  value="{if !empty($option.ContentMutiple.{$k_lang})}{$option.ContentMutiple.{$k_lang}}{/if}" class="form-control form-control-sm required" type="text" maxlength="255">
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_tuy_chon')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-qrcode"></i>
                                    </span>
                                </div>
                                <input name="code" value="{if !empty($option.code)}{$option.code}{/if}" class="form-control form-control-sm" type="text" maxlength="20">                                
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'ma_thuoc_tinh_viet_lien_khong_dau')}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
