{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{if empty($is_slider)}
    <div class="{if !empty($col)}{$col}{else}col-12 col-md-12 col-lg-12 p-0{/if} ">
{/if}

<article class="small-right-article">

    <div class="inner-image mb-3">
        <div class="ratio-16-9">
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
    <div class=" list_small-top">
        {if !empty($article.name)}   
            <div class="article-title mb-2">
                <a class="a-b" href="{$this->Utilities->checkInternalUrl($article.url)}" title="{if !empty($article.name)}{$article.name}{/if}">
                    {$article.name|escape|truncate:150:" ..."}
                </a>
            </div>  
        {/if}
 <div class="post-date date-new-2">
                <p>
                {if !empty($article.created)}
                     <img src="{CDN_URL}/media/icon/calendar2.png" class="icon-date">   <span>{$this->Utilities->convertIntgerToDateString($article.created, 'd')}</span>
                    {__d('template', 'thang')} {$this->Utilities->convertIntgerToDateString($article.created, 'm, Y')}
                {/if}
                </p>
            </div>

        {if !empty($article.description)}
            <div class="article-description mb-3">
                {$article.description|strip_tags|truncate:200:" ..."}
            </div>
        {/if}

    </div>  

</article>

{if empty($is_slider)}
	</div>
{/if}
{/strip}