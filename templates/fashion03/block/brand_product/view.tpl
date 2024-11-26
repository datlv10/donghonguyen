{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section mb-4">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.data)}
	<div class="swiper" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
	    <div class="swiper-wrapper">
	    	{foreach from = $data_block.data item = brand}
				{assign var = image_url value = ''}
				{if !empty($brand.image_avatar)}
					{assign var = image_url value = "{CDN_URL}{$this->Utilities->getThumbs($brand.image_avatar, 350)}"}
				{/if}

		        <div class="swiper-slide">
		            <div class="p-4 border">
    		        	<a class="ratio-16-9 d-block" href="{if !empty($brand.url)}{$brand.url}{/if}" alt="{if !empty($brand.name)}{$brand.name}{/if}">
    		        		{$this->LazyLoad->renderImage([
		                        'src' => $image_url, 
		                        'alt' => $brand.name, 
		                        'class' => 'img-fluid object-contant'
		                    ])}
    		        	</a>
		        	</div>
		        </div>
	        {/foreach}
	    </div>
	    {if !empty($data_extend.slider.pagination)}
		    <!-- If we need pagination -->
		    <div class="swiper-pagination"></div>
	    {/if}

	    {if !empty($data_extend.slider.navigation)}
		    <!-- If we need navigation buttons -->
		    <div class="swiper-button-next">
	            <i class="fa-light fa-angle-right"></i>
	        </div>
	        <div class="swiper-button-prev">
	            <i class="fa-light fa-angle-left"></i>
        </div>
        {/if}
	</div>
{/if}

{/strip}