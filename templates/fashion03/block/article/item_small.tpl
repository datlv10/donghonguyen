{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
<article class="article-item mb-4 clearfix">
    <div class="inner-image">
        {if !empty($article.image_avatar)}
            {assign var = url_img value = "{CDN_URL}{$article.image_avatar}"}
        {else}
            {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
        {/if}

        <a class="ratio-1-1 d-block" href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}" 
            title="{if !empty($article.name)}{$article.name}{/if}">
            {$this->LazyLoad->renderImage([
                'src' => $url_img, 
                'alt' => "{if !empty($article.name)}{$article.name}{/if}",
                'class' => 'img-fluid',
                'ignore' => $ignore
            ])}
        </a>
    </div>
    
    <div class="inner-content">
        {if !empty($article.name)}   
            <div class="article-title">
                <a href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}">
                    {$article.name}
                </a>
            </div>  
        {/if}
    </div>  
</article>
{/strip}