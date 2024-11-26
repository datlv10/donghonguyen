{strip}
{assign var = ignore value = false}
{if !empty($ignore_lazy)}
    {assign var = ignore value = $ignore_lazy}
{/if}
{$video_id = "video-{time()}-{rand(1, 1000)}"}

<article class="swiper-slide prod-hover nen-video ">
    {if !empty($article.url_video)}
        {if !empty($article.image_avatar)}
            {assign var = url_img value = "{CDN_URL}{$article.image_avatar}"}
        {else}
            {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
        {/if}

        {if $article.type_video == {VIDEO_YOUTUBE}}
            <div nh-light-gallery>
                <a class="wrp-effect-album" href="https://www.youtube.com/watch?v={$article.url_video}" >
                    <div class="inner-image ratio-3-2 effect-video video-zoom">
                        <img class="img-fluid" src="{$url_img}" alt="{if !empty($article.name)}{$article.name}{/if}">
                    </div>
                    
                    {if !empty($article.name)}   
                        <h4 class="video-title color-black my-4 text-justify ">
                            {$article.name|escape|truncate:50:" ..."}
                        </h4>  
                    {/if}
                </a>
            </div>
        {/if}

        {if $article.type_video == {VIDEO_SYSTEM}}
            <div nh-light-gallery>
                <a class="wrp-effect-album" data-html="#{$video_id}">
                    <div class="inner-image ratio-3-2 effect-video video-zoom">
                        <img class="img-fluid" src="{$url_img}" alt="{if !empty($article.name)}{$article.name}{/if}">
                    </div>
                    
                    {if !empty($article.name)}   
                        <h4 class="video-title color-black my-3 text-justify mt-5">
                            {$article.name|escape|truncate:185:" ..."}
                        </h4>  
                    {/if}
                </a>
            </div>

            <div id="{$video_id}" style="display:block;">
                <video class="lg-video-object lg-html5" controls preload="none">
                    <source src="{CDN_URL}{$article.url_video}" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
            </div>
        {/if}
    {/if}
</article>

{/strip}