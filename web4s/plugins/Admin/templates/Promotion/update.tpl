{assign var = url_list value = "{ADMIN_PATH}/promotion"}
{assign var = url_add value = "{ADMIN_PATH}/promotion/add"}
{assign var = url_edit value = "{ADMIN_PATH}/promotion/update"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {if !empty($url_edit) || !empty($url_add)}
                <div class="btn-group">
                    {if empty($id)}
                        <button data-link="{$url_edit}" data-update="1" id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-plus"></i>
                            {__d('admin', 'them_moi')} (F1)
                        </button>
                    {else}
                        <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-edit"></i>
                            {__d('admin', 'cap_nhat')} (F1)
                        </button>
                    {/if}
                    
                    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="kt-nav p-0">

                            {if !empty($url_add)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_add}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-medical-records"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_them_moi')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_list)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_list}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-hourglass-1"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_quay_lai')}
                                        </span>
                                    </span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="promotion-form" action="{ADMIN_PATH}/promotion/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_chuong_trinh_khuyen_mai')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($promotion.name)}{$promotion.name|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>
                </div>
                        
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_chuong_trinh')}
                                 <span class="kt-font-danger">*</span>
                            </label>
                            <input name="code" value="{if !empty($promotion.code)}{$promotion.code|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" placeholder="VD: KHUYENMAI20K">
                            <span class="form-text text-muted">
                                <p class="mb-5">
                                    {__d('admin', 'neu_khong_nhap_he_thong_se_tu_dong_sinh_ra_ma')}
                                </p>

                                <p>
                                    {__d('admin', 'ma_chuong_trinh_se_duoc_coi_nhu_1_ma_coupon_mac_dinh')}
                                </p>
                            </span>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'hien_thi')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="public" value="1" {if !isset($promotion.public) || !empty($promotion.public)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>
                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="public" value="0" {if isset($promotion.public) && $promotion.public == 0}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>

                            <span class="form-text text-muted">
                                <p class="mb-5">
                                    {__d('admin', 'hien_thi_chuong_trinh_trong_danh_sach_khuyen_mai_cua_khach_hang')}
                                </p>
                            </span>
                        </div>
                    </div>
                </div>              
            </div>
        </div>

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_chuong_trinh_khuyen_mai')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'thoi_gian_ap_dung')}
                            </label>
                            <div class="input-daterange input-group">
                                <input name="start_time" value="{if !empty($promotion.start_time)}{$this->Utilities->convertIntgerToDateString($promotion.start_time)}{/if}" type="text" class="form-control select-date" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-ellipsis-h"></i>
                                    </span>
                                </div>
                                <input name="end_time" value="{if !empty($promotion.end_time)}{$this->Utilities->convertIntgerToDateString($promotion.end_time)}{/if}" type="text" class="form-control select-date" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                </div>

                {assign var = type_discount value = "{if !empty($promotion.type_discount)}{$promotion.type_discount}{/if}"}
                {assign var = value value = []}
                {if !empty($promotion.value)}
                    {$value = $promotion.value}
                {/if}

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="mb-10">
                                {__d('admin', 'loai_khuyen_mai')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {$this->Form->select('type_discount', $this->PromotionAdmin->getListTypePromotion(), ['id' => 'type-discount', 'empty' => "-- {__d('admin', 'chon')} --", 'default' => $type_discount, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div nh-wrap-value="discount" class="{if $type_discount != 'discount_order' &&  $type_discount != 'discount_product'}d-none{/if}">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-12">
                            <div class="form-group">
                                <label class="mb-10">
                                    {__d('admin', 'gia_tri_chiet_khau')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                {assign var = type_value_discount value = "{MONEY}"}
                                {if !empty($value.type_value_discount)}
                                    {$type_value_discount = $value.type_value_discount}
                                {/if}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span nh-value-discount-type="{MONEY}" class="input-group-text w-auto cursor-p {if $type_value_discount == MONEY}active{/if}">$</span>
                                        <span nh-value-discount-type="{PERCENT}" class="input-group-text w-auto cursor-p {if $type_value_discount == PERCENT}active{/if}">%</span>
                                    </div>

                                    <input input-value="type_value_discount" input-value-discount-type value="{$type_value_discount}" type="hidden">
                                    <input input-value="value_discount" value="{if !empty($value.value_discount)}{$value.value_discount}{/if}" class="form-control form-control-sm text-right number-input" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-12">
                            <div class="form-group">
                                <label class="mb-10">
                                    {__d('admin', 'gia_tri_chiet_khau_toi_da')}
                                </label>
                                <input input-value="max_value" id="max-value-discount" value="{if !empty($value.max_value)}{$value.max_value}{/if}" class="form-control form-control-sm number-input" type="text">
                            </div>
                        </div>
                    </div>
                </div>

                <div nh-wrap-value="free-ship" class="{if $type_discount != 'free_ship'}d-none{/if}">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-12">
                            <div class="form-group">
                                <label class="mb-10">
                                    {__d('admin', 'gia_tri_mien_phi_toi_da')}
                                </label>
                                <input input-value="max_value" id="max-value-free-ship" value="" class="form-control form-control-sm number-input" type="text">
                            </div>
                        </div>
                    </div>
                </div>

                <div nh-wrap-value="give-product" class="{if $type_discount != 'give_product'}d-none{/if}">
                    {assign var = give_product value = [0 => [
                        'buy' => [],
                        'give' => []
                    ]]}

                    {if !empty($value.give_product)}
                        {$give_product = $value.give_product}
                    {/if}

                    {foreach from = $give_product item = item}
                        {assign var = item_buy value = []}
                        {if !empty($item.buy)}
                            {$item_buy = $item.buy}
                        {/if}

                        {assign var = item_give value = []}
                        {if !empty($item.give)}
                            {$item_give = $item.give}
                        {/if}

                        <div nh-give-product-item class="row mt-10 mb-10">
                            <div class="col-xl-12 col-lg-12 mb-10 text-right">
                                <span nh-give-product-action="buy" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-edit"></i>
                                    {__d('admin', 'thay_doi_dieu_kien_mua')}
                                </span>

                                <span nh-give-product-action="give" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-gift"></i>
                                    {__d('admin', 'thay_doi_san_pham_tang')}
                                </span>

                                <span nh-give-product-action="delete" class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash-alt"></i>
                                    {__d('admin', 'xoa')}
                                </span>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <div class="table-responsive nh-table-responsive">
                                        <table nh-give-product-table="buy" class="table mb-0 nh-table-item">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-left">
                                                        {__d('admin', 'san_pham_mua')}
                                                    </th>

                                                    <th style="width: 80px;">
                                                        {__d('admin', 'so_luong')}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                {if !empty($item_buy)}
                                                    {foreach from = $item_buy item = product}
                                                        <tr>
                                                            <td>
                                                                <span nh-give-product-label="product-name">
                                                                    {if !empty($product.name)}
                                                                        {$product.name}
                                                                    {/if}
                                                                </span>
                                                            </td>

                                                            <td class="text-center">
                                                                <span nh-give-product-label="quantity">
                                                                    {if !empty($product.quantity)}
                                                                        {$product.quantity}
                                                                    {/if}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                {else}
                                                    <tr init-give-row>
                                                        <td>
                                                            <span nh-give-product-label="product-name"></span>
                                                        </td>

                                                        <td class="text-center">
                                                            <span nh-give-product-label="quantity"></span>
                                                        </td>
                                                    </tr>
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group mb-0">
                                    <div class="table-responsive nh-table-responsive">
                                        <table nh-give-product-table="give" class="table mb-0 nh-table-item">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-left">
                                                        {__d('admin', 'san_pham_duoc_tang')}
                                                    </th>

                                                    <th style="width: 80px;">
                                                        {__d('admin', 'so_luong')}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {if !empty($item_give)}
                                                    {foreach from = $item_give item = product}
                                                        <tr>
                                                            <td>
                                                                <span nh-give-product-label="product-name">
                                                                    {if !empty($product.name)}
                                                                        {$product.name}
                                                                    {/if}
                                                                </span>
                                                            </td>

                                                            <td class="text-center">
                                                                <span nh-give-product-label="quantity">
                                                                    {if !empty($product.quantity)}
                                                                        {$product.quantity}
                                                                    {/if}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                {else}
                                                    <tr init-give-row>
                                                        <td>
                                                            <span nh-give-product-label="product-name"></span>
                                                        </td>

                                                        <td class="text-center">
                                                            <span nh-give-product-label="quantity"></span>
                                                        </td>
                                                    </tr>
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                            
                            </div>

                            <div class="col-xl-12 col-lg-12">
                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-0 mb-20"></div>
                            </div>
                        </div>
                    {/foreach}

                    <span nh-give-product-action="add-item" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i>
                        {__d('admin', 'them_dieu_kien')}
                    </span>
                    <input input-value="give_product" type="hidden" />
                </div>
                
                <input id="value" name="value" value="{if !empty($value)}{htmlentities($value|@json_encode)}{/if}" type="hidden" >
            </div>
        </div>

        {assign var = condition_order value = []}
        {if !empty($promotion.condition_order)}
            {$condition_order = $promotion.condition_order}
        {/if}
        <div nh-wrap-condition="order" class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ap_dung_voi_don_hang')}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                            <input check-condition="order" name="check_condition[order]" {if !empty($condition_order)}checked="true"{/if} type="checkbox"> 
                            {__d('admin', 'them_dieu_kien_ap_dung')}
                            <span></span>
                        </label>  
                    </div>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div nh-condition-info="order" class="collapse {if !empty($condition_order)}show{/if}">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'gia_tri_toi_thieu')}
                                </label>
                                <input name="condition_order[min_value]" value="{if !empty($condition_order.min_value)}{$condition_order.min_value}{/if}" class="form-control form-control-sm number-input" type="text">
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'gia_tri_toi_da')}
                                </label>
                                <input name="condition_order[max_value]" value="{if !empty($condition_order.max_value)}{$condition_order.max_value}{/if}" class="form-control form-control-sm number-input" type="text">
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'so_luong_san_pham_mua_toi_thieu')}
                                </label>
                                <input name="condition_order[number_product]" value="{if !empty($condition_order.number_product)}{$condition_order.number_product}{/if}" class="form-control form-control-sm number-input" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {assign var = condition_product value = []}
        {if !empty($promotion.condition_product)}
            {$condition_product = $promotion.condition_product}
        {/if}
        <div nh-wrap-condition="product" class="kt-portlet nh-portlet {if $type_discount == 'give_product'}d-none{/if}">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ap_dung_voi_san_pham')}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                            <input check-condition="product" name="check_condition[product]" {if !empty($condition_product)}checked="true"{/if} type="checkbox"> 
                            {__d('admin', 'them_dieu_kien_ap_dung')}
                            <span></span>
                        </label>  
                    </div>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div nh-condition-info="product" class="collapse {if !empty($condition_product)}show{/if}">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ap_dung_cho')}
                                </label>

                                {assign var = type_condition_product value = ''}
                                {if !empty($condition_product.type)}
                                    {$type_condition_product = $condition_product.type}
                                {/if}

                                {$this->Form->select('condition_product[type]', $this->PromotionAdmin->getListConditionProduct(), ['id' => 'type-condition-product', 'empty' => "-- {__d('admin', 'chon')} --", 'default' => $type_condition_product, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div nh-wrap-select="{PRODUCT}" class="{if $type_condition_product != PRODUCT}d-none{/if}">
                        <div class="row">
                            <div class="col-xl-8 col-lg-8">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'san_pham_ap_dung')}
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="flaticon-search"></i>
                                            </span>
                                        </div>
                                        <input suggest-item="{PRODUCT}" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_san_pham')}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'san_pham_da_chon')}
                            </label>

                            <div nh-item-selected class="clearfix mh-35 tagify">
                                {if !empty($condition_product.ids) && $type_condition_product == PRODUCT}
                                    {foreach from = $condition_product.ids item = product_id}
                                        {assign var = product_info value = $this->ProductAdmin->getDetailProductItem($product_id, $lang, ['get_attribute' => true])}                                    
                                        <span class="tagify__tag">
                                            <x class="tagify__tag__removeBtn" role="button"></x>
                                            <div>
                                                <span class="tagify__tag-text">
                                                    {if !empty($product_info.name_extend)}
                                                        {$product_info.name_extend}
                                                    {/if}
                                                </span>
                                            </div>
                                            <input name="condition_product[ids][]" value="{$product_id}" type="hidden">
                                        </span>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div nh-wrap-select="{CATEGORY_PRODUCT}" class="{if $type_condition_product != CATEGORY_PRODUCT}d-none{/if}">
                        <div class="row">
                            <div class="col-xl-8 col-lg-8">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'danh_muc_ap_dung')}
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="flaticon-search"></i>
                                            </span>
                                        </div>
                                        <input suggest-item="{CATEGORY_PRODUCT}" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_danh_muc_san_pham')}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'danh_muc_da_chon')}
                            </label>

                            <div nh-item-selected class="clearfix mh-35 tagify">
                                {if !empty($condition_product.ids) && $type_condition_product == CATEGORY_PRODUCT}
                                    {foreach from = $condition_product.ids item = category_id}

                                        {assign var = category_info value = $this->CategoryAdmin->getDetailCategory(PRODUCT, $category_id, $lang)}
                                        <span class="tagify__tag">
                                            <x class="tagify__tag__removeBtn" role="button"></x>
                                            <div>
                                                <span class="tagify__tag-text">
                                                    {if !empty($category_info.name)}
                                                        {$category_info.name}
                                                    {/if}
                                                </span>
                                            </div>
                                            <input name="condition_product[ids][]" value="{$category_id}" type="hidden">
                                        </span>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div nh-wrap-select="{BRAND}" class="{if $type_condition_product != BRAND}d-none{/if}">
                        <div class="row">
                            <div class="col-xl-8 col-lg-8">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'thuong_hieu_ap_dung')}
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="flaticon-search"></i>
                                            </span>
                                        </div>
                                        <input suggest-item="{BRAND}" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_thuong_hieu')}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'thuong_hieu_da_chon')}
                            </label>

                            <div nh-item-selected class="clearfix mh-35 tagify">
                                {if !empty($condition_product.ids) && $type_condition_product == BRAND}
                                    {foreach from = $condition_product.ids item = brand_id}
                                    
                                        {assign var = brand_info value = $this->BrandAdmin->getDetailBrand($brand_id, $lang)}
                                        <span class="tagify__tag">
                                            <x class="tagify__tag__removeBtn" role="button"></x>
                                            <div>
                                                <span class="tagify__tag-text">
                                                    {if !empty($brand_info.name)}
                                                        {$brand_info.name}
                                                    {/if}
                                                </span>
                                            </div>
                                            <input name="condition_product[ids][]" value="{$brand_id}" type="hidden">
                                        </span>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {assign var = condition_location value = []}
        {if !empty($promotion.condition_location)}
            {$condition_location = $promotion.condition_location}
        {/if}
        <div nh-wrap-condition="location" class="kt-portlet nh-portlet d-none">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ap_dung_voi_tinh_thanh')}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-actions">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                            <input check-condition="location" name="check_condition[location]" {if !empty($condition_location)}checked="true"{/if} type="checkbox"> 
                            {__d('admin', 'them_dieu_kien_ap_dung')}
                            <span></span>
                        </label>  
                    </div>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div nh-condition-info="location" class="collapse {if !empty($condition_location)}show{/if}">
                    <div nh-wrap-select="location">
                        <div class="row">
                            <div class="col-xl-8 col-lg-8">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tinh_thanh_ap_dung')}
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="flaticon-search"></i>
                                            </span>
                                        </div>
                                        <input suggest-item="location" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_tinh_thanh')}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'tinh_thanh_da_chon')}
                            </label>

                            <div nh-item-selected class="clearfix mh-35 tagify">
                                {if !empty($condition_location.ids)}
                                    {foreach from = $condition_location.ids item = city_id}
                                        {assign var = city_info value = $this->LocationAdmin->getDetailCity($city_id)}                                    
                                        <span class="tagify__tag">
                                            <x class="tagify__tag__removeBtn" role="button"></x>
                                            <div>
                                                <span class="tagify__tag-text">
                                                    {if !empty($city_info.name)}
                                                        {$city_info.name}
                                                    {/if}
                                                </span>
                                            </div>
                                            <input name="condition_location[ids][]" value="{$city_id}" type="hidden">
                                        </span>
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="give-product-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'chon_san_pham')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="flaticon-search"></i>
                            </span>
                        </div>
                        <input suggest-item="give-product" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_san_pham')}" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <div class="table-responsive nh-table-responsive">
                        <table table-select-product="" table-index="" class="table mb-0 nh-table-item">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-left">
                                        {__d('admin', 'ten_san_pham')}
                                    </th>

                                    <th style="width: 100px;">
                                        {__d('admin', 'so_luong')}
                                    </th>

                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr product-item-select="">
                                    <td nh-field="name"></td>

                                    <td class="text-center">
                                        <input nh-field="quantity" value="1" class="form-control form-control-sm number-input" type="text">
                                    </td>

                                    <td class="text-center">
                                        <i nh-action="delete" class="fa fa-trash-alt"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-apply-product" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'ap_dung')}
                </button>
            </div>
        </div>
    </div>
</div>