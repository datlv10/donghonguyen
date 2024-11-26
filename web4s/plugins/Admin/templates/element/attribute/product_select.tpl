{if !empty($code)}
    {assign var = products value = []}
    {if !empty($value)}
        {$products = $value|json_decode:1}
    {/if}
    <div class="wrap-auto-suggest">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="flaticon-search w-20px"></i>
                            </span>
                        </div>
                        <input id="{$code}-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_san_pham')}" autocomplete="off" input-attribute="{PRODUCT_SELECT}" input-attribute-code="{$code}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa fa-layer-group w-20px"></i>
                    </span>
                </div>
                <div id="wrap-data-selected" class="form-control form-control-sm clearfix mh-35 tagify h-auto" style="padding:2px 0 !important;">
                    {if !empty($products)}
                        {foreach from = $products item = product_id}
                            {assign var = product_info value = $this->ProductAdmin->getDetailProduct($product_id, LANGUAGE_ADMIN)}
                            <span class="tagify__tag">
                                <x class="tagify__tag__removeBtn" role="button"></x>
                                <div>
                                    <span class="tagify__tag-text">
                                        {if !empty($product_info.name)}
                                            {$product_info.name}
                                        {/if}
                                    </span>
                                </div>
                                <input name="{$code}[]" value="{if !empty($product_info.id)}{$product_info.id}{/if}" type="hidden">
                            </span>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </div>
	
{/if}