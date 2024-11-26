{assign var = all_images value = []}
{if !empty($product.all_images)}
	{assign var = all_images value = $product.all_images}
{/if}

{assign var = config_slider value = [
	'navigation' => [
		'nextEl' => '.swiper-button-next',
		'prevEl' => '.swiper-button-prev'
	]
]}

{assign var = config_slider_thumbs value = [
	'spaceBetween'=> 10,
    'slidesPerView'=> 4,
    'freeMode'=> true,
    'watchSlidesProgress'=> true
]}

<div class="product-image-detail mb-4" nh-swiper-thumb>
	{* Ảnh chính *}
	<div class="product-image-detail-top ">
		<div class="position-relative">
			<div class="swiper mb-3" nh-swiper-large="{htmlentities($config_slider|@json_encode)}">
			    <div class="swiper-wrapper">
			    	{if !empty($all_images)}
			    		{foreach from = $all_images item = image}
					        <div class="swiper-slide inner-image">
					            <img class="img-fluid" src="{CDN_URL}{$image}" alt="{if !empty($product.name)}{$product.name}{/if}">
					        </div>
				        {/foreach}
			    	{/if}
			    </div>

			    {if !empty($config_slider.navigation)}
				    <div class="swiper-button-next">
		                <i class="fa-light fa-angle-right h1"></i>
		            </div>
		            <div class="swiper-button-prev">
		                <i class="fa-light fa-angle-left h1"></i>
		            </div>
			    {/if}
			</div>

			{* Các buttons trên ảnh chính *}
	        <div class="additional-action">
	        	{if !empty($product.url_video) && !empty($product.type_video)}
	        		{if $product.type_video == {VIDEO_YOUTUBE}}
			        	<div nh-light-gallery>
			        		<a href="https://www.youtube.com/watch?v={$product.url_video}" class="btn-addition-action youtube-video btn-video"></a>
			        	</div>
		        	{/if}

		        	{if $product.type_video == {VIDEO_SYSTEM}}
			        	<div nh-light-gallery>
			        		<span class="btn-addition-action btn-video" data-html="#video-product"></span>
			        	</div>

			        	<div id="video-product" style="display:none;">
						    <video class="lg-video-object lg-html5" controls preload="none">
						        <source src="{CDN_URL}{$product.url_video}" type="video/mp4">
						        Your browser does not support HTML5 video.
						    </video>
						</div>
					{/if}
				{/if}

	        	{if !empty($all_images)}
	        		<div nh-expand-light-gallery>
		        		{if !empty($all_images[0])}
			        		<a nh-btn-action="expand" class="btn-addition-action btn-expand" href="javascript:;"></a>
				        	<div nh-light-gallery >
				        		{foreach from = $all_images key = k item = image}
				        			<div class="d-none" data-src="{CDN_URL}{$image}">
					        			<img alt="{if !empty($product.name)}{$product.name}{/if}" src="{CDN_URL}{$this->Utilities->getThumbs($image, 150)}">
					        		</div>				        		
				        		{/foreach}
				        	</div>		
			        	{/if}
		        	</div>		        		
	        	{/if}
	        </div>
        </div>
    </div>

	{* Thumbs *}
	{if !empty($all_images) && $all_images|@count gt 1}
		<div nh-slider-thumbs nh-swiper-thumbs="{htmlentities($config_slider_thumbs|@json_encode)}" class="swiper">
		    <div class="swiper-wrapper">
		    	{foreach from = $all_images item = image}
		    		<div class="swiper-slide">
		    			<div class="ratio-1-1">
			            	<img class="img-fluid" src="{CDN_URL}{$this->Utilities->getThumbs($image, 150)}" alt="{if !empty($product.name)}{$product.name}{/if}">
			            </div>
			        </div>
		    	{/foreach}
		    </div>
		</div>
	{/if}
	        
</div>