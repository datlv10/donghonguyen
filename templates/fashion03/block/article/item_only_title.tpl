{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}

<article class="article-only-tit">
        <div class="inner-content-only">
            {if !empty($article.name)}   
                <div class="only-title mb-3">
                    <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                  <img src="{CDN_URL}/media/icon/arow_post.png">      {$article.name}
                    </a>
                </div>  
            {/if}

        </div>  
    
</article>


{/strip}