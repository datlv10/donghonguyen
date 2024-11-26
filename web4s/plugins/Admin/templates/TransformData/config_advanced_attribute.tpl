{if !empty($data_config)}
    <form id="config-advanced-form" action="{ADMIN_PATH}/transform-data/export/save-config-advanced" method="POST" autocomplete="off">     
        {foreach from=$data_config key=key item=attribute}
            {assign var = attribute_id value = ''}
            {if !empty($attribute.id)}
                {assign var = attribute_id value = $attribute.id}
            {/if}

            <div class="form-group">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                    <input nh-check-single type="checkbox" name="ids[]" value="{$attribute_id}" {if !empty($attribute_id) && !empty($migrate_config) && in_array($attribute_id, $migrate_config)}checked{/if}> 
                    {if !empty($attribute.AttributesContent.name)}
                        {$attribute.AttributesContent.name}
                    {/if}
                    <span></span>
                </label>
            </div>
        {/foreach}

        <input type="hidden" name="type" value="{if !empty($type)}{$type}{/if}">
    </form>
{/if}