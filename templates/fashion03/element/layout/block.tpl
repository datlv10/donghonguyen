{strip}
    {assign var = block_type value = "{if !empty($block_info.type)}{$block_info.type}{/if}"}
    {assign var = data_extend value = []}
    {if !empty($block_info.data_extend)}
        {assign var = data_extend value = $block_info.data_extend}
    {/if}

    {assign var = data_block value = []}
    {if !empty($block_info.data_block)}
        {assign var = data_block value = $block_info.data_block}
    {/if}

    {assign var = view value = 'view.tpl'}
    {if !empty($block_info.view)}
        {assign var = view value = {$block_info.view}}
    {/if}

    <div nh-block="{$block_code}" nh-block-cache="{if !empty($block_config.cache)}true{else}false{/if}" class="{if !empty($block_config.class)}{$block_config.class}{/if}">
        {if $this->Block->checkViewExist($block_type, $view)}
            {$this->element("../block/{$block_type}/{$view|replace:'.tpl':''}", [
                'block_info' => $block_info, 
                'block_config' => $block_config, 
                "{DATA_EXTEND}" => $data_extend, 
                'data_block' => $data_block,
                'block_type' => $block_type
            ])}
        {/if}
    </div>
{/strip}