{assign var = number_column value = 4} {* column: 2 // 3 // 4 // 5 *}

{strip}
<ul nh-toggle-element="{$parent_menu_code}" class="entry-menu full-width scrollbar">
	<li class="container-menu">
		{$data_sub_menu = array_chunk($data_sub_menu, $number_column)}
		{foreach from = $data_sub_menu key = k_0 item = item}
			<ul class="row-menu">
				{foreach from = $item key = k_1 item = sub_menu}
	            <li class="column-{$number_column} {if !empty($sub_menu.children)}has-child{/if}">
	            	{if !empty($sub_menu.image_custom)}
	            		<img src="{$this->Utilities->replaceVariableSystem($sub_menu.image_custom)}" alt="{if !empty($sub_menu.name)}{$sub_menu.name}{/if}" class="img-fluid pb-4 pt-4" />
	            	{/if}

	            	{assign var = class_item value = ""}
					{if !empty($sub_menu.class_item)}
						{assign var = class_item value = "<i class='{$sub_menu.class_item}'></i>"}
					{/if} 
					{if !empty($sub_menu.name)}
						<a class="menu-title" href="{if !empty($sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_menu.url)}{else}/{/if}">
							{$class_item}{$sub_menu.name|escape|truncate:60:" ..."}
						</a>
					{/if}
					{if !empty($sub_menu.children)}
						{assign var = class_item_children value = ""}
						{if !empty($sub_menu.children.class_item_children)}
							{assign var = class_item_children value = "<i class='{$sub_menu.children.class_item}'></i>"}
						{/if}
						<span class="grower" nh-toggle="{$parent_menu_code}-{$k_0}-{$k_1}"></span>
						<ul nh-toggle-element="{$parent_menu_code}-{$k_0}-{$k_1}" class="sub-menu">
							{foreach from = $sub_menu.children item = sub_sub_menu}
								<li>
									<a class="menu-link" href="{if !empty($sub_sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_sub_menu.url)}{else}/{/if}">
										{$class_item_children}
										{if !empty($sub_sub_menu.name)}
											{$sub_sub_menu.name|escape|truncate:60:" ..."}
										{/if}
									</a>
								</li>
							{/foreach}
						</ul>
					{/if}
				</li>
				{/foreach}
	        </ul>
        {/foreach}
    </li>
</ul>
{/strip}