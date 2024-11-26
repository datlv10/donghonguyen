{if !empty($category)}
    {if !empty($category.image_avatar)}
        {assign var = url_img value = "{CDN_URL}{$category.image_avatar}"}
    {else}
        {assign var = url_img value = "{ADMIN_PATH}{NO_IMAGE_URL}"}
    {/if}

    {assign var = url_list value = "{ADMIN_PATH}/category/{$type}"}

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
                {$this->element('Admin.page/language')}
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-wizard-v4">
            <div class="kt-portlet">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid">
                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v4__wrapper">
                            <div class="kt-form" style="width: 95%">
                                <div class="kt-wizard-v4__content">
                                    <div class="kt-heading kt-heading--md">
                                        {__d('admin', 'danh_muc')}:
                                        {if !empty($category.name)}
                                            {$category.name}
                                        {/if}
                                        {if !empty($category.url)}
                                            <small>
                                                <a target="_blank" href="/{$category.url}" class="kt-link kt-font-bolder kt-link--info">
                                                    ({__d('admin', 'xem_danh_muc')})
                                                </a>
                                            </small>

                                        {/if}
                                    </div>
                                    <div class="kt-form__section kt-form__section--first">
                                        <div class="kt-wizard-v4__review entire-detail">
                                            <div class="kt-wizard-v4__review-item">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                            {__d('admin', 'thong_tin_cap_nhat')}
                                                        </div>
                                                        <div class="kt-wizard-v4__review-content">
                                                            <p class="mb-5">
                                                                {__d('admin', 'ngon_ngu')}: 
                                                                <span class="kt-font-bolder">
                                                                    <span class="list-flags">
                                                                        <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag" />
                                                                    </span>
                                                                </span>
                                                            </p>

                                                            <p class="mb-5">
                                                                {__d('admin', 'trang_thai')}:
                                                                {if isset($category.status) && $category.status == 1}
                                                                    <span class="text-success kt-font-bolder">
                                                                        {__d('admin', 'hoat_dong')}
                                                                    </span>
                                                                {elseif isset($category.status) && $category.status == 0}
                                                                    <span class="text-danger kt-font-bolder">
                                                                        {__d('admin', 'khong_hoat_dong')}
                                                                    </span>
                                                                {/if}
                                                            </p>

                                                            <p class="mb-5">
                                                                {__d('admin', 'ngay_tao')}: 
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.created)}
                                                                        {$category.created}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>
                                                            
                                                            <p class="mb-5">
                                                                {__d('admin', 'cap_nhat_moi')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.updated)}
                                                                        {$category.updated}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>

                                                            <p class="mb-5">
                                                                {__d('admin', 'nguoi_tao')}: 
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.created_by_user)}
                                                                        {$category.created_by_user}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                            {__d('admin', 'thong_tin_co_ban')}
                                                        </div>
                                                        <div class="kt-wizard-v4__review-content">
                                                            <p class="mb-5">
                                                                {__d('admin', 'duong_dan')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.url)}
                                                                        {$category.url}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>

                                                            {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                                                                {TYPE} => $type, 
                                                                {LANG} => $lang,
                                                                {NOT_ID} => "{if !empty($category.id)}{$category.id}{/if}"
                                                            ])}
                                                            {if !empty($category.parent_id) && !empty($list_categories[$category.parent_id])}
                                                                <p class="mb-5">
                                                                    {__d('admin', 'danh_muc_cha')}:
                                                                    <span class="kt-font-bolder">
                                                                        {$list_categories[$category.parent_id]}
                                                                    </span>
                                                                </p>
                                                            {/if}

                                                            <p class="mb-5">
                                                                {__d('admin', 'vi_tri')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.position)}
                                                                        {$category.position}
                                                                    {else}
                                                                        0
                                                                    {/if}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                            {__d('admin', 'thong_tin_seo')}
                                                        </div>
                                                        <div class="kt-wizard-v4__review-content">
                                                            <p class="mb-5">
                                                                {__d('admin', 'tieu_de_seo')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.seo_title)}
                                                                        {$category.seo_title}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>

                                                            <p class="mb-5">
                                                                {__d('admin', 'mo_ta_seo')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.seo_description)}
                                                                        {$category.seo_description}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>

                                                            <p class="mb-5">
                                                                {__d('admin', 'tu_khoa_seo')}:
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($category.seo_keyword)}
                                                                        {$category.seo_keyword}
                                                                    {else}
                                                                        ...
                                                                    {/if}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'da_phuong_tien')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">
                                                    <p class="mb-5">
                                                        {__d('admin', 'anh_dai_dien')}: 
                                                        <div class="d-flex flex-wrap kt-margin-t-10">
                                                            <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="image-avatar" href="{$url_img}">
                                                                <img class="img-cover" src="{$url_img}" alt="image-avatar" style="width: 80px;">
                                                            </a>
                                                        </div>
                                                    </p>

                                                    {if !empty($category.images)}
                                                        <p class="mb-5">
                                                            {__d('admin', 'anh_bai_viet')}: 
                                                            <div class="d-flex flex-wrap kt-margin-t-10">
                                                                {foreach from = $category.images item = image_item}
                                                                    <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="album-{$category.id}" href="{CDN_URL}{$image_item}">
                                                                        <img class="img-cover" src="{CDN_URL}{$image_item}" alt="album" style="width: 80px;">
                                                                    </a>
                                                                {/foreach}
                                                            </div>
                                                        </p>
                                                    {/if}

                                                    {if !empty($category.url_video) && $category.type_video == {VIDEO_SYSTEM}}
                                                        <p class="mb-5">
                                                            {__d('admin', 'video')}:
                                                            <span class="list-files d-inline-block">
                                                                <a href="{CDN_URL}{$category.url_video}" class="kt-media kt-media--lg kt-margin-r-5 position-relative item-file" target="_blank">
                                                                    <i class="fa fa fa-file-video"></i>
                                                                </a>
                                                            </span>
                                                        </p>
                                                    {/if}

                                                    {if !empty($category.url_video) && $category.type_video == {VIDEO_YOUTUBE}}
                                                        <p class="mb-5">
                                                            {__d('admin', 'video')}:
                                                            <span class="list-files d-inline-block">
                                                                <a href="https://www.youtube.com/watch?v={$category.url_video}" class="kt-media kt-media--lg kt-margin-r-5 position-relative item-file"  target="_blank">
                                                                    <i class="fa fa fa-file-video"></i>
                                                                </a>
                                                            </span>
                                                        </p>
                                                    {/if}
                                                </div>
                                            </div>
                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'thong_tin_bai_viet')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">
                                                    <p class="mb-5">
                                                        <span class="kt-font-bolder">
                                                            {__d('admin', 'mo_ta_ngan')}:
                                                        </span>
                                                        {if !empty($category.description)}
                                                            {$category.description}
                                                        {else}
                                                            ...
                                                        {/if}
                                                    </p>
                                                    <p class="mb-5">
                                                        <span class="kt-font-bolder">
                                                            {__d('admin', 'noi_dung')}:
                                                        </span>
                                                        {if !empty($category.content)}
                                                            {$category.content}
                                                        {else}
                                                            ...
                                                        {/if}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_tim_thay_thong_tin_danh_muc')}</span>
{/if}