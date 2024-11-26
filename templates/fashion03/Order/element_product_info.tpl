<div class="h4 font-weight-bold mb-4">
	{__d('template', 'thong_tin_dat_hang')}
</div>

{if !empty($order_info.items)}
    <div class="scrollbar" style="max-height: 34rem;overflow-x: hidden;">
    	{foreach from = $order_info.items item = item}
            {if !empty($item['images'][0])}
                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item['images'][0], 350)}"}
            {else}
                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
            {/if}

            <div class="row mx-n2 {if !$item@last}mb-4{/if}">
                <div class="col-2 px-2">
                    <div class="ratio-1-1">
            	        <a href="{$this->Utilities->checkInternalUrl($item.url)}">
                            <img class="img-fluid object-contain" src="{$url_img}" alt="{if !empty($item.name_extend)}{$item.name_extend}{/if}" />
                        </a>
                    </div>
                </div>
                
                <div class="col-10 px-2">
                    <div class="top-name-right">
                        <div class="name-element font-weight-bold">
                            <a class="color-main" href="{$this->Utilities->checkInternalUrl($item.url)}">
                                {if !empty($item.name_extend)}
    								{$item.name_extend|truncate:100:" ..."}
    							{/if}
                            </a>
                        </div>
                        <div>
                            {__d('template', 'so_luong')}: 
                            <span>
                                {if !empty($item.quantity)}
                                    {$item.quantity}
                                {else}
                                    1
                                {/if}
                            </span>
                        </div>
                       
                        <div class="price-quantity">
                            <span class="price-amount">
    							{if isset($item.total_item)}
    		            			{$item.total_item|number_format:0:".":","}
    		            			<span class="currency-symbol">
                                        {CURRENCY_UNIT}
                                    </span>
    		            		{/if}
    				        </span>
    					    
    					    {if !empty($item.default_total_item)}
    						    <span class="text-muted">
    						    	( {$item.default_total_item|number_format:0:".":","}
    						    	<span class="currency-symbol">
                                        {CURRENCY_UNIT_DEFAULT}
                                    </span>)
                                </span>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}    
    </div>
{/if}
