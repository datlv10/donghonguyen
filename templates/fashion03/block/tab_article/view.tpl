{strip}
{if !empty($data_extend.locale[{LANGUAGE}].tieu_de)}
    <h3 class="title-section text-center">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </h3>
{/if}

{if !empty($data_block.tabs)}
    <ul class="block-tab nav justify-content-center effect-nav">
        {foreach from = $data_block.tabs item = tab}
            <li class="nav-item">
                <a nh-active-tab="{$tab@index}" class="nav-link {if $tab@first}active{/if}" data-toggle="tab" href="#article-{$tab@index}">
                    {if !empty($tab.name)}
                        {$tab.name}
                    {/if}
                </a>
            </li>
        {/foreach}
    </ul>

    <div class="tab-content">
        {foreach from = $data_block.tabs item = tab}
            <div nh-tab-content="{$tab@index}" {if $tab@first}loaded="1"{/if} id="article-{$tab@index}" class="tab-pane {if $tab@first}active{else}fade{/if}" >
                {if !empty($data_block.data) && !empty($block_config.item[0].view_child) && $tab@first}
                    {assign var = view_path value = "../block/{ARTICLE}/{$block_config.item[0].view_child|pathinfo:$smarty.const.PATHINFO_FILENAME}"}
                    
                    {$this->element($view_path, [
                        'data_block' => $data_block,
                        'block_type' => {ARTICLE}
                    ])}
                {/if}
            </div>
        {/foreach}
    </div>
{/if}
{/strip}