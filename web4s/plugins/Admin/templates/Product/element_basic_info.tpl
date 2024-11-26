{if !empty($id)}
    <div class="kt-portlet nh-portlet nh-active-hover position-relative">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'thong_tin_cap_nhat')}
                </h3>
            </div>
            {if !empty($product.url)}
                <div class="kt-portlet__head-toolbar">
                    <a target="_blank" href="/{$product.url}" class="kt-link kt-font-bolder kt-link--info">
                        {__d('admin', 'xem_san_pham')}
                    </a>
                </div>
            {/if}
        </div>

        <div class="kt-portlet__body pb-0">
            <div class="row">
                <div class="col-lg-6 col-xs-6">
                    
                    <div class="form-group form-group-xs row">
                        <label class="col-lg-4 col-xl-4 col-form-label">
                            {__d('admin', 'trang_thai')}
                        </label>

                        <div class="col-lg-8 col-xl-8">
                            {if !empty($product.draft)}
                                <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'ban_luu_nhap')}
                                </span>
                            {/if}

                            {if isset($product.status) && $product.status == 1}
                                <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'hoat_dong')}
                                </span>
                            {elseif ($product.draft == 1) || (isset($product.status) && $product.status == 0)}
                                <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'ngung_hoat_dong')}
                                </span>
                            {elseif isset($product.status) && $product.status == -1}
                                <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'cho_duyet')}
                                </span>
                            {elseif isset($product.status) && $product.status == 2}
                                <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'ngung_kinh_doanh')}
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
                                {if !empty($product.user_full_name)}
                                    {$product.user_full_name}
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
                                {if !empty($product.created)}
                                    {$product.created}
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
                                {if !empty($product.updated)}
                                    {$product.updated}
                                {/if}
                            </span>
                        </div>
                    </div>

                    <div class="form-group form-group-xs row">
                        <label class="col-lg-4 col-xl-4 col-form-label">
                            {__d('admin', 'seo')}
                        </label>
                        <div class="col-lg-8 col-xl-8">
                            {if !empty($product.seo_score) && $product.seo_score == 'success'}
                                <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'tot')}
                                </span>
                            {elseif !empty($product.seo_score) && $product.seo_score == 'warning'}
                                <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'binh_thuong')}
                                </span>
                            {elseif !empty($product.seo_score) && $product.seo_score == 'danger'}
                                <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'chua_dat')}
                                </span>
                            {else}
                                <span class="form-control-plaintext"><em>{__d('admin', 'chua_co')}</em></span>
                            {/if}
                        </div>
                    </div>

                    <div class="form-group form-group-xs row">
                        <label class="col-lg-4 col-xl-4 col-form-label">
                            {__d('admin', 'tu_khoa')}
                        </label>
                        <div class="col-lg-8 col-xl-8">
                            <div class="kt-section__content kt-section__content--solid">
                                {if !empty($product.keyword_score) && $product.keyword_score == 'success'}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'tot')}
                                </span>
                                {elseif !empty($product.keyword_score) && $product.keyword_score == 'warning'}
                                    <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'binh_thuong')}
                                </span>
                                {elseif !empty($product.keyword_score) && $product.keyword_score == 'danger'}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                    {__d('admin', 'chua_dat')}
                                </span>
                                {else}
                                    <span class="form-control-plaintext"><em>{__d('admin', 'chua_co')}</em></span>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-xs-6">  
                    <div class="form-group form-group-xs row">
                        <label class="col-lg-4 col-xl-4 col-form-label">
                            {__d('admin', 'ngon_ngu_hien_tai')}
                        </label>
                        <div class="col-lg-8 col-xl-8">
                            <span class="form-control-plaintext kt-font-bolder">
                                <div class="list-flags">
                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                                    {if !empty($list_languages[$lang])}
                                        {$list_languages[$lang]}
                                    {/if}
                                </div>                                        
                            </span>
                        </div>
                    </div>

                    {assign var = all_name_content value = $this->ProductAdmin->getAllNameContent($id)}
                    {if !empty($use_multiple_language) && !empty($list_languages) }
                        <div class="form-group form-group-xs row">
                            <label class="col-lg-4 col-xl-4 col-form-label">
                                {__d('admin', 'sua_ban_dich')}
                            </label>
                            <div class="col-lg-12 col-xs-12">
                                <table class="table table-bordered mb-10">
                                    <tbody>
                                        {foreach from = $list_languages key = k_language item = language}
                                            
                                            <tr>
                                                <td class="w-90">
                                                    <div class="list-flags d-inline mr-5">
                                                        <img src="{ADMIN_PATH}{FLAGS_URL}{$k_language}.svg" alt="{$k_language}" class="flag" />
                                                    </div>
                                                    {$language}: 
                                                    <i>
                                                        {if !empty($all_name_content[$k_language])}
                                                            {$all_name_content[$k_language]|truncate:100:" ..."}
                                                        {else}
                                                            <span class="kt-font-danger fs-12">
                                                                {__d('admin', 'chua_nhap')}
                                                            </span>
                                                        {/if}
                                                    </i>

                                                    <a href="{ADMIN_PATH}/product/update/{$product.id}?lang={$k_language}" class="pl-10">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>                                            
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}        

