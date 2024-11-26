{if !empty($article)}
    {if !empty($article.image_avatar)}
        {assign var = url_img value = "{$article.image_avatar}"}
    {else}
        {assign var = url_img value = "{ADMIN_PATH}{NO_IMAGE_URL}"}
    {/if}
    {assign var = url_list value = "{ADMIN_PATH}/article"}
    

    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-sm btn-default">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
            <div class="kt-form kt-form--label-right">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'ngon_ngu')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        <div class="list-flags">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag" />
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'trang_thai')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    {if !empty($article.status)}
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'hoat_dong')}
                                        </span>
                                    {else}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'khong_hoat_dong')}
                                        </span>    
                                    {/if}
                                </div>
                            </div>
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'ngay_tao')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.created)}
                                            {date("H:i - d/m/Y", $article.created)}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'cap_nhat')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.updated)}
                                            {date("H:i - d/m/Y", $article.updated)}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'seo')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    {if !empty($article.seo_score) && $article.seo_score == 'success'}
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'tot')}
                                        </span>
                                    {elseif !empty($article.seo_score) && $article.seo_score == 'warning'}
                                        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'binh_thuong')}
                                        </span>
                                    {elseif !empty($article.seo_score) && $article.seo_score == 'danger'}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'chua_dat')}
                                        </span>
                                    {else}
                                        <span class="form-control-plaintext kt-font-bolder">...</span>
                                    {/if}
                                </div>
                            </div>
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'tu_khoa')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    {if !empty($article.keyword_score) && $article.keyword_score == 'success'}
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'tot')}
                                        </span>
                                    {elseif !empty($article.keyword_score) && $article.keyword_score == 'warning'}
                                        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'binh_thuong')}
                                        </span>
                                    {elseif !empty($article.keyword_score) && $article.keyword_score == 'danger'}
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'chua_dat')}
                                        </span>
                                    {else}
                                        <span class="form-control-plaintext kt-font-bolder">...</span>
                                    {/if}
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'tieu_de')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.ArticlesContent.name)}
                                            {$article.ArticlesContent.name}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'duong_dan')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.Links.url)}
                                            {$article.Links.url}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'vi_tri')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.position)}
                                            {$article.position}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'bai_noi_bat')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($article.featured)}{__d('admin', 'co')}
                                    {else}
                                        {__d('admin', 'khong')}{/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">{__d('admin', 'danh_muc')}</label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder wrp-comma">
                                        {if !empty($categories_id)}
                                            {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForCheckboxList([
                                                {TYPE} => {ARTICLE}, 
                                                {LANG} => $lang
                                            ])}

                                            {foreach from = $categories_id key = category_id item = item}
                                                {if !empty($list_categories[$category_id])}
                                                    {$list_categories[$category_id]['name']}<span class="comma-item">, </span>
                                                {/if}
                                            {/foreach}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            {if !empty($article.image_avatar)}
                                <div class="form-group form-group-xs row mb-10">
                                    <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'anh_chinh')}</label>
                                    <div class="col-xl-9 col-lg-8">
                                        <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                            <div class="kt-avatar__holder" style="background-image: url('{CDN_URL}{$url_img}');background-size: contain;background-position: 50% 50%;"></div>
                                        </div>
                                    </div>
                                </div>
                            {/if}

                            {if !empty($article.images)}
                                <div class="form-group form-group-xs row mb-10">
                                    <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'album')}</label>
                                    <div class="col-xl-9 col-lg-8">
                                        <div class="kt-section__content d-flex flex-wrap kt-section__content--solid--">
                                            {assign var = images value = $article.images|json_decode:1}
                                            {foreach from = $images item = image}
                                                <span class="kt-media kt-margin-r-5 kt-margin-t-5">
                                                    <img src="{CDN_URL}{$image}" alt="image" style="width: auto !important;">
                                                </span>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            {/if}

                            {if !empty($article.url_video)}
                                <div class="form-group form-group-xs row mb-10">
                                    <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'video')}</label>
                                    <div class="col-xl-9 col-lg-8">
                                        <div class="list-files">
                                            <a href="{$article.url_video}" class="kt-media kt-media--lg kt-margin-r-5 position-relative item-file"  target="_blank">
                                                <i class="fa fa fa-file-video"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {/if}

                            {if !empty($article.files)}
                                <div class="form-group form-group-xs row mb-10">
                                    <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'tep_dinh_kem')}</label>
                                    <div class="col-xl-9 col-lg-8">
                                        <div class="list-files">
                                            {assign var = files value = $article.files|json_decode:1}
                                            {foreach from = $files item = file}
                                                <a href="{$file}" class="kt-media kt-media--lg mr-5 position-relative item-file" target="_blank">
                                                    {assign var = file_type value = {$this->UtilitiesAdmin->getTypeFileByUrl($file)}}
                                                    <i class="fa fa-file{if !empty($file_type)}-{$file_type}{/if}"></i>
                                                </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>

                    <div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mb-10 mt-10"></div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'the_bai_viet')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">{if !empty($article.ArticlesContent.tags)}{$article.ArticlesContent.tags}{else}...{/if}</span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'tu_khoa_seo')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">{if !empty($article.ArticlesContent.seo_keyword)}{$article.ArticlesContent.seo_keyword}{else}...{/if}</span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'tieu_de_seo')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">{if !empty($article.ArticlesContent.seo_title)}{$article.ArticlesContent.seo_title}{else}...{/if}</span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'mo_ta_seo')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">{if !empty($article.ArticlesContent.seo_description)}{$article.ArticlesContent.seo_description}{else}...{/if}</span>
                                </div>
                            </div>

                            <div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mb-10 mt-10"></div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'mo_ta_ngan')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext">{if !empty($article.ArticlesContent.description)}{$article.ArticlesContent.description}{else}<span class="kt-font-bolder">...</span>{/if}</span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">{__d('admin', 'noi_dung')}</label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext">{if !empty($article.ArticlesContent.content)}{$article.ArticlesContent.content}{else}<span class="kt-font-bolder">...</span>{/if}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}</span>
{/if}

