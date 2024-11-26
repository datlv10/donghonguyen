<div class="wrap-suggestion">
	{if !empty($products)}
		<div class="font-weight-bold h5 m-3">
			{__d('template', 'san_pham')}
		</div>
	    <ul class="list-unstyled mb-0">
	        {foreach from = $products item = product}
	            {if !empty($product['image'])}
	                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product['image'], 50)}"}
	            {else}
	                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
	            {/if}
	            
	            <li class="px-3 py-2">
	                <a class="row mx-n2 color-main" href="/{$product.url}">
	                	<div class="col-1 px-2">
	                		<div class="ratio-1-1">
								<img src="{$url_img}" alt="{$product.name}" class="img-fluid">
							</div>
						</div>
	                	<div class="col-11 px-2">
	                		{if !empty($product.name)}
	                    		<div>
	                    			{$product.name|truncate:45:" ..."}
	                    		</div>
	                		{/if}
	            			<div class="price suggest-price">
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
	                </a>
	            </li>
	        {/foreach}
	    </ul>
	{/if}

	{if !empty($articles)}
		<div class="font-weight-bold h5 m-3">
			{__d('template', 'tin_tuc')}
		</div>
	    <ul class="list-unstyled mb-0">
	        {foreach from = $articles item = article}
	            {if !empty($article['image'])}
	                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($article['image'], 50)}"}
	            {else}
	                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
	            {/if}
	            <li class="px-3 py-2">
	                <a class="row mx-n2 color-main" href="/{$article.url}">
	                	<div class="col-1 px-2">
	                		<div class="ratio-1-1">
								<img src="{$url_img}" alt="{$article.name}" class="img-fluid">
							</div>
						</div>
	                	<div class="col-11 px-2">
	                		{if !empty($article.name)}
	                    		<div>
	                    			{$article.name|truncate:45:" ..."}
	                    		</div>
	                		{/if}
	                	</div>
	                </a>
	            </li>
	        {/foreach}
	    </ul>
	{/if}
</div>