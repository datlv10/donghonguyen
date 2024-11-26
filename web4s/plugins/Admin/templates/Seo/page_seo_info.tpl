{assign var = list_language value = $this->LanguageAdmin->getList()}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <div class="btn-group">
                <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'cap_nhat')} (F1)
                </button>
            </div>
        </div>

    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    {if !empty($list_pages)}        
        <form id="main-form" action="{ADMIN_PATH}/page-seo-info/save" method="POST" autocomplete="off">
            {assign var = index value = 0}
            {foreach from = $list_pages key = key item = item}
                <div class="kt-portlet kt-portlet--mobile mb-10 nh-template-portlet wrap-item kt-portlet--collapse">
                    <div class="kt-portlet__head p-5" style="min-height: 40px;">
                        <div class="kt-portlet__head-label ml-5">
                            <h3 class="kt-portlet__head-title">
                                {if !empty($item.name)}
                                    {$item.name}
                                {/if}
                            </h3>
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group">                        
                                <span class="btn btn-sm btn-icon btn-info btn-icon-md m-0 btn-toggle-item">
                                    <i class="la la-angle-down"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__body p-10" style="{if !empty($item)}display: none;{/if}">
                        <div class="row">
                            {foreach from = $list_language item = language key = k_lang}
                                {assign var = content value = ''}
                                {if !empty($item.content.{$k_lang})}
                                    {assign var = content value = $item.content.{$k_lang}}
                                {/if}
                                <div class="col-md-6 col-12">
                                    <div class="kt-widget24 kt-widget24--solid mb-10">
                                        <div class="form-group">
                                            <label>
                                                {__d('admin', 'tieu_de_seo')}
                                                ({$language})
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="la la-list-alt"></i>
                                                    </span>
                                                </div>

                                                <input name="{$index}[seo_title]" value="{if !empty($content.seo_title)}{$content.seo_title}{/if}" type="text" class="form-control form-control-sm" maxlength="255">

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <div class="list-flags">
                                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                {__d('admin', 'mo_ta_seo')}
                                                ({$language})
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="la la-file-text"></i>
                                                    </span>
                                                </div>

                                                <input name="{$index}[seo_description]" value="{if !empty($content.seo_description)}{$content.seo_description}{/if}" type="text" class="form-control form-control-sm" maxlength="255">

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <div class="list-flags">
                                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                {__d('admin', 'tu_khoa_seo')}
                                                ({$language})
                                            </label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="la la-tags"></i>
                                                    </span>
                                                </div>
                                                
                                                <input name="{$index}[seo_keyword]" value="{if !empty($content.seo_keyword)}{$content.seo_keyword}{/if}" type="text" class="form-control form-control-sm tagify-input">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <div class="list-flags">
                                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                {__d('admin', 'anh_seo')}
                                            </label>

                                            <div class="clearfix">
                                                {assign var = bg_avatar value = ''}
                                                {if !empty($content.seo_image)}
                                                    {assign var = bg_avatar value = "background-image: url('{CDN_URL}{$content.seo_image}');background-size: contain;background-position: 50% 50%;"}
                                                {/if}

                                                {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_avatar_{$index}{if !empty($content.id)}{$content.id}{/if}"}

                                                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_avatar)}kt-avatar--changed{/if}">
                                                    <a {if !empty($content.seo_image)}href="{CDN_URL}{$content.seo_image}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_avatar}"></a>
                                                    <label data-src="{$url_select_image}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                                        <i class="fa fa-pen"></i>
                                                    </label>
                                                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                                        <i class="fa fa-times"></i>
                                                    </span>

                                                    <input id="image_avatar_{$index}{if !empty($content.id)}{$content.id}{/if}" name="{$index}[seo_image]" value="{if !empty($content.seo_image)}{htmlentities($content.seo_image)}{/if}" type="hidden" />
                                                </div>
                                            </div>
                                        </div>
                                        <input name="{$index}[template_code]" value="{if !empty($item.template_code)}{$item.template_code}{/if}" type="hidden" />
                                        <input name="{$index}[page_code]" value="{if !empty($item.code)}{$item.code}{/if}" type="hidden" />
                                        <input name="{$index}[lang]" value="{if !empty($k_lang)}{$k_lang}{/if}" type="hidden" />
                                        <input name="{$index}[id]" value="{if !empty($content.id)}{$content.id}{/if}" type="hidden" />
                                    </div>
                                </div>
                                {$index = $index + 1}
                            {/foreach}
                        </div>
                    </div>
                </div>
            {/foreach}
        </form>
    {/if}
</div>