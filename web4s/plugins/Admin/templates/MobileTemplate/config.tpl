{assign var = url_list value = "{ADMIN_PATH}/mobile-app/dashboard"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
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
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_mau_sac')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/setting-general/color-config" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label">
                                {__d('admin', 'mau_chinh')} (color_main)
                            </label>
                            <input name="color_main" value="{if !empty($color.color_main)}{$color.color_main}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label">
                                {__d('admin', 'mau_nut')} (button_color)
                            </label>
                            <input name="button_color" value="{if !empty($color.button_color)}{$color.button_color}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label">
                                {__d('admin', 'mau_chu')} (color_text)
                            </label>
                            <input name="color_text" value="{if !empty($color.color_text)}{$color.color_text}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label">
                                {__d('admin', 'mau_noi_bat')} (color_hightlight)
                            </label>
                            <input name="color_hightlight" value="{if !empty($color.color_hightlight)}{$color.color_hightlight}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="col-form-label">
                                {__d('admin', 'mau_screen')} (color_screen)
                            </label>
                            <input name="color_screen" value="{if !empty($color.color_screen)}{$color.color_screen}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_danh_sach_san_pham')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <p>{__d('admin', 'thiet_lap_cho_trang_he_thong')}</p>
            <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/setting-general/product-config" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'so_luong_tren_dong')}
                                <b>(Mobile)</b>
                            </label>            
                            <input name="number_on_line" value="{if !empty($product.number_on_line)}{$product.number_on_line}{/if}" class="form-control form-control-sm" type="number">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'so_luong_tren_dong')}
                                <b>(Ipad/Tablet)</b>
                            </label>            
                            <input name="ipad_number_on_line" value="{if !empty($product.ipad_number_on_line)}{$product.ipad_number_on_line}{/if}" class="form-control form-control-sm" type="number">
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                Element
                            </label>        
                            {$this->Form->select('element', $this->MobileTemplateAdmin->listElementProduct(), ['empty' => null, 'default' => "{if !empty($product.element)}{$product.element}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ti_le_chieu_cao_anh')}
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-arrows-alt-v"></i>
                                    </span>
                                </div>
                                <input name="image_height" value="{if !empty($product.image_height)}{$product.image_height}{/if}" class="form-control form-control-sm" type="number">
                            </div>            
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ti_le_chieu_rong_anh')}
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-arrows-alt-h"></i>
                                    </span>
                                </div>
                                <input name="image_width" value="{if !empty($product.image_width)}{$product.image_width}{/if}" class="form-control form-control-sm" type="number">
                            </div>            
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_bo_loc_nang_cao')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/setting-general/advanced-search-config" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'tim_kiem_tu_khoa')}</strong>
                            </label>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                {assign var = keyword value = ''}
                                {if !empty($advanced_search.keyword)}
                                    {assign var = keyword value = $advanced_search.keyword}
                                {/if}
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="keyword[show]" value="0" {if empty($keyword.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="keyword[show]" value="1" {if !empty($keyword.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="collapse-group form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'tinh_trang')}</strong>
                            </label>
                            {assign var = status value = ''}
                            {if !empty($advanced_search.status)}
                                {assign var = status value = $advanced_search.status}
                            {/if}

                            <div class="form-group mb-0">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="status[show]" value="0" {if empty($status.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="status[show]" value="1" {if !empty($status.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'lua_chon')}:
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="status[select_all]" value="1" {if isset($status.select_all) && $status.select_all == 1}checked{/if}> 
                                            {__d('admin', 'tat_ca')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="status[select_all]" value="0" {if isset($status.select_all) && $status.select_all == 0}checked{/if}> 
                                            {__d('admin', 'chon_trang_thai')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="collapse {if isset($status.select_all) && $status.select_all == 0}show{/if}">
                                <div class="clearfix">                                          
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                        <input name="status[status_id][]" value="{FEATURED}" {if !empty($status.status_id) && in_array({FEATURED}, $status.status_id)}checked{/if} type="checkbox"> 
                                            {__d('admin', 'noi_bat')}
                                        <span></span>
                                    </label>
                                </div> 
                                <div class="clearfix">                                          
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                        <input name="status[status_id][]" value="{DISCOUNT}" {if !empty($status.status_id) && in_array({DISCOUNT}, $status.status_id)}checked{/if} type="checkbox"> 
                                        {__d('admin', 'giam_gia')}
                                        <span></span>
                                    </label>
                                </div> 
                                <div class="clearfix">                                          
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                        <input name="status[status_id][]" value="{STOCKING}" {if !empty($status.status_id) && in_array({STOCKING}, $status.status_id)}checked{/if} type="checkbox"> 
                                        {__d('admin', 'con_hang')}
                                        <span></span>
                                    </label>
                                </div> 
                            </div>
                        </div>

                        <div class="collapse-group form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'danh_muc_san_pham')}</strong>
                            </label>
                            
                            <div class="form-group mb-0">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                {assign var = category value = ''}
                                {if !empty($advanced_search.category)}
                                    {assign var = category value = $advanced_search.category}
                                {/if}
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="category[show]" value="0" {if empty($category.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="category[show]" value="1" {if !empty($category.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'lua_chon')}:
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="category[select_all]" value="1" {if isset($category.select_all) && $category.select_all == 1}checked{/if}> 
                                            {__d('admin', 'tat_ca')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="category[select_all]" value="0" {if isset($category.select_all) && $category.select_all == 0}checked{/if}> 
                                            {__d('admin', 'chon_danh_muc')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="collapse {if isset($category.select_all) && $category.select_all == 0}show{/if}">
                                {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                                    {TYPE} => {PRODUCT}, 
                                    {LANG} => $lang,
                                    'get_parent' => true
                                ])}
                                <div class="kt-scroll" data-scroll="true" data-height="140" data-scrollbar-shown="true">
                                    {if !empty($list_categories)}
                                        {foreach from = $list_categories key = category_id item = cat}
                                            <div class="clearfix">                                          
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                                    <input name="category[category_id][]" value="{if !empty($category_id)}{$category_id}{/if}" {if !empty($category.category_id) && in_array($category_id, $category.category_id)}checked{/if} type="checkbox"> 
                                                    {if !empty($cat)}
                                                        {$cat}
                                                    {/if}
                                                    <span></span>
                                                </label>
                                            </div> 
                                        {/foreach}
                                    {else}
                                        <i class="mb-10">{__d('admin', 'chua_co_danh_muc_duoc_kich_hoat')}</i>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'tim_kiem_khoang_gia')}</strong>
                            </label>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                {assign var = price value = ''}
                                {if !empty($advanced_search.price)}
                                    {assign var = price value = $advanced_search.price}
                                {/if}
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="price[show]" value="0" {if empty($price.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="price[show]" value="1" {if !empty($price.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="kt-form__group kt-form__group--inline">
                                <div class="kt-form__group">
                                    <div class="input-group">
                                        <input id="price_from" type="text" value="{if isset($price.price_from)}{$price.price_from}{/if}" class="form-control number-input" name="price[price_from]" placeholder="Từ">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fa fa-long-arrow-alt-right"></i></span>
                                        </div>
                                        <input id="price_to" type="text" value="{if isset($price.price_to)}{$price.price_to}{/if}" class="form-control number-input" name="price[price_to]" placeholder="Đến">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse-group form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'thuong_hieu')}</strong>
                            </label>
                            <div class="form-group mb-0">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                {assign var = brand value = ''}
                                {if !empty($advanced_search.price)}
                                    {assign var = brand value = $advanced_search.brand}
                                {/if}
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="brand[show]" value="0" {if empty($brand.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="brand[show]" value="1" {if !empty($brand.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'lua_chon')}:
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="brand[select_all]" value="1" {if isset($brand.select_all) && $brand.select_all == 1}checked{/if}> 
                                            {__d('admin', 'tat_ca')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="brand[select_all]" value="0" {if isset($brand.select_all) && $brand.select_all == 0}checked{/if}> 
                                            {__d('admin', 'chon_thuong_hieu')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="collapse {if isset($brand.select_all) && $brand.select_all == 0}show{/if}">
                                <div class="kt-scroll" data-scroll="true" data-height="140" data-scrollbar-shown="true">
                                    {assign var = list_brands value = $this->BrandAdmin->getListBrands()}
                                    {if !empty($list_brands)}
                                        {foreach from = $list_brands key = brand_id item = item_brand}
                                            <div class="clearfix">        
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                                    <input name="brand[brand_id][]" value="{$brand_id}" type="checkbox" {if !empty($brand.brand_id) && in_array($brand_id, $brand.brand_id)}checked{/if}> 
                                                    {if !empty($item_brand)}
                                                        {$item_brand}
                                                    {/if}
                                                    <span></span>
                                                </label>
                                            </div> 
                                        {/foreach}
                                    {else}
                                        <i class="mb-10">{__d('admin', 'chua_co_thuong_hieu_duoc_kich_hoat')}</i>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <div class="collapse-group form-group mb-15">
                            <label>
                                <strong>{__d('admin', 'thuoc_tinh')}</strong>
                            </label>

                            <div class="form-group mb-0">
                                <label class="col-form-label">
                                    {__d('admin', 'hien_thi')}:
                                </label>
                                {assign var = attribute value = ''}
                                {if !empty($advanced_search.attribute)}
                                    {assign var = attribute value = $advanced_search.attribute}
                                {/if}
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                        <input type="radio" name="attribute[show]" value="0" {if empty($attribute.show)}checked{/if}> 
                                            {__d('admin', 'khong')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input type="radio" name="attribute[show]" value="1" {if !empty($attribute.show)}checked{/if}> 
                                            {__d('admin', 'co')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">
                                    {__d('admin', 'lua_chon')}:
                                </label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="attribute[select_all]" value="1" {if isset($attribute.select_all) && $attribute.select_all == 1}checked{/if}> 
                                            {__d('admin', 'tat_ca')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                        <input class="collapse-btn" type="radio" name="attribute[select_all]" value="0" {if isset($attribute.select_all) && $attribute.select_all == 0}checked{/if}> 
                                            {__d('admin', 'chon_thuoc_tinh')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="collapse {if isset($attribute.select_all) && $attribute.select_all == 0}show{/if}">
                                <div class="kt-scroll" data-scroll="true" data-height="140" data-scrollbar-shown="true">
                                    {assign var = list_attributes value = $this->AttributeAdmin->getSpecialItem($lang)}
                                    {if !empty($list_attributes)}
                                        {foreach from = $list_attributes key = attribute_id item = attr}
                                            <div class="clearfix">        
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                                    <input name="attribute[attribute_id][]" value="{if !empty($attribute_id)}{$attribute_id}{/if}" type="checkbox" {if !empty($attribute.attribute_id) && in_array($attribute_id, $attribute.attribute_id)}checked{/if}> 
                                                    {if !empty($attr)}
                                                        {$attr}
                                                    {/if}
                                                    <span></span>
                                                </label>
                                            </div> 
                                        {/foreach}
                                    {else}
                                        <i class="mb-10">{__d('admin', 'chua_co_thuong_hieu_duoc_kich_hoat')}</i>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'duong_dan_chinh_sach')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/setting-general/link-policy-config" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chinh_sach')}
                            </label>            
                            <input name="policy" value="{if !empty($link_policy.policy)}{$link_policy.policy}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'dieu_khoan_su_dung')}
                            </label>            
                            <input name="terms" value="{if !empty($link_policy.terms)}{$link_policy.terms}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'gioi_thieu')}
                            </label>            
                            <input name="about_us" value="{if !empty($link_policy.about_us)}{$link_policy.about_us}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'xoa_du_lieu_nguoi_dung')}
                            </label>            
                            <input name="user_delete" value="{if !empty($link_policy.user_delete)}{$link_policy.user_delete}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>