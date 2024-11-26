{if !empty($value)}
	{assign var = all_images value = $value|json_decode:true}
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
	{if !empty($all_images)}
	    <div class="my-3" nh-swiper-thumb>
		    {* Ảnh chính *}
	    	<div class="position-relative">
		    	<div class="swiper mb-3" nh-swiper-large="{htmlentities($config_slider|@json_encode)}">
		    		<div class="swiper-wrapper">
				    	{if !empty($all_images)}
				    		{foreach from = $all_images item = image}
						        <div class="swiper-slide">
						        	<div class="ratio-3-2">
						            	<img class="img-fluid" src="{CDN_URL}{$image}" alt="{if !empty($article_info.name)}{$article_info.name}{/if}">
						            </div>
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
		        	{if !empty($all_images)}
			        	<div nh-light-gallery>
			        		<a class="btn-addition-action btn-expand" href="{CDN_URL}{$all_images[0]}">
			        			<img src="{CDN_URL}{$this->Utilities->getThumbs( $all_images[0], 150)}" class="d-none">
			        		</a>
			        		{foreach from =  $all_images key = k item = image}
			        			{if $k > 0}
				        			<div class="d-none" data-src="{CDN_URL}{$image}">
					        			<img src="{CDN_URL}{$this->Utilities->getThumbs($image, 150)}">
					        		</div>
			        			{/if}						        		
			        		{/foreach}
			        	</div>				        		
		        	{/if}
		        </div>
	        </div>

	        {* Thumbs *}
			{if !empty($all_images) && $all_images|@count gt 1}
				<div nh-slider-thumbs nh-swiper-thumbs="{htmlentities($config_slider_thumbs|@json_encode)}" class="swiper">
				    <div class="swiper-wrapper">
				    	{foreach from = $all_images item = image}
				    		<div class="swiper-slide">
				    			<div class="ratio-1-1">
					            	<img class="img-fluid" src="{CDN_URL}{$this->Utilities->getThumbs($image, 350)}">
					            </div>
					        </div>
				    	{/foreach}
				    </div>
				</div>
			{/if}
	    </div>
    {/if} 

{/if}