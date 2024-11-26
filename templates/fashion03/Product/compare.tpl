{assign var = max_item value = 6} {* so luong hien thi tren pc*}
{assign var = max_item_mobile value = 3} {* so luong hien thi tren mobile*}

{if !empty($data_block.data)}
	{assign var = compare value = $data_block.data}
{/if}

{if !empty(DEVICE)}
	{assign var = max_item value = $max_item_mobile}
{/if}

{if !empty($compare)}
<div nh-compare="warp">
	<div compare-content class="compare-content">
		<div class="compare-content-inner">
			<a nh-compare="close" href="javascript:;" class="compare-content-close effect-rotate icon-close">
				<i class="fa-light fa-xmark"></i>
			</a>

			<div class="sidebar-sticky sticky-title-compare">
				<div class="row mx-0">
					{for $i = 0 to $max_item - 1}
						<div class="col px-0">
							<div class="inner-compare p-2">								
								{if !empty($compare[$i]) && !empty($compare[$i].id)}
									{assign var = product_info value = $compare[$i]}
									<div class="text-center">
										<a href="javascript:;" {if !empty($product_info.id)}compare-remove="{$product_info.id}"{/if} class="compare-bar-remove icon-close">
											<i class="fa-light fa-circle-xmark"></i>
										</a>
									</div>
								{/if}
		                    </div>
						</div>	
					{/for}
				</div>
				<div class="row mx-0">
					{for $i = 0 to $max_item - 1}
						<div class="col px-0">
							<div class="inner-compare pt-0">								
								{if !empty($compare[$i]) && !empty($compare[$i].id)}
									{assign var = product_info value = $compare[$i]}
									<div class="d-flex flex-column h-100 flex-wrap">
										<div class="product-title">
											<a href="{if !empty($product_info.url)}{$this->Utilities->checkInternalUrl($product_info.url)}{/if}">
												{if !empty($product_info.name)}{$product_info.name}{/if}
											</a>
										</div>

										<div class="star-rating mb-3">
											<span style="width:100%"></span>
										</div>

										<div class="mt-auto" nh-product="{if !empty($product_info.id)}{$product_info.id}{/if}" nh-product-item-id="{if !empty($product_info.items[0])}{$product_info.items[0].id}{/if}">

											{if !empty($product_info.total_quantity_available) || empty($data_init.product.check_quantity)}

							                    {assign var = add_cart value = "nh-btn-action='add-cart'"}
							                    {assign var = title_cart value = "{__d('template', 'them_gio_hang')}"}
							                    {if !empty($product_info.attributes_item_special) && ($product_info.number_item gte 2)}
							                        {assign var = title_cart value = "{__d('template', 'xem_chi_tiet')}"}
							                    {/if}

							                    {assign var = link_cart value = "javascript:;"}
							                    {if !empty($product_info.attributes_item_special) && !empty($product_info.url) && ($product_info.number_item gte 2)}
							                        {assign var = link_cart value = "{$this->Utilities->checkInternalUrl($product_info.url)}"}
							                        {assign var = add_cart value = ""}
							                    {/if}

							                    <a {$add_cart} class="btn btn-product-action w-100 btn-submit" href="{$link_cart}" title="{$title_cart}">
							                        {$title_cart}
							                    </a>                    
							                {else}
							                    <a class="btn btn-product-action w-100 btn-submit" href="{$this->Utilities->checkInternalUrl($product_info.url)}" title="{__d('template', 'xem_chi_tiet')}">
							                        {__d('template', 'xem_chi_tiet')}
							                    </a>
							                {/if}

						                    {if isset($product_info.total_quantity_available) && $product_info.total_quantity_available <= 0 && !empty($data_init.product.check_quantity)}
						                        <div class="font-danger">
						                            ({__d('template', 'het_hang')})
						                        </div>
						                    {/if}
					                    </div>
									</div>
			                    {else}
									<a href="javascript:;" class="placeholder-add" data-toggle="modal" data-target="#compare-search-modal">
										<i class="fa-light fa-plus"></i>
									</a>
								{/if}
		                    </div>
						</div>	
					{/for}
				</div>
			</div>

			<div class="row mx-0">
				{for $j = 0 to $max_item - 1}
					<div class="col px-0">
						<div class="inner-compare">
							{if !empty($compare[$j]) && !empty($compare[$j].id)}
								{assign var = product_info value = $compare[$j]}
							  	<div class="inner-image wrp-effect-change-img ratio-1-1">
								    <div class="product-status">
								      	{if !empty($product_info.apply_special) && !empty($product_info.discount_percent)}
						                    <span class="onsale">
						                        -{$product_info.discount_percent}%
						                    </span>
						                {/if}
						                
						                {if !empty($product_info.featured)}
						                    <span class="featured">
						                        {__d('template', 'noi_bat')}
						                    </span>
						                {/if}
						                
						                {if isset($product_info.total_quantity_available) && $product_info.total_quantity_available <= 0 && !empty($data_init.product.check_quantity)}
						                    <span class="out-stock">
						                        {__d('template', 'het_hang')}
						                    </span>
						                {/if}
									</div>
									{if !empty($product_info['all_images'][0])}
						                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($product_info['all_images'][0], 500)}"}
						            {else}
						                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
						            {/if}

						            <a href="{$this->Utilities->checkInternalUrl($product_info.url)}" title="{$product_info.name}">
						            	<img class="img-fluid" src="{$url_img}" alt="{$product_info.name}">
						            </a>

						            {if !empty($product_info['all_images'][1])}
						                <div class="effect-change-img">
						                    <a href="{$this->Utilities->checkInternalUrl($product_info.url)}" title="{$product_info.name}">
						                    	<img class="img-fluid" src="{CDN_URL}{$this->Utilities->getThumbs($product_info['all_images'][1], 350)}" alt="{$product_info.name}">
						                    </a>
						                </div>
						            {/if}
								</div>
							{else}
								<div class="placeholder-empty"></div>
							{/if}
						</div>
					</div>
				{/for}
			</div>

			<div class="title-compare">
				<b>{__d('template', 'mo_ta')}</b>
			</div>
			<div class="row mx-0">
				{for $k = 0 to $max_item - 1}
					<div class="col px-0">
						<div class="inner-compare">
							{if !empty($compare[$k]) && !empty($compare[$k].id)}
								{assign var = product_info value = $compare[$k]}
								{if !empty($product_info.description)}
									{$product_info.description}
								{/if}
							{else}
								<div class="placeholder-empty"></div>
							{/if}
						</div>		
					</div>
				{/for}
			</div>
			<div class="title-compare">
				<b>{__d('template', 'gia_va_phien_ban_san_pham')}</b>
			</div>

			<div class="row mx-0">
				{for $e = 0 to $max_item - 1}
					<div class="col px-0">
						<div class="inner-compare">
							{if !empty($compare[$e]) && !empty($compare[$e].id)}
								{assign var = product_info value = $compare[$e]}
								{foreach from = $product_info.items item = item}
									{if empty($item)}{continue}{/if}
									<div {if !$item@last}class="border-bottom mb-3 pb-3{/if}">
										<div>
											<label>{__d('template', 'ma_san_pham')}:</label>
				                            <span>
				                                {if !empty($item.code)}
				                                    {$item.code}
				                                {else}
				                                    ...
				                                {/if}
				                            </span>
				                        </div>

				                        {if empty($item.apply_special) && !empty($item.price)}
				                            <div>
				                            	<label>{__d('template', 'gia')}:</label>
				                                <span>
				                                    {$item.price|number_format:0:".":","}
				                                </span>                    
				                            </div>
				                        {/if}

				                        {if !empty($item.price) && !empty($item.apply_special)}
				                            <div>
				                                <label>{__d('template', 'gia')}:</label>
				                                <span>
				                                    {$item.price|number_format:0:".":","}
				                                </span>                    
				                            </div>
				                        {/if}

				                        {if !empty($item.apply_special) && !empty($item.price_special)}
				                            <div>
				                                <label>{__d('template', 'gia_dac_biet')}:</label>
				                                <span>
				                                    {$item.price_special|number_format:0:".":","}
				                                </span>                    
				                            </div>
				                            {if !empty($item.date_special)}
				                                <div>
				                                    <label>{__d('template', 'ngay_khuyen_mai')}:</label>
				                                    <span>
				                                        {$item.date_special}
				                                    </span>                    
				                                </div>
				                            {/if}
				                        {/if}
				                        
				                        {if !empty($item.images)}
					                        <div>
					                            <label>{__d('template', 'anh_san_pham')}:</label>
					                            <div class="row mx-n1 flex-nowrap overflow-hidden" nh-light-gallery>
					                                {foreach from = $item.images item = image_item}
					                                    <a class="col-3 px-1 mt-2" data-lightbox="album-{$item.id}" href="{CDN_URL}{$image_item}">
					                                        <img class="img-cover img-thumbnail" src="{CDN_URL}{$this->Utilities->getThumbs($image_item, 150)}" alt="{if !empty($product_info.name)}{$product_info.name}{/if}">
					                                    </a>
					                                {/foreach}
					                            </div>
					                        </div>
				                        {/if}

				                        <div>
				                            <label>{__d('template', 'so_luong_san_co')}:</label>
				                            <span>
				                                {if !empty($item.quantity_available)}
				                                    {$item.quantity_available}
				                                {else}
				                                    0
				                                {/if}
				                            </span>
				                        </div>
				                    </div>
								{/foreach}
							{else}
								<div class="placeholder-empty"></div>
							{/if}
						</div>
					</div>
				{/for}
			</div>
		</div>
	</div>
	<div compare-bar class="compare-bar d-flex align-items-center justify-content-between">
		<a href="javascript:;" compare-bar="close" class="compare-bar-close effect-rotate icon-close">
			<i class="fa-light fa-xmark"></i>
		</a>
		<div class="d-flex justify-content-end">
			<div class="compare-bar-items">
				{foreach from = $compare item = bar}
					{if !empty($bar['all_images'][0])}
		                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($bar['all_images'][0], 150)}"}
		            {else}
		                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
		            {/if}
			    	<div class="compare-bar-item">
			    		<img src="{$url_img}" alt="{if !empty($bar.name)}{$bar.name}{/if}">
			    		<a href="javascript:;" {if !empty($bar.id)}compare-remove="{$bar.id}"{/if} class="compare-bar-remove icon-close">
			    			<i class="fa-light fa-xmark"></i>
			    		</a>
			    	</div>
		    	{/foreach}
			</div>
			<div compare-bar="btn" class="compare-bar-btn">
				<i class="fa-light fa-bars"></i>
				<span>{__d('template', 'so_sanh_san_pham')}</span>
			</div>
		</div>
	</div>
</div>
{else}
	{__d('template', 'khong_co_du_lieu')}
{/if}
