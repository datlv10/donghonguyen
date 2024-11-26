{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}

<article class="swiper-slide">
        <div class="inner-content-custom">
            {if !empty($article.name)}   
                <div class="only-title mb-2">
                    <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                        {$article.name}
                    </a>
                </div>  
            {/if}

        </div>  
    
</article>


{/strip}