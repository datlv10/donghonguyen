{if !empty($product)}
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}
                        {$title_for_layout}
                    {/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{ADMIN_PATH}/product" class="btn btn-default btn-sm">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>

                <a href="{ADMIN_PATH}/product/add" class="btn btn-brand btn-sm">
                    {__d('admin', 'cap_nhat_san_pham')}
                </a>

                {$this->element('Admin.page/language')}
            </div>
        </div>
    </div>

    <div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-wizard-v4">
            <div class="kt-portlet">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid">
                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v4__wrapper">
                            <div class="kt-form p-20" style="width: 100%">
                                <div class="kt-wizard-v4__content">
                                    <div class="kt-heading kt-heading--md mt-10">
                                        {__d('admin', 'san_pham')}:
                                        
                                        {if !empty($product.name)}
                                            {$product.name} 
                                        {/if}

                                        {if !empty($product.url)}
                                            <small>
                                                <a target="_blank" href="/{$product.url}" class="kt-link kt-font-bolder kt-link--info">
                                                    ({__d('admin', 'xem_san_pham')})
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
                                                            <div class="kt-section">
                                                                <div class="kt-section__content kt-section__content--solid">
                                                                    <p class="mb-10">
                                                                        {__d('admin', 'ngon_ngu')}: 
                                                                        <span class="kt-font-bolder">
                                                                            <span class="list-flags">
                                                                                <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag" />
                                                                            </span>
                                                                        </span>
                                                                    </p>

                                                                    {if !empty($product.draft)}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'trang_thai')}:
                                                                            <span class="text-dark kt-font-bolder">
                                                                                {__d('admin', 'ban_luu_nhap')}
                                                                            </span>    
                                                                        </p>
                                                                    {else}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'trang_thai')}:
                                                                            {if isset($product.status) && $product.status == 1}
                                                                                <span class="text-success kt-font-bolder">
                                                                                    {__d('admin', 'hien_thi')}
                                                                                </span>
                                                                            {elseif ($product.draft == 1) || (isset($product.status) && $product.status == 0)}
                                                                                <span class="text-danger kt-font-bolder">
                                                                                    {__d('admin', 'khong_hien_thi')}
                                                                                </span>
                                                                            {elseif isset($product.status) && $product.status == -1}
                                                                                <span class="text-warning kt-font-bolder">
                                                                                    {__d('admin', 'cho_duyet')}
                                                                                </span>
                                                                            {elseif isset($product.status) && $product.status == 2}
                                                                                <span class="text-warning kt-font-bolder">
                                                                                    {__d('admin', 'ngung_kinh_doanh')}
                                                                                </span>
                                                                            {/if}

                                                                        </p>
                                                                    {/if}

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'ngay_tao')}: 
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.created)}
                                                                                {$product.created}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'nguoi_tao')}: 
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.created_by_user)}
                                                                                {$product.created_by_user}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'cap_nhat_moi')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.updated)}
                                                                                {$product.updated}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'so_luot_xem')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.view)}
                                                                                {$product.view}
                                                                            {else}
                                                                                0
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'so_luong_binh_luan')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.comment)}
                                                                                {$product.comment}
                                                                            {else}
                                                                                0
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    {if !empty($product.rating)}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'diem_danh_gia')}:
                                                                            <span class="kt-font-bolder">
                                                                                {$product.rating}
                                                                            </span>
                                                                        </p>
                                                                    {/if}

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'so_luong_danh_gia')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.rating_number)}
                                                                                {$product.rating_number}
                                                                            {else}
                                                                                0
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    {if !empty($qrcode_url)}
                                                                        <p class="mb-10">
                                                                            <img src="{$qrcode_url}" width="300px">
                                                                        </p>
                                                                    {/if}
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                                                                    
                                                    <div class="col-md-4">
                                                        <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                            {__d('admin', 'thong_tin_co_ban')}
                                                        </div>

                                                        <div class="kt-wizard-v4__review-content">
                                                            <div class="kt-section">
                                                                <div class="kt-section__content kt-section__content--solid">
                                                                    <p class="mb-10">
                                                                        {__d('admin', 'duong_dan')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.url)}
                                                                                {$product.url}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'san_pham_noi_bat')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.featured)}
                                                                                {__d('admin', 'co')}
                                                                            {else}
                                                                                {__d('admin', 'khong')}
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'hien_thi_muc_luc')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.catalogue)}
                                                                                {__d('admin', 'co')}
                                                                            {else}
                                                                                {__d('admin', 'khong')}
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'danh_muc')}:
                                                                        <span class="kt-font-bolder wrp-comma">
                                                                            {if !empty($product.categories)}
                                                                                {foreach from = $product.categories key = category_id item = category}
                                                                                    {$category.name}<span class="comma-item">, </span>
                                                                                {/foreach}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    {assign var = list_brands value = $this->BrandAdmin->getListBrands()}
                                                                    {if !empty($product.brand_id)}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'thuong_hieu')}:
                                                                            <span class="kt-font-bolder">
                                                                                {$list_brands[$product.brand_id]}
                                                                            </span>
                                                                        </p>
                                                                    {/if}

                                                                    {if !empty($product.url_video) && $product.type_video == {VIDEO_SYSTEM}}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'video')}:
                                                                            <span class="list-files d-inline-block">
                                                                                <a href="{CDN_URL}{$product.url_video}" class="kt-media kt-media--lg kt-margin-r-5 position-relative item-file"  target="_blank">
                                                                                    <i class="fa fa fa-file-video"></i>
                                                                                </a>
                                                                            </span>
                                                                        </p>
                                                                    {/if}

                                                                    {if !empty($product.url_video) && $product.type_video == {VIDEO_YOUTUBE}}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'video')}:
                                                                            <span class="list-files d-inline-block">
                                                                                <a href="https://www.youtube.com/watch?v={$product.url_video}" class="kt-media kt-media--lg kt-margin-r-5 position-relative item-file"  target="_blank">
                                                                                    <i class="fa fa fa-file-video"></i>
                                                                                </a>
                                                                            </span>
                                                                        </p>
                                                                    {/if}


                                                                    {if !empty($product.files)}
                                                                        <p class="mb-10">
                                                                            {__d('admin', 'tep_dinh_kem')}:
                                                                            <span class="list-files d-inline-block">
                                                                                {foreach from = $product.files item = file}
                                                                                    <a href="{CDN_URL}{$file}" class="kt-media kt-media--lg position-relative item-file" target="_blank">
                                                                                        {assign var = file_type value = {$this->UtilitiesAdmin->getTypeFileByUrl($file)}}
                                                                                        <i class="fa fa-file{if !empty($file_type)}-{$file_type}{/if}"></i>
                                                                                    </a>
                                                                                {/foreach}
                                                                            </span>
                                                                        </p>
                                                                    {/if}

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'vi_tri')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.position)}
                                                                                {$product.position}
                                                                            {else}
                                                                                0
                                                                            {/if}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                            {__d('admin', 'thong_tin_seo')}
                                                        </div>

                                                        <div class="kt-wizard-v4__review-content">
                                                            <div class="kt-section">
                                                                <div class="kt-section__content kt-section__content--solid">
                                                                    <p class="mb-10">
                                                                        {__d('admin', 'diem_seo')}:
                                                                        {if !empty($product.seo_score) && $product.seo_score == 'success'}
                                                                            <span class="text-success kt-font-bolder">
                                                                                {__d('admin', 'tot')}
                                                                            </span>
                                                                        {elseif !empty($product.seo_score) && $product.seo_score == 'warning'}
                                                                            <span class="text-warning kt-font-bolder">
                                                                                {__d('admin', 'binh_thuong')}
                                                                            </span>
                                                                        {elseif !empty($product.seo_score) && $product.seo_score == 'danger'}
                                                                            <span class="text-danger kt-font-bolder">
                                                                                {__d('admin', 'chua_dat')}
                                                                            </span>
                                                                        {else}
                                                                            <span class="kt-font-bolder kt-font-bolder">...</span>
                                                                        {/if}
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'diem_tu_khoa')}:
                                                                         {if !empty($product.keyword_score) && $product.keyword_score == 'success'}
                                                                            <span class="text-success kt-font-bolder">
                                                                                {__d('admin', 'tot')}
                                                                            </span>
                                                                        {elseif !empty($product.keyword_score) && $product.keyword_score == 'warning'}
                                                                            <span class="text-warning kt-font-bolder">
                                                                                {__d('admin', 'binh_thuong')}
                                                                            </span>
                                                                        {elseif !empty($product.keyword_score) && $product.keyword_score == 'danger'}
                                                                            <span class="text-danger kt-font-bolder">
                                                                                {__d('admin', 'chua_dat')}
                                                                            </span>
                                                                        {else}
                                                                            <span class="text-dark kt-font-bolder">{__d('admin', 'chua_co')}</span>
                                                                        {/if}
                                                                    </p>
                                                                    <p class="mb-10">
                                                                        {__d('admin', 'the_bai_viet')}:
                                                                        <span class="kt-font-bolder wrp-comma">
                                                                            {if !empty($product.tags)}
                                                                                {foreach from = $product.tags item = tag}
                                                                                    {$tag.name}<span class="comma-item">, </span>
                                                                                {/foreach}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'tu_khoa_seo')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.seo_keyword)}
                                                                                {$product.seo_keyword}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'tieu_de_seo')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.seo_title)}
                                                                                {$product.seo_title}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>

                                                                    <p class="mb-10">
                                                                        {__d('admin', 'mo_ta_seo')}:
                                                                        <span class="kt-font-bolder">
                                                                            {if !empty($product.seo_description)}
                                                                                {$product.seo_description}
                                                                            {else}
                                                                                ...
                                                                            {/if}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'gia_va_phien_ban_san_pham')}
                                                </div>

                                                <div class="kt-wizard-v4__review-content">
                                                    {if !empty($product.items)}
                                                        <div class="row">
                                                            {foreach from = $product.items item = item}
                                                                <div class="col-md-6">
                                                                    <div class="kt-section">
                                                                        <div class="kt-section__content kt-section__content--solid">
                                                                            <p class="mb-10">
                                                                                {__d('admin', 'ma_san_pham')}:
                                                                                <span class="kt-font-bolder">
                                                                                    {if !empty($item.code)}
                                                                                        {$item.code}
                                                                                    {else}
                                                                                        ...
                                                                                    {/if}
                                                                                </span>
                                                                            </p>

                                                                            <p class="mb-10">
                                                                                {__d('admin', 'trang_thai')}: 
                                                                                {if !empty($item.status) && $item.status === 1}
                                                                                    <span class="text-success kt-font-bolder">
                                                                                        {__d('admin', 'hoat_dong')}
                                                                                    </span>
                                                                                {else}
                                                                                    <span class="text-danger kt-font-bolder">
                                                                                        {__d('admin', 'khong_hoat_dong')}
                                                                                    </span>    
                                                                                {/if}
                                                                            </p>

                                                                            {if empty($item.apply_special) && !empty($item.price)}
                                                                                <p class="mb-10">
                                                                                    {__d('admin', 'gia')}:
                                                                                    <span class="kt-font-bolder">
                                                                                        {$item.price|number_format:0:".":","}
                                                                                    </span>                    
                                                                                </p>
                                                                            {/if}

                                                                            {if !empty($item.price) && !empty($item.apply_special)}
                                                                                <p class="mb-10">
                                                                                    {__d('admin', 'gia')}:
                                                                                    <span class="kt-font-bolder">
                                                                                        {$item.price|number_format:0:".":","}
                                                                                    </span>                    
                                                                                </p>
                                                                            {/if}

                                                                            {if !empty($item.apply_special) && !empty($item.price_special)}
                                                                                <p class="mb-10">
                                                                                    {__d('admin', 'gia_dac_biet')}:
                                                                                    <span class="kt-font-bolder">
                                                                                        {$item.price_special|number_format:0:".":","}
                                                                                    </span>                    
                                                                                </p>
                                                                                {if !empty($item.date_special)}
                                                                                    <p class="mb-10">
                                                                                        {__d('admin', 'ngay_khuyen_mai')}:
                                                                                        <span class="kt-font-bolder">
                                                                                            {$item.date_special}
                                                                                        </span>                    
                                                                                    </p>
                                                                                {/if}
                                                                            {/if}
                                                                            
                                                                            <p class="mb-10">
                                                                                {__d('admin', 'anh_san_pham')}: 
                                                                                <div class="d-flex flex-wrap kt-margin-t-10">
                                                                                    {foreach from = $item.images item = image_item}
                                                                                        <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="album-{$item.id}" href="{CDN_URL}{$image_item}">
                                                                                            <img class="img-cover" src="{CDN_URL}{$image_item}" alt="image" style="width: 80px;">
                                                                                        </a>
                                                                                    {/foreach}
                                                                                </div>
                                                                            </p>

                                                                            <p class="mb-10">
                                                                                {__d('admin', 'so_luong_san_co')}:
                                                                                <span class="kt-font-bolder">
                                                                                    {if !empty($item.quantity_available)}
                                                                                        {$item.quantity_available}
                                                                                    {else}
                                                                                        0
                                                                                    {/if}
                                                                                </span>
                                                                            </p>  
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            {/foreach}
                                                        </div>
                                                    {/if}
                                                </div>
                                            </div>

                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'thong_tin_san_pham')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">
                                     
                                                    <p class="mb-10">
                                                        <span class="kt-font-bolder">
                                                            {__d('admin', 'mo_ta_ngan')}:
                                                        </span>
                                                        <div class="kt-section">
                                                            <div class="kt-section__content kt-section__content--solid">
                                                                {if !empty($product.description)}
                                                                    {$product.description}
                                                                {else}
                                                                    ...
                                                                {/if}
                                                            </div>
                                                        </div>                                                        
                                                    </p>
                                                    <p class="mb-10">
                                                        <span class="kt-font-bolder">
                                                            {__d('admin', 'noi_dung')}:
                                                        </span>
                                                        <div class="kt-section">
                                                            <div class="kt-section__content kt-section__content--solid">
                                                                {if !empty($product.content)}
                                                                    {$product.content}
                                                                {else}
                                                                    ...
                                                                {/if}
                                                            </div>
                                                        </div>                                                        
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
    <span class="kt-datatable--error">
        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
    </span>
{/if}

