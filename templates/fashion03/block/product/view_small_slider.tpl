{strip}
{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section mb-3 pb-3 border-bottom">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}
<div class="view-small">
    {if !empty($data_block.data)}
        <div class="swiper" nh-swiper="{if !empty($data_extend.slider)}{htmlentities($data_extend.slider|@json_encode)}{/if}">
            <div class="swiper-wrapper">
                {foreach from = $data_block.data item = product}
                    {$this->element("../block/{$block_type}/item_small", [
                        'product' => $product, 
                        'is_slider' => true,
                        'ignore_lazy' => $ignore_lazy
                    ])}
                {/foreach}
            </div>
            {if !empty($data_extend.slider.pagination)}
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
            {/if}
            {if !empty($data_extend.slider.navigation)}
                <div class="swiper-button-next">
                    <i class="fa-light fa-angle-right"></i>
                </div>
                <div class="swiper-button-prev">
                    <i class="fa-light fa-angle-left"></i>
                </div>
            {/if}
        </div>
    {else}
        <div class="mb-4">
            {__d('template', 'khong_co_du_lieu')}
        </div>
    {/if}
</div>
{/strip}