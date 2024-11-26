{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
<div nh-wishlist="reload" class="{if !empty($col)}{$col}{else}col-lg-3 col-md-6 col-6{/if}">
    <div nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($product.items[0])}{$product.items[0].id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-item swiper-slide">
        <div class="inner-image mb-3">
            <div class="product-status">
                {if !empty($product.apply_special) && !empty($product.discount_percent)}
                    <span class="onsale">
                        -{$product.discount_percent}%
                    </span>
                {/if}
                
                {if !empty($product.featured)}
                    <span class="featured">
                        {__d('template', 'noi_bat')}
                    </span>
                {/if}
                
                {if isset($product.total_quantity_available) && $product.total_quantity_available <= 0 && !empty($data_init.product.check_quantity)}
                    <span class="out-stock">
                        {__d('template', 'het_hang')}
                    </span>
                {/if}
            </div>
            <div class="ratio-custome">
                {if !empty($product['all_images'][0])}
                    {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['all_images'][0], 500)}"}
                {else}
                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}

                <a href="{$this->Utilities->checkInternalUrl($product.url)}" title="{$product.name}">
                    {$this->LazyLoad->renderImage([
                        'src' => $url_img, 
                        'alt' => $product.name, 
                        'class' => 'img-fluid',
                        'ignore' => $ignore
                    ])}
                </a>
            </div>

            <div class="product-action">
                <a nh-btn-action="wishlist" wishlist-id="{if !empty($product.id)}{$product.id}{/if}" wishlist-type="{PRODUCT}" class="btn-product-action" href="javascript:;" title="{__d('template', 'yeu_thich')}">
                    <i class="fa-light fa-heart"></i>
                </a>
            </div>
        </div>
        
        <div class="inner-content text-center">
            {if !empty($product.name)}
                <div class="product-title">
                    <a href="{$this->Utilities->checkInternalUrl($product.url)}">
                        {$product.name|escape|truncate:50:" ..."}
                    </a>
                </div>
            {/if}

            <div class="price mt-2">                        
                <span class="price-amount">
                    {if empty($product.apply_special) && !empty($product.price)}
                        {$product.price|number_format:0:".":","}
                        <span class="currency-symbol">{CURRENCY_UNIT}</span>
                    {/if}

                    {if !empty($product.apply_special) && !empty($product.price_special)}
                        {$product.price_special|number_format:0:".":","}
                        <span class="currency-symbol">{CURRENCY_UNIT}</span>
                    {/if}
                </span>                        

                {if !empty($product.apply_special) && !empty($product.price)}
                    <span class="price-amount old-price">
                        {$product.price|number_format:0:".":","}
                        <span class="currency-symbol">{CURRENCY_UNIT}</span>
                    </span>
                {/if}
            </div>
        </div>      
    </div>
</div>

{/strip}