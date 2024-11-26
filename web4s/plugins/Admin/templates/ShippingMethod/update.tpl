{assign var = url_list value = "{ADMIN_PATH}/shipping-method"}
{assign var = url_add value = "{ADMIN_PATH}/shipping-method/add"}
{assign var = url_edit value = "{ADMIN_PATH}/shipping-method/update"}

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
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/shipping-method/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <input name="custom_config" value="" type="hidden" >

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_chung')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'phi_van_chuyen_chung')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-money-bill"></i>
                                    </span>
                                </div>
                                <input name="general_shipping_fee" value="{if !empty($shipping_method.general_shipping_fee)}{$shipping_method.general_shipping_fee}{/if}" class="form-control form-control-sm text-right number-input" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-8 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'lay_gia_van_chuyen')}
                            </label>

                            {assign var = type_fee value = ''}
                            {if !empty($shipping_method.type_fee)}
                                {assign var = type_fee value = $shipping_method.type_fee}
                            {/if}

                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input name="type_fee" value="general" {if empty($type_fee) || $type_fee == 'general'}checked{/if} type="radio" >
                                    {__d('admin', 'theo_phi_chung')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input name="type_fee" value="custom" {if $type_fee == 'custom'}checked{/if} type="radio" >
                                    {__d('admin', 'theo_cau_hinh_rieng')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

                <div class="kt-heading kt-heading--md">
                    {__d('admin', 'cau_hinh_rieng_theo_gia_tri_don_hang')}
                </div>

                <div class="form-group">
                    <span btn-action="add-item" class="btn btn-sm btn-success">
                        <i class="la la-plus"></i>
                        {__d('admin', 'them_gia_tri_don_hang')}
                    </span>
                </div>

                <table id="table-config-by-order" class="table mb-0 nh-table-item">
                    <thead class="thead-light">
                        <tr>
                            <th class="w-20">
                                {__d('admin', 'gia_tri_don_hang')}
                            </th>

                            <th class="w-65">
                                {__d('admin', 'tinh_thanh_thanh_pho')}
                            </th>

                            <th class="w-10">
                                {__d('admin', 'phi_van_chuyen')}
                            </th>

                            <th class="w-3">
                                
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        {if !empty($shipping_method.custom_config)}
                            {foreach from = $shipping_method.custom_config item = item}
                                {$this->element('../ShippingMethod/shipping_row_element', ['item' => $item])}
                            {/foreach}
                        {else}
                            {$this->element('../ShippingMethod/shipping_row_element', ['item' => []])}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'mo_ta_phuong_thuc')}
                    </h3>
                </div>
            </div>

            {foreach from = $list_languages key = k_language item = language}
                <input name="content_id[{$k_language}]" value="{if !empty($content[$k_language].id)}{$content[$k_language].id}{/if}" type="hidden">
            {/foreach}

            <div class="kt-portlet__body">
                {foreach from = $list_languages key = k_language item = language}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_phuong_thuc')}
                            <span class="kt-font-danger">*</span>
                            ({$language} <img src="/admin/assets/media/flags/{$k_language}.svg" class="flag h-15px w-15px"> )  
                        </label>
                        <input name="name[{$k_language}]" value="{if !empty($content[$k_language].name)}{$content[$k_language].name|escape}{/if}" class="form-control form-control-sm required" message-required="{__d('admin', 'vui_long_nhap_thong_tin')}" type="text">
                    </div>
                {/foreach}

                {foreach from = $list_languages key = k_language item = language}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'mo_ta_ngan')}
                            ({$language} <img src="/admin/assets/media/flags/{$k_language}.svg" class="flag h-15px w-15px"> )
                        </label>                            
                        <div class="clearfix">
                            <textarea name="description[{$k_language}]" id="description_{$k_language}" class="mce-editor-simple">{if !empty($content[$k_language].description)}{$content[$k_language].description}{/if}</textarea>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </form>
</div>