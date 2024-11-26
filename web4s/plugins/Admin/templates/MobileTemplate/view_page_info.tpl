{assign var = list_languages value = $this->LanguageAdmin->getList()}

<input type="hidden" name="code" value="{if !empty($page_info.code)}{$page_info.code}{/if}">
{if !empty($page_info.code)}
    <div class="form-group row">
        <label class="col-xl-2 col-lg-3 col-form-label">
            {__d('admin', 'ma_trang')}
        </label>
        <div class="col-lg-4">
            <div class="mt-10">{$page_info.code}</div>
        </div>
    </div>
{/if}

<div class="form-group row">
    <label class="col-xl-2 col-lg-3 col-form-label">
        {__d('admin', 'loai_trang')}
        <span class="kt-font-danger">*</span>
    </label>

    <div class="col-lg-4">
        {$this->Form->select('type', $this->MobileTemplateAdmin->listTypePageTemplate(), ['id'=> 'type', 'empty' => null, 'default' => "{if !empty($page_info.type)}{$page_info.type}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
    </div>
</div>

<div class="form-group row">
    <label class="col-xl-2 col-lg-3 col-form-label">
        {__d('admin', 'ten_trang')}
        <span class="kt-font-danger">*</span>
    </label>

    <div class="col-lg-8">
        <input name="name" value="{if !empty($page_info.name)}{$page_info.name}{/if}" type="text" class="form-control form-control-sm">
    </div>
</div>

<div id="wrap-link" class="clearfix">
    {if !empty($list_languages)}
        {foreach from = $list_languages item = language key = k_lang}
            <div class="form-group row">
                <label class="col-xl-2 col-lg-3 col-form-label">
                    {__d('admin', 'tieu_de_trang')}
                    ({$language})
                    <span class="kt-font-danger">*</span>
                </label>

                <div class="col-lg-8">
                    <div class="input-group">
                        <input name="config[{$k_lang}]" value="{if !empty($page_info.config.page_title.$k_lang)}{$page_info.config.page_title.$k_lang}{/if}" class="form-control form-control-sm" type="text">

                        <div class="input-group-append">
                            <span class="input-group-text">
                                <div class="list-flags">
                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                </div>
                            </span>
                        </div>
                    </div>
                </div>            
            </div>
        {/foreach}
    {/if}
</div>

<div id="wrap-category" class="clearfix">
    {$this->element('../Template/load_dropdown_category', ['category_id' => "{if !empty($page_info.category_id)}{$page_info.category_id}{/if}", 'type_category' => $type_category, 'language' => $lang])}
</div>