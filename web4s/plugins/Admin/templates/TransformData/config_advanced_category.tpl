{if !empty($data_config)}
	<form id="config-advanced-form" action="{ADMIN_PATH}/transform-data/export/save-config-advanced" method="POST" autocomplete="off"> 
		{foreach from=$data_config item=item key=key}
			{assign var = category_id value = ''}
	        {if !empty($item.id)}
	            {assign var = category_id value = $item.id}
	        {/if}

	        <div class="form-group">
	            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
	                <input nh-check-single type="checkbox" name="ids[]" value="{$category_id}" {if !empty($category_id) && !empty($migrate_config) && in_array($category_id, $migrate_config)}checked{/if}> 
	                {if !empty($item.CategoriesContent.name)}
	                    {$item.CategoriesContent.name}
	                {/if}
	                <span></span>
	            </label>
	        </div>
		{/foreach}

		<input type="hidden" name="type" value="{if !empty($type)}{$type}{/if}">
	</form>
{/if}