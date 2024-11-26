{strip}
{if !empty($data_block)}
	<div class="swiper swiper-slider-main" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
	    <div class="swiper-wrapper">
	    	{foreach from = $data_block item = slider}
	    		{assign var = image_source value = ''}
				{if !empty($slider.image) && !empty($slider.image_source)}
					{assign var = image_source value = $slider.image_source}
				{/if}

				{assign var = image_url value = ''}
				{if !empty($slider.image) && $image_source == 'cdn'}
					{assign var = image_url value = "{CDN_URL}{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{CDN_URL}{$this->Utilities->getThumbs($slider.image, 350)}"}
					{/if}
				{/if}

				{if !empty($slider.image) && $image_source == 'template'}
					{assign var = image_url value = "{$slider.image}"}
					{if !empty(DEVICE)}
					    {assign var = image_url value = "{$this->Utilities->getThumbs($slider.image, 350, 'template')}"}
					{/if}
				{/if}
				
		        <div class="swiper-slide {if !empty($slider.class_item)}{$slider.class_item}{/if}">
		            {if $slider@first}
		                <img src="{$image_url}" class="img-fluid" alt="{if !empty($slider.name)}{$slider.name}{/if}">
		            {else}
		                <img data-src="{$image_url}" class="swiper-lazy img-fluid" alt="{if !empty($slider.name)}{$slider.name}{/if}">
		                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
		            {/if}
		        	
		        	<div class="swiper-slide--wrap" data-swiper-parallax="-300">
		        		{if !empty($slider.name)}
			        		<div class="swiper-slide--tile">
			        			{$slider.name}
			        		</div>
		        		{/if}
		        		{if !empty($slider.description)}
		        			<div class="swiper-slide--description">
			        			{$slider.description}
			        		</div>
		        		{/if}
		        		{if !empty($slider.url)}
		        			<a class="swiper-slide--link" {if !empty($slider.url)}href="{$this->Utilities->checkInternalUrl($slider.url)}"{/if} title="{if !empty($slider.name)}{$slider.name}{/if}" {if !empty($slider.blank_link)}target="_blank"{/if}>
		        				{__d('template', 'mua_ngay')}
		        			</a>
		        		{/if}
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
	            <i class="fa-light fa-angle-right display-2"></i>
	        </div>
	        <div class="swiper-button-prev">
	            <i class="fa-light fa-angle-left display-2"></i>
        </div>
        {/if}
	</div>
{/if}

{/strip}