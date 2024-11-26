{if !empty($brands)}
    <div class="row">
        {foreach from=$brands key=key item=item}

            {assign var = brand_id value = ""}
            {if !empty($item.id)}
                {assign var = brand_id value = $item.id}
            {/if}

            {assign var = checked value = ""}
            {if !empty($brand_id) && !empty($data_apply) && in_array($brand_id,$data_apply)}
                {assign var = checked value = "checked"}
            {/if}
            <div class="col-sm-6 col-12">
                {if !empty($item.BrandsContent.name)}
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                        <input type="checkbox" value="{if !empty($brand_id)}{$brand_id}{/if}" name="brands[]" {$checked}> {$item.BrandsContent.name}
                        <span></span>
                    </label>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}

<input type="hidden" name="category_id" value="{if !empty($category_id)}{$category_id}{/if}">