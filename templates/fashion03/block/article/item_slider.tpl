{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else} p-0{/if} mb-5">
{/if}

<article class=" swiper-slide">

    <div class="inner-image mb-3">
        <div class="ratio-3-2">
            {if !empty($article.image_avatar)}
                {assign var = url_img value = "{CDN_URL}{$article.image_avatar}"}
            {else}
                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
            {/if}
        
            <a href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}" title="{if !empty($article.name)}{$article.name}{/if}">
                {$this->LazyLoad->renderImage([
                    'src' => $url_img, 
                    'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                    'class' => 'img-fluid img-4 ',
                    'ignore' => $ignore
                ])}
            </a>
        </div>
    </div>
    <div class="blog-clb">
        {if !empty($article.name)}   
            <div class="article-title my-3">
                <a class="a-b" href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name|escape|truncate:150:" ..."}
                </a>
            </div>  
        {/if}

 
    </div>  
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}