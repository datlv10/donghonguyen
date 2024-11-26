{if !empty($genealogies)}
    <ul id="foldertree">
        {foreach from=$genealogies item=item key=key}
            <li class="parent_li {if count($genealogies) < 2}no-before{/if}">
                <span class="{if !empty($item.sex) && $item.sex == 'female'}nu{else}nam{/if}" data-tree="{if !empty($item.tree)}{$item.tree}{/if}" title="Mở rộng các đời sau">
                    <i class="fa fa-minus-square"></i> 
                    <a href="javascript:;" detail-genealogy data-id="{if !empty($item.id)}{$item.id}{/if}" title="Xem thông tin nhân vật">
                        {if !empty($item.text)}{$item.text}{/if}
                    </a>
                </span>
                {if !empty($item.children)}
                    {$this->element("../Genealogy/item_tree", ['genealogies' => $item.children])}
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}

