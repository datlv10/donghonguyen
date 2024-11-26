{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{assign var = rating value = 0}
{if !empty($product.rating)}
    {math assign = rating equation = 'x*y' x = $product.rating y = 20} 
{/if}

{if empty($is_slider)}
<div class="{if !empty($col)}{$col}{else}col-lg-3 col-md-6 col-6{/if}">
{/if}
    <div nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($product.items[0])}{$product.items[0].id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-item swiper-slide clearfix mb-4">
        <div class="inner-image ">
            {if !empty($product['all_images'][0])}
                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['all_images'][0], 350)}"}
            {else}
                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
            {/if}

            <a class="ratio-1-1 d-block" href="{if !empty($product.url)}{$this->Utilities->checkInternalUrl($product.url)}{/if}" 
                title="{if !empty($product.name)}{$product.name}{/if}">
                {$this->LazyLoad->renderImage([
                    'src' => $url_img, 
                    'alt' => "{if !empty($product.name)}{$product.name}{/if}",
                    'class' => 'img-fluid swiper-lazy',
                    'ignore' => $ignore
                ])}
            </a>
        </div>

        <div class="inner-content">
            {if !empty($product.name)}
                <div class="product-title">
                    <a href="{if !empty($product.url)}{$this->Utilities->checkInternalUrl($product.url)}{/if}">
                        {$product.name|escape|truncate:50:" ..."}
                    </a>
                </div>
            {/if}
            <div class="rating-price">  
                <div class="star-rating">
                    <span style="width:{$rating}%"></span>
                </div>

                <div class="price">
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
{if empty($is_slider)}
</div>
{/if}
{/strip}