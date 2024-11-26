{assign var = url_list value = "{ADMIN_PATH}/setting/attribute"}
{assign var = url_add value = "{ADMIN_PATH}/setting/attribute/add"}
{assign var = url_edit value = "{ADMIN_PATH}/setting/attribute/update"}


{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/attribute/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_thuoc_tinh')}
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
                                        {__d('admin', 'tieu_de')}
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

                                        <input name="name[{$k_lang}]"  value="{if !empty($attribute.ContentMutiple.{$k_lang})}{$attribute.ContentMutiple.{$k_lang}}{/if}" class="form-control form-control-sm required" type="text" maxlength="255">
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
                                {__d('admin', 'ma_thuoc_tinh')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-qrcode"></i>
                                    </span>
                                </div>
                                <input name="code" value="{if !empty($attribute.code)}{$attribute.code}{/if}" class="form-control form-control-sm" type="text" maxlength="20">                                
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'ma_thuoc_tinh_viet_thuong_va_khong_chua_ky_tu_dac_biet')}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'loai_thuoc_tinh')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {assign var = list_attribute_type value = $this->AttributeAdmin->getListType()}

                            {assign var = attribute_type value = ''}
                            {if !empty($attribute.attribute_type)}
                                {assign var = attribute_type value = $attribute.attribute_type}
                            {/if}

                            {if empty($attribute_type)}
                                {$this->Form->select('attribute_type', $list_attribute_type, ['empty' => "-- {__d('admin', 'loai_thuoc_tinh')} --", 'default' => "{$attribute_type}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            {else}
                                <p class="kt-font-bold">
                                    {if !empty($list_attribute_type[$attribute_type])}                                        
                                        <i>
                                            {$list_attribute_type[$attribute_type]}
                                        </i>
                                    {/if}
                                    <input type="hidden" name="attribute_type" value="{$attribute_type}">
                                </p>
                            {/if}
                        </div>
                    </div>                
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'loai_input')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {assign var = list_input_type value = $this->AttributeAdmin->getListTypeInput($attribute_type)}
                            {if empty($attribute.input_type)}
                                {$this->Form->select('input_type', $list_input_type, ['empty' => "-- {__d('admin', 'loai_input')} --", 'default' => "{if !empty($attribute.input_type)}{$attribute.input_type}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => 7])}
                            {else}
                                <p class="kt-font-bold">
                                    {if !empty($list_input_type[$attribute.input_type])}
                                        <i>
                                            {$list_input_type[$attribute.input_type]}
                                        </i>
                                    {/if}
                                    <input type="hidden" name="input_type" value="{if !empty($attribute.input_type)}{$attribute.input_type}{/if}">
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

                <div id="wrap-has-image" class="row {if empty($attribute.input_type) || $attribute.input_type != {SPECICAL_SELECT_ITEM}}d-none{/if}">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ap_dung_anh_dai_dien_cho_thuoc_tinh_nay')} ?
                            </label>
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="has_image" value="0" {if empty($attribute.has_image)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="has_image" value="1" {if !empty($attribute.has_image)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'tuy_chon_nay_chi_1_thuoc_tinh_duy_nhat_duoc_ap_dung')}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'bat_buoc')}
                            </label>
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="required" value="0" {if empty($attribute.required)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="required" value="1" {if !empty($attribute.required)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
