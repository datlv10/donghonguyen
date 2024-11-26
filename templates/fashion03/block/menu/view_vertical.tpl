{strip}
{assign var = display_menu value = 'style="display: block"'}
{if empty({DEVICE})}
	{assign var = display_menu value = 'style="display: none"'}
{/if}

{$menu_id = "menu-{time()}-{rand(1, 1000)}"}
<div class="menu-container menu-vertical">
	<a class="menu-vertical--title" href="javascript:;" nh-toggle="{$menu_id}">
		<i class="fa-light fa-align-justify"></i>
		{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
		    {$this->Block->getLocale('tieu_de', $data_extend)}
		{/if}
	</a>
	<div class="back-drop"></div>
	<nav class="menu-vertical--nav" nh-menu="sidebar" menu-type="vertical">
		<div class="menu-vertical--top">
			<span class="menu-vertical--header">
				{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
				    {$this->Block->getLocale('tieu_de', $data_extend)}
				{/if}
			</span>
			<a href="javascript:;" nh-menu="btn-close" class="close-sidebar effect-rotate icon-close">
				<i class="fa-light fa-xmark"></i>
			</a>
		</div>

		{if !empty($data_block)}
			<ul class="menu-vertical--content list-unstyled mb-0" nh-toggle-element="{$menu_id}" {$display_menu}>
				{foreach from = $data_block item = menu}
					{assign var = class_has_child value = ""}
					{if !empty($menu.has_sub_menu)}
						{assign var = class_has_child value = "has-child "}
					{/if}

					{assign var = class_position value = ""}
					{if !empty($menu.view_item) && $menu.view_item == 'sub_dropdown'}
						{assign var = class_position value = "position-relative "}
					{/if}

					{assign var = class_item value = ""}
					{if !empty($menu.class_item)}
						{assign var = class_item value = "<i class='menu-vertical--icon {$menu.class_item}'></i>"}
					{/if}
					
					{if !empty($menu.name)}
						{assign var = image_source value = ''}
						{if !empty($menu.image) && !empty($menu.image_source)}
							{assign var = image_source value = $menu.image_source}
						{/if}

						{assign var = image_url value = ''}
						{if !empty($menu.image) && $image_source == 'cdn'}
							{assign var = image_url value = "{CDN_URL}{$menu.image}"}
						{/if}

						{if !empty($menu.image) && $image_source == 'template'}
							{assign var = image_url value = "{$menu.image}"}
						{/if}

						<li class="{$class_position}{$class_has_child}">
							<a href="{if !empty($menu.url)}{$this->Utilities->checkInternalUrl($menu.url)}{else}/{/if}" {if !empty($menu.blank_link)}target="_blank"{/if}>
		                        {if !empty($menu.image)}
		                            <img src="{$image_url}" alt="{$menu.name}" class="menu-vertical--icon" />
		                        {/if}
								{$class_item}{$menu.name|escape|truncate:60:" ..."}
								<span class="fa-light fa-chevron-right"></span>
							</a>

							{if empty($menu.data_sub_menu) && !empty($menu.data_extend_sub_menu)}
	                            {$menu.data_sub_menu = $menu.data_extend_sub_menu}
	                        {/if}

							{if !empty($menu.data_sub_menu)}
								{assign var = parent_menu_code value = $this->Utilities->randomCode()}
								
	                            {assign var = data_child value = $menu.data_sub_menu}
	                            {if $menu.type_sub_menu == 'custom'}
	                                {assign var = data_child value = $this->Block->getLocale('data_sub_menu', $menu.data_sub_menu)}
	                            {/if}
	                            
								<span class="grower" nh-toggle="{$parent_menu_code}"></span>

								{$this->element("../block/{$block_type}/{$menu.view_item}", [
									'data_sub_menu' => $data_child,
									'parent_menu_code' =>  $parent_menu_code
								])}
							{/if}
						</li>
					{/if}
				{/foreach}
				{if !empty($data_extend['locale'][{LANGUAGE}]['link_tat_ca'])}
				    <div class="text-right">
	            	    <a class="menu-vertical--link-all" href="{$this->Block->getLocale('link_tat_ca', $data_extend)}">
	            	        {if !empty($data_extend['locale'][{LANGUAGE}]['label_link_tat_ca'])}
	            	            {$this->Block->getLocale('label_link_tat_ca', $data_extend)}
	            	            <i class="fa-light fa-arrow-right ml-3"></i>
	            	        {/if}
	            	    </a>
	        	    </div>
	        	{/if}
			</ul>
		{/if}
	</nav>
</div>
{/strip}