{assign var = item_id value = null}
{if !empty($item.id)}
    {$item_id = $item.id}
{/if}
<tr data-id="{$item_id}" data-attribute="{if !empty($item.special_id)}{$item.special_id}{/if}">
    {if !empty($attributes_special)}
        {foreach from = $attributes_special item = attribute}
            <td class="text-center">
                {assign var = value value = ''}
                {if !empty($attribute_item_value[$item_id][{$attribute.id}])}
                    {assign var = value value = $attribute_item_value[$item_id][{$attribute.id}]}
                {/if}

                {$attribute.value = {$value}}

                {if $attribute.input_type == {SPECICAL_SELECT_ITEM}}
                    {$attribute.input_type = {SINGLE_SELECT}}
                    {$attribute.has_image = 0}
                    {$attribute.require = 1}
                    {$attribute.class = 'special-attribute no-init'}
                    {$attribute.name = "item_{$attribute.code}[]"}
                {/if}

                {assign var = options value = []}
                {if !empty($options_special_selected[{$attribute.code}])}
                    {assign var = options value = $options_special_selected[{$attribute.code}]}
                {/if}
                
                {$attribute.options = $options}
                {$attribute.id = $attribute.code}
                {$this->AttributeAdmin->generateInput($attribute, $lang)}
            </td>
        {/foreach}
    {/if}

    {if !empty($attributes_item)}
        {foreach from = $attributes_item item = attribute}
            <td class="text-center">
                {assign var = value value = ''}
                {if !empty($attribute_item_value[$item_id][{$attribute.id}])}
                    {assign var = value value = $attribute_item_value[$item_id][{$attribute.id}]}
                {/if}

                {$attribute.value = {$value}}
                {$attribute.class = 'item-attribute no-init'}
                {assign var = options value = []}
                {if !empty($all_options[{$attribute.id}])}
                    {assign var = options value = $all_options[{$attribute.id}]}
                {/if}
                
                {$attribute.options = $options}
                {$this->AttributeAdmin->generateInput($attribute, $lang)}
            </td>
        {/foreach}
    {/if}
    
    <td class="text-center">
        <input name="item_code[]" value="{if !empty($item.code)}{$item.code}{/if}" class="form-control form-control-sm p-5" type="text">
    </td>

    <td class="text-center">
        <input name="item_price[]" value="{if !empty($item.price)}{floatval($item.price)}{/if}" class="form-control form-control-sm number-input" type="text">
    </td>

    <td class="text-center">
        <span class="fw-400 price-special">
            {if !empty($item.price_special)}
                {floatval($item.price_special)|number_format:0:".":","}
            {/if}
        </span>

        <a href="javascript:;" class="change-price-special">
            <i class="la la-edit fs-18"></i>
        </a>

        <input name="item_price_special[]" value="{if !empty($item.price_special)}{floatval($item.price_special)}{/if}" type="hidden">
        <input name="item_date_special[]" value="{if !empty($item.time_special)}{$item.time_special}{/if}" type="hidden">
    </td>
   
    <td class="text-center">
        <input name="item_quantity_available[]" value="{if !empty($item.quantity_available)}{$item.quantity_available}{/if}" class="form-control form-control-sm number-input" type="text">
    </td>                

    <td class="text-center">
        <a href="javascript:;" class="delete-item">
            <i class="fa fa-trash-alt fs-14 text-danger" title="{__d('admin', 'xoa')}"></i>
        </a>

        {if !empty($id)}
            <i class="fa fa-arrows-alt cursor-p pl-5 fs-14 sort-item" title="{__d('admin', 'sap_xep')}" id="{$id}"></i>
        {/if}
    </td>
</tr>