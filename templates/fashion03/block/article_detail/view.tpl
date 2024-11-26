{strip}
{assign var = article_info value = []}
{if !empty($data_block.data)}
	{assign var = article_info value = $data_block.data}
{/if}
{assign var = all_images value = []}
{if !empty($article_info.images)}
	{assign var = all_images value = $article_info.images}
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
{if !empty($article_info)}
	<article class="article-detail">
		{if !empty($article_info.name)}
			<div class="h3 font-weight-bold mb-3">
				{$article_info.name|escape}
			</div>
		{/if}

		<div class="mb-3 post-date-details">
		    {if !empty($article_info.created_by_user)}
		        {__d('template', 'tac_gia')}
		        <span class="font-weight-bold ml-2 mr-2 pr-2 border-right border-gray">
		            {$article_info.created_by_user}
		        </span>
		    {/if}
	        {if !empty($article_info.created)}
	            <span class="post-date">
	            	{$article_info.created}
	        	</span>
	        {/if}
	    </div>

	    {if !empty($all_images)}
		    <div class="article-image-detail mb-3 " nh-swiper-thumb>
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
				        		{if !empty( $article_info.images[0])}
					        		<a class="btn-addition-action btn-expand" href="{CDN_URL}{$all_images[0]}">
					        			<img alt="{if !empty($article_info.name)}{$article_info.name}{/if}" src="{CDN_URL}{$this->Utilities->getThumbs( $all_images[0], 150)}" class="d-none">
					        		</a>
				        		{/if}

				        		{foreach from =  $all_images key = k item = image}
				        			{if $k > 0}
					        			<div class="d-none" data-src="{CDN_URL}{$image}">
						        			<img alt="{if !empty($article_info.name)}{$article_info.name}{/if}" src="{CDN_URL}{$this->Utilities->getThumbs($image, 150)}">
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
						            	<img class="img-fluid" src="{CDN_URL}{$this->Utilities->getThumbs($image, 350)}" alt="{if !empty($article_info.name)}{$article_info.name}{/if}">
						            </div>
						        </div>
					    	{/foreach}
					    </div>
					</div>
				{/if}
		    </div>
	    {/if} 

	    {if !empty($article_info.url_video) && !empty($article_info.type_video)}
		    <div class="mb-3">
		        {if $article_info.type_video == {VIDEO_YOUTUBE}}
		            <div class="ratio-16-9">
		                <iframe nh-lazy="iframe" data-src="https://www.youtube.com/embed/{$article_info.url_video}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		            </div>                   
		        {/if}

		        {if $article_info.type_video == {VIDEO_SYSTEM}}
		        	<div class="ratio-16-9">
		        		<video nh-lazy="video" data-src="{CDN_URL}{$article_info.url_video}|video/{$article_info.url_video|pathinfo:$smarty.const.PATHINFO_EXTENSION}" controls></video>
		        	</div>	            
		        {/if}
		    </div>
	    {/if}

		<div class=" mb-3" {if !empty($article_info.catalogue)}nh-table-content="content"{/if}>
		    {if !empty($article_info.description)}
			    <div class="font-weight-bold">
			    	{$article_info.description}
			    </div>
		    {/if}
		    
		    {if !empty($article_info.content)}
			    <div class="article-content">
			    	{$this->LazyLoad->renderContent($article_info.content)}
			    </div>
		    {/if}
	    </div>

        {if !empty($article_info.has_file)}
		    <div class="entire-file-pdf">
		    	{if !empty($article_info.files)}
					{foreach from = $article_info.files item = file}
						{assign var = file_name value = $this->Utilities->getFileNameInUrl($file)}
						{if $file|pathinfo:$smarty.const.PATHINFO_EXTENSION eq 'pdf'}
						    <div class="ratio-1-1 mb-4">
						        <iframe id="iframepdf" src="{CDN_URL}{$this->Utilities->checkInternalUrl($file)}"></iframe>
						    </div>
						{/if}
					{/foreach}  
				{/if}
			</div>
		{/if}

	    {if !empty($article_info.has_file)}
		    <div class="entire-file">
		        <p>
		            <b>
		                {__d('template', 'tai_lieu')}
		            </b>
		        </p>
		        
		    	{if !empty($article_info.files)}
					{foreach from = $article_info.files item = file}
						{assign var = file_name value = $this->Utilities->getFileNameInUrl($file)}
						<a href="{CDN_URL}{$this->Utilities->checkInternalUrl($file)}" download="{CDN_URL}{$this->Utilities->checkInternalUrl($file)}" class="btn btn-submit text-lowercase">
							<i class="fa-light fa-download"></i> {__d('template', 'tai_xuong')} {urldecode($file_name)}
						</a>
					{/foreach}  
				{/if}
			</div>
		{/if}

		{* tag bài viết *}
		{if !empty($article_info.tags)}
    		<div class="d-flex align-items-center flex-wrap mb-2">
                <label class="mr-2">
                    {__d('template', 'the_bai_viet')}: 
                </label>
                <ul class="tags list-unstyled mb-0">
			        {foreach from = $article_info.tags item = tag}
			        	{if !empty($tag.name)}
						    <li>
						        <a href="{if !empty($tag.url)}{TAG_PATH}/{$tag.url}{/if}" title="{$tag.name}">
						        	{$tag.name}{if !$tag@last},{/if}
						        </a>
						    </li>
						{/if}
			        {/foreach}
				</ul>
    		</div>
		{/if}

		{* Chia sẻ mạng xã hội *}
	    {if !empty($article_info.url)}
	        {assign var = url_article value = "{$this->Utilities->getUrlWebsite()}{$this->Utilities->checkInternalUrl($article_info.url)}"}
	        <div class="social-share d-flex align-items-center flex-wrap mb-3">
	            <span class="share-title">
	                <label class="mb-0">
	                    {__d('template', 'chia_se')}:
	                </label>
	            </span>

	            <div class="list-social">
	                <div class="btn-social">
	                    <a href="javascript:;" nh-link-redirect="https://www.facebook.com/sharer/sharer.php?u={$url_article}" nh-link-redirect-blank title="Facebook">
	                        <i class="fa-brands fa-facebook-f"></i>
	                    </a>
	                </div>

	                <div class="btn-social">
	                    <a href="javascript:;" nh-link-redirect="https://twitter.com/share?url={$url_article}" nh-link-redirect-blank title="Twitter">
	                        <i class="fa-brands fa-twitter"></i>
	                    </a>
	                </div>

	                <div class="btn-social">
	                    <a href="javascript:;" nh-link-redirect="https://plus.google.com/share?url={$url_article}" nh-link-redirect-blank title="Google+">
	                        <i class="fa-brands fa-google-plus-g"></i>
	                    </a>
	                </div>

	                <div class="btn-social">
	                    <a href="javascript:;" nh-link-redirect="https://pinterest.com/pin/create/button/?url={$url_article}" nh-link-redirect-blank title="Pinterest">
	                        <i class="fa-brands fa-pinterest-p"></i>
	                    </a>
	                </div>

	                <div class="btn-social">
	                    <a href="javascript:;" nh-link-redirect="https://www.linkedin.com/shareArticle?mini=true&amp;url={$url_article}" nh-link-redirect-blank title="LinkedIn">
	                        <i class="fa-brands fa-linkedin-in"></i>
	                    </a>
	                </div>
	            </div>
	        </div>
	    {/if}
	</article>
	{/strip}
{/if}