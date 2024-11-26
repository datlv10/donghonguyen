{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{assign var = element value = "item_slider"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}
{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section-new ">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.data)}

    <div class="swiper slider_ban" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
        <div class="swiper-wrapper">
            {foreach from = $data_block.data item = article}
                {$this->element("../block/{$block_type}/{$element}", [
                    'article' => $article, 
                    'is_slider' => $is_slider,
                    'ignore_lazy' => $ignore_lazy
                ])}
            {/foreach}
        </div>
        {if !empty($data_extend.slider.pagination)}
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        {/if}
       
    </div>
    {if !empty($data_extend.slider.navigation)}
        <div class=" swiper-button-next-pro swiper-button-next-docdao">
            <img src="{CDN_URL}/media/icon/next_slider_s7.png">
        </div>
        <div class=" swiper-button-prev-pro swiper-button-prev-docdao">
           <img src="{CDN_URL}/media/icon/pev_slider_s7.png">
        </div>
    {/if}
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}
{/strip}