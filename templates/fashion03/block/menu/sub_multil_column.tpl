{assign var = number_column value = 3} {* column: 2 // 3 // 4 // 5 *}

{strip}
<div nh-toggle-element="{$parent_menu_code}" class="entry-menu multil-column scrollbar">

	<div class="container-menu">
		{$data_sub_menu = array_chunk($data_sub_menu, $number_column)}

		{foreach from = $data_sub_menu key = k_0 item = item}
			<div class="row-menu">

				{foreach from = $item key = k_1 item = sub_menu}
	                <div class="column-{$number_column} {if !empty($sub_menu.children)}has-child{/if}">
						<a class="menu-title" href="{if !empty($sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_menu.url)}{else}/{/if}">
							{$sub_menu.name|escape|truncate:60:" ..."}
						</a>
						
						{if !empty($sub_menu.children)}
							<span class="grower" nh-toggle="{$parent_menu_code}-{$k_0}-{$k_1}"></span>
							<ul class="sub-menu" nh-toggle-element="{$parent_menu_code}-{$k_0}-{$k_1}">
								{foreach from = $sub_menu.children item = item}
									<li>
										<a class="menu-link" href="{if !empty($sub_menu.url)}{$this->Utilities->checkInternalUrl($sub_menu.url)}{else}/{/if}">{$item.name|escape|truncate:60:" ..."}</a>
									</li>
								{/foreach}
							</ul>
						{/if}
					</div>
				{/foreach}

			</div>
        {/foreach}

	</div>				
</div>
{/strip}