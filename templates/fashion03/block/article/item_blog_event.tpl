{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-6 col-md-3 col-lg-3{/if}">
{/if}

<article class="article-item swiper-slide">
    <div class="">
        <div class="inner-image">
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
                        'class' => 'img-fluid',
                        'ignore' => $ignore
                    ])}
                </a>
            </div>
             <div class=" date-blogs-2">
                <p>
                {if !empty($article.created)}
                     <img src="{CDN_URL}/media/icon/calendar2.png" class="icon-date">   <span>{$this->Utilities->convertIntgerToDateString($article.created, 'd')}</span>
                    {__d('template', 'thang')} {$this->Utilities->convertIntgerToDateString($article.created, 'm, Y')}
                {/if}
                </p>
            </div>
        </div>
        <div class="inner-content">
           
            {if !empty($article.name)}   
                <div class="article-title my-2">
                    <a href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                        {$article.name|escape|truncate:50:" ..."}
                    </a>
                </div>  
            {/if}

            {if !empty($article.description)}
                <div class="article-description mb-3">
                    {$article.description|strip_tags|truncate:85:" ..."}
                </div>
            {/if}

           
        </div>  
    </div>
</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}