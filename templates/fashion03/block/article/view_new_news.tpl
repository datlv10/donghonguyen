{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{assign var = element value = "item_new_news"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}
{strip}


{if !empty($data_block.data)}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-news text-center">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}
<div class="fix-swiper-height">
    <div class="swiper " nh-swiper="{if !empty($data_extend.slider_doc)}{htmlentities($data_extend.slider_doc|@json_encode)}{/if}">
        <div class="swiper-wrapper">
            {foreach from = $data_block.data item = article}
                {$this->element("../block/{$block_type}/{$element}", [
                    'article' => $article, 
                    'is_slider' => $is_slider,
                    'ignore_lazy' => $ignore_lazy
                ])}
            {/foreach}
        </div>
        {if !empty($data_extend.slider_doc.pagination)}
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        {/if}
        {if !empty($data_extend.slider_doc.navigation)}
            <div class="swiper-button-next">
                <i class="fa-light fa-angle-right h1"></i>
            </div>
            <div class="swiper-button-prev">
                <i class="fa-light fa-angle-left h1"></i>
            </div>
        {/if}
    </div>
</div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}
{/strip}