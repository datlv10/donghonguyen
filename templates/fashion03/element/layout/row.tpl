{strip}
    {assign var = row_config value = []}
    {if !empty($row.config)}
        {assign var = row_config value = $row.config}
    {/if}
    <div {if !empty($row_config.id_row)}id="{$row_config.id_row}"{/if} nh-row="{if !empty($row.code)}{$row.code}{/if}" class="{if !empty($row_config.style_class)}{$row_config.style_class}{/if}">
        {if empty($row_config.full_screen)}
            <div class="container">
        {/if}
            <div class="row {if !empty($row_config.full_screen)}no-gutters{/if}">
                {foreach from = $row.columns item = column}
                    {assign var = column_value value = "{if !empty($column.column_value)}{$column.column_value}{/if}"}
                    <div class="{if empty({DEVICE})}col-md-{$column_value} col-12{else}col-{$column_value}{/if}">
                        {if !empty($column.blocks)}
                            {foreach from = $column.blocks item = block_code}
                                {assign var = block_info value = []}
                                {if !empty($blocks[$block_code])}
                                    {assign var = block_info value = $blocks[$block_code]}
                                {/if}

                                {assign var = block_config value = []}
                                {if !empty($block_info.config)}
                                    {assign var = block_config value = $block_info.config}
                                {/if}

                                {assign var = block_cache_options value = []}
                                {if !empty($block_config.cache)}
                                    {assign var = block_cache_options value = $this->Setting->getConfigCacheView($block_code, {BLOCK}, $block_info)}
                                {/if}

                                {$this->element('layout/block',
                                    [
                                        'block_info' => $block_info, 
                                        'block_config' => $block_config, 
                                        'block_code' => $block_code
                                    ],
                                    $block_cache_options
                                )}
                            {/foreach}
                        {/if}
                    </div>
                {/foreach}
            </div>
        {if empty($row_config.full_screen)}
            </div>
        {/if}
    </div>
{/strip}