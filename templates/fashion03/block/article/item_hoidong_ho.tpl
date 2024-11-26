{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}

<div class="">
<article class="article-item">
    <div class="row m-0">
        <div class="col-12 col-md-6 p-0">
            <div class="inner-image">
            <div class="ratio-4-3">
                {if !empty($article.image_avatar)}
                    {assign var = url_img value = "{CDN_URL}{$article.image_avatar}"}
                {else}
                    {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}
            
                <a href="{if !empty($article.url)}{$this->Utilities->checkInternalUrl($article.url)}{/if}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$this->LazyLoad->renderImage([
                        'src' => $url_img, 
                        'alt' => "{if !empty($article.name)}{$article.name}{/if}", 
                        'class' => 'img-fluid',
                        'ignore' => $ignore
                    ])}
                </a>
            </div>
        </div>
        </div>
        <div class="col-12 col-md-6 is-mobi-p-0">
            <div class="inner-content">
            {if !empty($article.name)}   
                <div class=" article_blog-custom-title ">
                    <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                        {$article.name}
                    </a>
                </div>  
            {/if}
            
        </div>  
        </div>
        
    </div>
</article>
</div>
{/strip}