{strip}
{if !empty($categories)}
	{if !empty($parent_id)}
		<span nh-toggle="child-category-{$parent_id}" class="dropdown-toggle"></span>
	{/if}

	<ul {if !empty($parent_id)}nh-toggle-element="child-category-{$parent_id}"{/if} class="{if !empty($parent_id)}list-child{else}categories-section{/if} list-unstyled mb-0">
		{foreach from = $categories item = category}
			<li class="{if !empty($category.children)}has-child{/if}">
				<a {if !empty($category.url)}href="{$this->Utilities->checkInternalUrl($category.url)}"{/if}>
					{$category.name|escape|truncate:80:" ..."}
				</a>
				{if !empty($category.children)}
					{$this->element('../block/category_product/item', [
						'categories' => $category.children,
						'parent_id' => $category.id
					])}
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
{/strip}