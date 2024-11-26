{if empty($attributes_special) || (!empty($attributes_special) && empty($has_attribute_image))}
    <div class="form-group">
        <label>
            {__d('admin', 'anh_san_pham')}
        </label>
        <div class="row mb-10 wrap-album collapse show">
            <div class="col-lg-9 col-xl-8">
                <div class="list-image-album">
                    <input id="album" name="album" value="{if !empty($product.all_images)}{htmlentities($product.all_images|@json_encode)}{/if}" type="hidden" />
                    {if !empty($product.all_images)}                       
                        {foreach from = $product.all_images item = image}
                            <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
                                <img src="{CDN_URL}{$image}">
                                <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
                                    <i class="fa fa-times"></i>
                                </span>
                            </a>
                        {/foreach}
                    {/if}
                </div>
            </div>

            {assign var = url_select_album value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=album"}

            <div class="col-lg-3 col-xl-2">
                <span data-src="{$url_select_album}" class="col-lg-12 btn-sm btn btn-success btn-select-image-album" data-type="iframe">
                    <i class="fa fa-images"></i> 
                    {__d('admin', 'chon_anh')}
                </span>
            </div>
        </div>
    </div>
{/if}

{if !empty($attributes_special)}
    {foreach from = $attributes_special item = attribute}
        {assign var = attribute_code value = $attribute.code}

        {if $attribute.has_image}
            {if !empty($options_special_selected[$attribute_code])}
                <div class="form-group">
                    <label>
                        {if !empty($attribute.name)}
                            {$attribute.name}
                        {/if}

                        {if !empty($attribute.required)}
                            <span class="kt-font-danger">*</span>
                        {/if}
                    </label>

                    {foreach from = $options_special_selected[$attribute_code] key = selected_option_id  item = option_name}
                        <div class="clearfix item-attribute-image">
                            <div class="row mb-10 ">
                                <div class="col-lg-9 col-xl-8 special-attribute-select">
                                    {assign var = attr value = $attribute}
                                    {$attr.value = $selected_option_id}
                                    {$attr.disabled = true}
                                    {$this->AttributeAdmin->generateInput($attr, $lang)}
                                </div>

                                <div class="col-lg-3 col-xl-2">
                                    <button type="button" class="btn btn-sm btn-danger btn-icon btn-circle mr-5 delete-attribute-image">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-brand btn-icon btn-circle collapse-attribute-image" aria-expanded="true">
                                        <i class="fa fa-angle-double-down"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-10 wrap-album collapse show">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="list-image-album">
                                        {assign var = code_album value = "{$attribute_code}_{$selected_option_id}"}
                                        <input id="{$attribute_code}_{$selected_option_id}" value="{if !empty($item_images[{$code_album}])}{htmlentities($item_images[{$code_album}]|@json_encode)}{/if}" type="hidden" />
                                        {if !empty($item_images[{$code_album}])}
                                            {assign var = images value = $item_images[{$code_album}]}
                                            {foreach from = $images item = image}
                                                <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
                                                    <img src="{CDN_URL}{$image}">
                                                    <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                                </a>
                                            {/foreach}
                                        {/if}
                                    </div>
                                </div>

                                {assign var = url_select_images value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$attribute_code}_{$selected_option_id}"}

                                <div class="col-lg-3 col-xl-2">
                                    <span data-src="{$url_select_images}" class="col-lg-12 btn btn-sm btn-success btn-select-image-album" data-type="iframe">
                                        <i class="fa fa-images"></i> 
                                        {__d('admin', 'chon_anh')}
                                    </span>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}
            
            <div class="form-group">
                <label>
                    {if !empty($attribute.name)}
                        {$attribute.name}
                    {/if}

                    {if !empty($attribute.required)}
                        <span class="kt-font-danger">*</span>
                    {/if}
                </label>

                {if empty($options_special_selected[$attribute_code])}
                    <div class="clearfix item-attribute-image">
                        <div class="row mb-10 ">
                            <div class="col-lg-9 col-xl-8 special-attribute-select">
                                {$this->AttributeAdmin->generateInput($attribute, $lang)}
                            </div>

                            <div class="col-lg-3 col-xl-2">
                                <button type="button" class="btn btn-sm btn-danger btn-icon btn-circle mr-5 delete-attribute-image">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-brand btn-icon btn-circle collapse-attribute-image" aria-expanded="true">
                                    <i class="fa fa-angle-double-down"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row mb-10 wrap-album collapse show">
                            <div class="col-lg-9 col-xl-8">
                                <div class="list-image-album">
                                    <input id="" name="" value="{if !empty($product.images)}{htmlentities($product.images)}{/if}" type="hidden" />
                                    {if !empty($product.images)}
                                        {assign var = images value = $product.images|json_decode:1}
                                        {foreach from = $images item = image}
                                            <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
                                                <img src="{CDN_URL}{$image}">
                                                <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </a>
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>

                            {assign var = url_select_album value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=album"}

                            <div class="col-lg-3 col-xl-2">
                                <span data-src="{$url_select_album}" class="col-lg-12 btn-sm btn btn-success btn-select-image-album" data-type="iframe">
                                    <i class="fa fa-images"></i> 
                                    {__d('admin', 'chon_anh')}
                                </span>
                            </div>
                        </div>
                    </div>
                {/if}

                <div id="select-attribute-image" class="row">
                    <div class="col-lg-9 col-xl-8 special-attribute-select">
                        {assign var = attribute_image value = $attribute}
                        {$attribute_image.required = 0}
                        {$attribute_image.id = $attribute_code}                             
                        {$this->AttributeAdmin->generateInput($attribute_image, $lang)}
                    </div>
                </div>
            </div>
        {else}
            <div class="form-group">
                <label>
                    {if !empty($attribute.name)}
                        {$attribute.name}
                    {/if}

                    {if !empty($attribute.required)}
                        <span class="kt-font-danger">*</span>
                    {/if}
                </label>

                <div class="clearfix">
                    <div class="row mb-10 ">
                        <div class="col-lg-9 col-xl-8 special-attribute-select">
                            {if !empty($options_special_selected[$attribute_code])}
                                {$attribute.value = $this->UtilitiesAdmin->getArrayKeys($options_special_selected[$attribute_code])}
                            {/if}
                            {$this->AttributeAdmin->generateInput($attribute, $lang)}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {/foreach}

    <div class="form-group">
        <div class="btn-group">
            <button id="add-new-item" type="button" class="btn btn-sm btn-success d-none">
                <i class="la la-plus fs-12"></i>
                {__d('admin', 'them_phien_ban_san_pham')}
            </button>
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="la la-cogs fs-12"></i>
                {__d('admin', 'hanh_dong')}
            </button>

            <div class="dropdown-menu">
                <a class="dropdown-item copy-item" href="javascript:;">
                    <i class="kt-nav__link-icon flaticon2-copy fs-14"></i>
                    {__d('admin', 'sao_chep_thong_tin_phien_ban_dau_tien_cho_cac_phien_ban_con_lai')}
                </a>

                <a class="dropdown-item copy-quantity" href="javascript:;">
                    <i class="kt-nav__link-icon flaticon2-layers-1 fs-14"></i>
                    {__d('admin', 'sao_chep_so_luong_phien_ban_dau_tien_cho_cac_phien_ban_con_lai')}
                </a>

                <a class="dropdown-item copy-price" href="javascript:;">
                    <i class="kt-nav__link-icon la la-money fs-16"></i>
                    {__d('admin', 'sao_chep_gia_phien_ban_dau_tien_cho_cac_phien_ban_con_lai')}
                </a>

                <a class="dropdown-item clear-item" href="javascript:;">
                    <i class="kt-nav__link-icon flaticon2-trash fs-16"></i>
                    {__d('admin', 'xoa_thong_tin_tat_ca_phien_ban')}
                </a>
            </div>
        </div>
    </div>
{/if}

<div class="">
    <table id="table-items" class="table mb-0 nh-table-item">
        <thead class="thead-light">
            <tr>
                {if !empty($attributes_special)}
                    {foreach from = $attributes_special item = attribute}
                        <th class="w-10">
                            {$attribute.name}
                        </th>
                    {/foreach}
                {/if}

                {if !empty($attributes_item)}
                    {foreach from = $attributes_item item = attribute}
                        <th class="w-10">
                            {$attribute.name}
                        </th>
                    {/foreach}
                {/if}
                
                <th class="w-10">
                    {__d('admin', 'ma_san_pham')}
                </th>
                <th class="w-15">
                    {__d('admin', 'gia')}
                </th>
                <th class="text-center w-15">
                    {__d('admin', 'gia_dac_biet')}
                </th>            
                <th class="w-7">
                    {__d('admin', 'san_co')}
                </th>                
                <th class="w-3">
                    
                </th>
            </tr>
        </thead>
        <tbody>
            {if !empty($product.items)}
                {foreach from = $product.items item = item}
                    {$this->element('../Product/items_row', ['item' => $item])}
                {/foreach}
            {else}
                {$this->element('../Product/items_row')}
            {/if}
        </tbody>
    </table>
</div>
