{strip}
{assign var = item value = "item"}
{if !empty($data_extend['item'])}
    {assign var = item value = $data_extend['item']}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <div class="border-bottom text-uppercase h5 font-weight-bold border-light pb-4 mb-4">
    	{$this->Block->getLocale('tieu_de', $data_extend)}
    </div>
{/if}

<div class="view-small">
    {if !empty($data_block.data)}
        <div class="row">
            {foreach from = $data_block.data item = product}
                {$this->element("../block/{$block_type}/{$item}", [
                    'product' => $product, 
                    'col' => $col,
                    'ignore_lazy' => $ignore_lazy
                ])}
            {/foreach} 
        </div>
    {else}
        <div class="mb-4">
            {__d('template', 'khong_co_du_lieu')}
        </div>
    {/if}
</div>
{/strip}