<div class="kt-portlet nh-portlet nh-active-hover position-relative">
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
                {__d('admin', 'ten_san_pham')}
                <span class="kt-font-danger">*</span>
            </label>
            <input id="name" name="name" value="{if !empty($product.name)}{$product.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'duong_dan')}
                <span class="kt-font-danger">*</span>
            </label>
            <div class="input-group flex-nowrap">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="la la-link"></i>
                    </span>
                </div>
                <input name="link" value="{if !empty($product.url)}{$product.url}{/if}" data-link-id="{if !empty($product.url_id)}{$product.url_id}{/if}" type="text" class="form-control form-control-sm nh-link" maxlength="255">
            </div>
        </div>
        <div id="wrap-category" class="row">
            <div class="col-lg-9">
                <div class="form-group">
                    <label>
                        {__d('admin', 'danh_muc')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-align-justify w-20px"></i>
                            </span>
                        </div>
                        {assign var = categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                            {TYPE} => {PRODUCT}, 
                            {LANG} => $lang
                        ])}

                        {assign var = categories_selected value = []}
                        {if !empty($product.categories)}
                            {foreach from = $product.categories item = category}
                                {$categories_selected[] = $category.id}
                            {/foreach}
                        {/if}

                        {$this->Form->select('categories', $categories, ['id' => 'categories', 'empty' => null, 'default' => $categories_selected, 'class' => 'form-control kt-select-multiple', 'multiple' => 'multiple', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}"])}
                    </div>
                </div> 
            </div>

            <div class="col-lg-3">
                <label>
                    {__d('admin', 'danh_muc_chinh')}
                </label>

                {$this->Form->select('main_category_id', $main_categories, ['id' => 'main_category_id', 'empty' => {__d('admin', 'chon')}, 'default' => $main_category_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}", 'nh-brand-by-category' => "{if !empty($brand_by_category)}1{else}0{/if}", 'nh-attribute-by-category' => "{if !empty($attribute_by_category)}1{else}0{/if}"])}
            </div>
        </div>                          

        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="form-group">
                    <label>
                        {__d('admin', 'thuong_hieu')}
                    </label>

                    {assign var = brands value = $this->BrandAdmin->getBrandByMainCategory($main_category_id, $lang)}

                    {assign var = search_brand value = false}
                    {if !empty($brands) && count($brands) > 7}
                        {$search_brand = true}                                        
                    {/if}

                    {$this->Form->select('brand_id', $brands, ['id' => 'brand_id', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($product.brand_id)}{$product.brand_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-live-search' => $search_brand])}
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'san_pham_noi_bat')}
                            </label>
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="featured" value="1" {if !empty($product.featured)}checked{/if}> {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="featured" value="0" {if empty($product.featured)}checked{/if}> {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label class="mb-10">
                                {__d('admin', 'hien_thi_muc_luc')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="catalogue" value="1" {if !empty($product.catalogue)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>
                                
                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="catalogue" value="0" {if empty($product.catalogue)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                        

        <div class="row">
            <div class="col-xl-3 col-lg-3">
                <div class="form-group mb-0">
                    <label>
                        {__d('admin', 'vi_tri')}
                    </label>

                    <input name="position" value="{$position}" class="form-control form-control-sm" type="text">
                </div>
            </div>
        </div>
    </div>
</div>