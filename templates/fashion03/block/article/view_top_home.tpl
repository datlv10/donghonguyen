{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{assign var = element value = "item_blogs_custom"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = title value = ""}
{if !empty($seo_info.title)}
    {assign var = title_seo value = "{$seo_info.title}"}
{/if}
{if !empty($title_for_layout)}
    {assign var = title_seo value = "{$title_for_layout}"}
{/if}

{strip}

{if !empty($data_block.data)}
<div class="blog-custom-top">
    <div class="row">
        <div class="col-12 col-md-8">
                <div class="swiper" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
                    <div class="swiper-wrapper ">
                    {foreach from = $data_block.data key=key item = article}
                        {if $key < 5 }
                            {$this->element("../block/{$block_type}/item_top_home", [
                                'article' => $article, 
                                'is_slider' => $is_slider,
                                'ignore_lazy' => $ignore_lazy
                            ])}
                        {/if}
                        
                {/foreach}
                 
                    </div>
                    {if !empty($data_extend.slider.navigation)}
                    <div class="swiper-button-next">
                        <img src="{CDN_URL}/media/icon/arrow-right-slider.png">
                    </div>
                    <div class="swiper-button-prev">
                        <img src="{CDN_URL}/media/icon/arow-left-slider.png">
                    </div>
                 {/if}
                </div>
            
        </div>
        
        <div class="col-12 col-md-4">
            {if ($data_block.data) > 1}
            
                <div class="view_small_new">
                    {foreach from = $data_block.data key=key item = article}
                        {if $article@index gte 5 && $key < 7}
                            {$this->element("../block/{$block_type}/item_small_hot_top", [
                                'article' => $article, 
                                'is_slider' => $is_slider,
                                'ignore_lazy' => $ignore_lazy
                            ])}
                        {/if}
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
</div>

{else}
    {__d('template', 'khong_co_du_lieu')}
{/if}

{if !empty($block_config.has_pagination) && !empty($data_block[{PAGINATION}])}
    {$this->element('pagination', ['pagination' => $data_block[{PAGINATION}]])}
{/if}
{/strip}