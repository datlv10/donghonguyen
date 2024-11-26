{if !empty($genealogies)}
    <ul>
        {foreach from=$genealogies item=item key=key}
            <li>
                <a href="javascript:;" data-id="{$item.id}">
                    {if !empty($item.text)}{$item.text}{/if}
                </a>

                {if !empty($item.children)}
                    {$this->element("../Genealogy/item_tree", ['genealogies' => $item.children])}
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}

