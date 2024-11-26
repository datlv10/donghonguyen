{strip}
<ul nh-toggle-element="{$parent_menu_code}" class="entry-menu dropdown">
	{foreach from = $data_sub_menu key = k_0 item = sub_menu}
		<li class="{if !empty($sub_menu.children)}has-child{/if} ">
			<a class="menu-link" href="{if !empty($sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_menu.url)}{else}/{/if}">
				{$sub_menu.name|escape|truncate:60:" ..."}
				
				{if !empty($sub_menu.children)}
					<span class="child-indicator fa-light fa-chevron-right"></span>
				{/if}
			</a>

			{if !empty($sub_menu.children)}
				<span class="grower" nh-toggle="{$parent_menu_code}-{$k_0}"></span>

				<ul nh-toggle-element="{$parent_menu_code}-{$k_0}">
					{foreach from = $sub_menu.children item = item}
		                {$this->element("../block/{$block_type}/element_dropdown", [
		                	'sub_menu' => $item
		                ])}
		            {/foreach}
		        </ul>
			{/if}

		</li>
    {/foreach}
</ul>
{/strip}