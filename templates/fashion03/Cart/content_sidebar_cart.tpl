{strip}
{assign var = cart_info value = $this->Cart->getCartInfo()}
{assign var = items value = []}
{assign var = total_quantity value = 0}

{if !empty($cart_info.total_quantity)}
	{assign var = total_quantity value = $cart_info.total_quantity}
{/if}
{if !empty($cart_info['items'])}
	{assign var = items value = $cart_info['items']}
{/if}

<div class="box-minicart " nh-total-quantity-cart="{$total_quantity}">
	<ul class="cart-list list-unstyled mb-0">
		{if !empty($items)}
			{foreach from = $items item = item key = product_item_id}
				<li nh-cart-item="{$product_item_id}" nh-cart-item-quantity="{if !empty($item.quantity)}{$item.quantity}{/if}" class="cart-item clearfix">
					<div class="inner-image">
						{if !empty($item['images'][0])}
			                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item['images'][0], 150)}"}
			            {else}
			                {assign var = url_img value = "{URL_TEMPLATE}/assets/img/no-image.png"}
			            {/if}

				        <a href="{$this->Utilities->checkInternalUrl($item.url)}" title="{if !empty($item.name_extend)}{$item.name_extend}{/if}">
				            <img class="img-fluid" src="{$url_img}" alt="{if !empty($item.name_extend)}{$item.name_extend}{/if}">
				        </a>
				    </div>

				    <div class="inner-content">
				    	{if !empty($item.name_extend)}
			                <a class="product-title" href="{$this->Utilities->checkInternalUrl($item.url)}">
			                    {$item.name_extend|escape|truncate:80:" ..."}
			                </a>
		                {/if}

			            <div class="quantity">
			            	<span class="mr-2">
				            	{if isset($item.quantity)}
				            		{$item.quantity}
				            	{/if}
				            </span>
			            	x
			            	<span class="price-amount ml-2">
			            		{if isset($item.price)}
			            			{$item.price|number_format:0:".":","}
			            			<span class="currency-symbol">{CURRENCY_UNIT}</span>
			            		{/if}
			            		{if !empty($item.default_price)}
								    <span class="d-inline form-text text-muted ml-5 fs-12">
								    	( {$item.default_price|number_format:0:".":","} 
								    	<span class="currency-symbol fs-12">{CURRENCY_UNIT_DEFAULT}</span> )
			                        </span>
			                    {/if}
			            	</span>
			            </div>
			            <div class="mt-2">
			                <a href="javascript:;" class="color-highlight" nh-remove-item-cart="{if !empty($product_item_id)}{$product_item_id}{/if}" >
	                            {__d('template', 'xoa')}
	                        </a>
			            </div>
			    	</div>
				</li>
			{/foreach}
		{else}
			<li class="empty text-center">
				<i class="fa-brands fa-opencart"></i>
				<div class="empty-cart">
					{__d('template', 'chua_co_san_pham_nao_trong_gio_hang')}
				</div>
			</li>
		{/if}
	</ul>

	{if !empty($items)}
		<div class="entire-bottom-minicart">
			<div class="total-price clearfix">
				<label>{__d('template', 'gia_tam')}: </label>
				<p class="price-amount">
	        		{if isset($cart_info.total)}
	            		{$cart_info.total|number_format:0:".":","}
	            		<span class="currency-symbol">{CURRENCY_UNIT}</span>

	            		{if !empty($cart_info.total_default)}
						    <span class="form-text text-muted">
						    	( {$cart_info.total_default|number_format:0:".":","} 
						    	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span> )
	                        </span>
	                    {/if}
	            	{/if}
	        	</p>
			</div>
			
			<div class="mini-cart-btn">
				<a href="/order/cart-info" class="btn btn-cart-info btn-submit">
					{__d('template', 'xem_gio_hang')}
				</a>
				
				<a href="/order/info" class="btn btn-checkout btn-submit">
					{__d('template', 'thanh_toan')}
				</a>
			</div>
		</div>
	{/if}
</div>
{/strip}