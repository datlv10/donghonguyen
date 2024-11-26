{strip}
{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = item value = "item"}
{if !empty($data_extend['item'])}
    {assign var = item value = $data_extend['item']}
{/if}

{assign var = is_slider value = false}
{if !empty($data_extend.slider)}
    {assign var = is_slider value = true}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <h3 class="title-section text-center mb-5">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.data)}
    <div class="row">
        {foreach from = $data_block.data item = product}
            {$this->element("../block/{$block_type}/{$item}", [
                'product' => $product, 
                'col' => $col,
                'is_slider' => $is_slider,
                'ignore_lazy' => $ignore_lazy
            ])}
        {/foreach} 
    </div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}

{if !empty($block_config.has_pagination) && !empty($data_block[{PAGINATION}])}
    {$this->element('pagination_ajax', ['pagination' => $data_block[{PAGINATION}]])}
{/if}
{/strip}