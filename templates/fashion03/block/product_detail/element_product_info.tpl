{assign var = first_item value = []}
{if !empty($product.items[0])}
    {assign var = first_item value = $product.items[0]}
{/if}

{assign var = rating value = 0}
{if !empty($product.rating)}
    {math assign = rating equation = 'x*y' x = $product.rating y = 20} 
{/if}

<div nh-product-detail nh-product="{if !empty($product.id)}{$product.id}{/if}" nh-product-item-id="{if !empty($first_item)}{$first_item.id}{/if}" nh-product-attribute-special="{if !empty($product.attributes_item_special)}{htmlentities($product.attributes_item_special|@json_encode)}{/if}" class="product-content-detail">

    {if !empty($product.name)}
		<h2 class="product-title-detail mb-3">
			{$product.name|escape}
            <span nh-label-extend-name>{if !empty($first_item.extend_name)}({$first_item.extend_name}){/if}</span>
        </h2>
    {/if}

    {* Điểm số đánh giá và bình luận*}
    <div class="product-rating d-flex align-items-center flex-nowrap mb-4">
        
        <div class="star-rating mr-1">
            <span style="width:{$rating}%"></span>
        </div>
        {if !empty($product.rating_number)}
            (<span class="count mr-1">
                {$product.rating_number|number_format:0:".":","}
            </span> {__d('template', 'danh_gia')})
        {/if}

        <div class="review-link ml-3">
            {if !empty($product.comment)}
                (<span class="count">
                    {$product.comment|number_format:0:".":","}
                </span> 
                {__d('template', 'khach_hang_da_binh_luan')})
            {/if}
        </div>
    </div>

    {* Giá sản phẩm*}
    <div class="price mb-4">
    	{if empty($first_item.apply_special) && !empty($first_item.price)}
            <span nh-label-price="{$first_item.price}" class="price-amount">
                <span nh-label-value>
                    {$first_item.price|number_format:0:".":","}
                </span>                    
                <span class="currency-symbol">{CURRENCY_UNIT}</span>
            </span>
        {/if}

        {if !empty($first_item.apply_special) && !empty($first_item.price_special)}
        	<span nh-label-price="{$first_item.price_special}" class="price-amount">
                <span nh-label-value>
                    {$first_item.price_special|number_format:0:".":","}
                </span>                    
                <span class="currency-symbol">{CURRENCY_UNIT}</span>
            </span>
        {/if}

        {assign var = old_price value = ""}
        {assign var = show_old_price value = "d-none"}
        {if !empty($first_item.price) && !empty($first_item.apply_special)}
            {assign var = old_price value = $first_item.price}
            {assign var = show_old_price value = ""}
        {/if}
        <span nh-label-price-special="{$old_price}" class="price-amount old-price {$show_old_price}">
            <span nh-label-value>
                {if !empty($first_item.price) && !empty($first_item.apply_special)}
                    {$first_item.price|number_format:0:".":","}
                {/if}
            </span>
            <span class="currency-symbol">{CURRENCY_UNIT}</span>
        </span>
    </div>

    {* Thông tin chính *}    
	{if !empty($first_item.code)}
        <div class="code mb-2">
            <label>
                {__d('template', 'ma_san_pham')}:
            </label>
            <span nh-label-code="{$first_item.code}">
            	{$first_item.code}
            </span>
        </div>
    {/if}

    {if !empty($product.categories)}
        <div class="product-category mb-2">
        	<label>
                {__d('template', 'danh_muc')}:
            </label>
            {foreach from = $product.categories item = category}
            	{if !empty($category.status)}
                    <a {if !empty($category.url)}href="{$this->Utilities->checkInternalUrl($category.url)}"{/if} 
                    	target="_blank">
                        {if !empty($category.name)}
                            {$category.name|escape}
                        {/if}                            
                    </a>
                    {if !$category@last}
                        <span class="comma-item">, </span>
                    {/if}
            	{/if}
            {/foreach} 
        </div>
    {/if}

    {if !empty($product.brand_name)}
        <div class="brand mb-2">
            <label>
                <b>{__d('template', 'thuong_hieu')}:</b>
            </label>
            <span>
            	{$product.brand_name}
            </span>
        </div>
    {/if}

    {if !empty($product.width) || !empty($product.length) || !empty($product.height) || !empty($product.weight)}
        <div class="row">
        	{if !empty($product.weight)}
        		<div class="col-6">
                    <div class="weight mb-2">
                        <label>
                            {__d('template', 'can_nang')}:
                        </label>
                        <span>{$product.weight}</span>
                        <span class="currency-symbol">{$product.weight_unit}</span>
                    </div>
                </div>
        	{/if}
        	{if !empty($product.length)}
        		<div class="col-6">
                    <div class="length mb-2">
                        <label>
                            {__d('template', 'chieu_dai')}:
                        </label>
                        <span>{$product.length}</span>
                        <span class="currency-symbol">{$product.length_unit}</span>
                    </div>
            	</div>		
        	{/if}
            {if !empty($product.width)}
            	<div class="col-6">
                    <div class="width mb-2">
                        <label>
                            {__d('template', 'chieu_rong')}:
                        </label>
                        <span>{$product.width}</span>
                        <span class="currency-symbol">{$product.width_unit}</span>
                    </div>
            	</div>
            {/if}
            {if !empty($product.height)}
            	<div class="col-6">
                    <div class="height mb-2">
                        <label>
                            {__d('template', 'chieu_cao')}:
                        </label>
                        <span>{$product.height}</span>
                        <span class="currency-symbol">{$product.height_unit}</span>
                    </div>
            	</div>
            {/if}
        </div>
    {/if}

    {* Thuộc tính phiên bản sản phẩm*}
    {$this->element("../block/product_detail/element_attribute_item", [
        'product' => $product,
        'first_item' => $first_item
    ])}
    {* Thêm giỏ hàng *}
    {if !empty($product.status) && $product.status == 1}
        <div class="d-flex flex-wrap mb-4">
            <div class="d-flex">
                {$this->element('input_quantity', [
                    'first_item' => $first_item,
                    'check_quantity' => true
                ])}
                <div class="btn-cart-buy d-flex flex-wrap">
                    <a nh-btn-action="add-cart" href="javascript:;" class="add-to-cart ml-3 mr-3 {if (isset($first_item.quantity_available) && $first_item.quantity_available <= 0 || empty($first_item.quantity_available)) && !empty($data_init.product.check_quantity)}d-none{/if}">
                        {__d('template', 'them_gio_hang')}
                    </a>
                    {* <a nh-btn-action="add-cart" nh-redirect="/order/info" href="javascript:;" class="add-to-cart add-to-cart-buy mr-3">
                        {__d('template', 'thanh_toan_ngay')}
                    </a> *}
                </div>
            </div>

            <div class="product-action-detail d-flex flex-wrap">
                <a nh-btn-action="wishlist" wishlist-id="{if !empty($product.id)}{$product.id}{/if}" wishlist-type="{PRODUCT}" class="btn-product-action mr-3" href="javascript:;" title="{__d('template', 'yeu_thich')}">
                    <i class="fa-light fa-heart"></i>
                </a>
                
                <a nh-btn-action="compare" data-product-id="{if !empty($product.id)}{$product.id}{/if}" class="btn-product-action" href="javascript:;" title="{__d('template', 'so_sanh')}">
                    <i class="fa-light fa-retweet"></i>
                </a>
            </div>
        </div>

        <div nh-quantity-product="out-stock" class="out-of-stock mb-4 {if (!empty($first_item.quantity_available) && $first_item.quantity_available > 0) || empty($data_init.product.check_quantity)}d-none{/if}">
            {__d('template', 'san_pham_het_hang')}
        </div>    	
    {else}
        <div  class="out-of-stock mb-4">
            {__d('template', 'san_pham_ngung_kinh_doanh')}
        </div>
    {/if}

    {* Chia sẻ mạng xã hội *}
    {if !empty($product.url)}
        {assign var = url_product value = "{$this->Utilities->getUrlWebsite()}{$this->Utilities->checkInternalUrl($product.url)}"}
        <div class="social-share d-flex align-items-center flex-wrap mt-5">
            <span class="share-title">
                <label class="mb-0">
                    {__d('template', 'chia_se')}:
                </label>
            </span>

            <div class="list-social">
                <div class="btn-social">
                    <a href="javascript:;" nh-link-redirect="https://www.facebook.com/sharer/sharer.php?u={$url_product}" nh-link-redirect-blank title="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>

                <div class="btn-social">
                    <a href="javascript:;" nh-link-redirect="https://twitter.com/share?url={$url_product}" nh-link-redirect-blank title="Twitter">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                </div>

                <div class="btn-social">
                    <a href="javascript:;" nh-link-redirect="https://plus.google.com/share?url={$url_product}" nh-link-redirect-blank title="Google+">
                        <i class="fa-brands fa-google-plus-g"></i>
                    </a>
                </div>

                <div class="btn-social">
                    <a href="javascript:;" nh-link-redirect="https://pinterest.com/pin/create/button/?url={$url_product}" nh-link-redirect-blank title="Pinterest">
                        <i class="fa-brands fa-pinterest-p"></i>
                    </a>
                </div>

                <div class="btn-social">
                    <a href="javascript:;" nh-link-redirect="https://www.linkedin.com/shareArticle?mini=true&amp;url={$url_product}" nh-link-redirect-blank title="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    {/if}
</div>