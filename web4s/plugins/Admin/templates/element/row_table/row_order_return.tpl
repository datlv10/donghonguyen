{assign var = product_id value = ''}
{if !empty($data_item.product_id)}
    {assign var = product_id value = $data_item.product_id}
{/if}

{assign var = product_item_id value = ''}
{if !empty($data_item.product_item_id)}
    {assign var = product_item_id value = $data_item.product_item_id}
{/if}


<tr data-id="" data-product-id="{$product_id}" data-product-item-id="{$product_item_id}" class="fw-400">
    <td>
        <span label-code="{if !empty($data_item.code)}{$data_item.code}{/if}">
            {if !empty($data_item.code)}{$data_item.code}{/if}
        </span>
    </td>
        
    <td>
        <span label-name="{if !empty($data_item.name_extend)}{$data_item.name_extend}{/if}">
            {if !empty($data_item.name_extend)}
                {$data_item.name_extend}
            {/if}
        </span>
    </td>
        
    <td>
        <input input-quantity="0" max-quantity="{if !empty($data_item.quantity)}{$data_item.quantity}{/if}" value="0" class="form-control form-control-sm number-input text-right" type="text">
    </td>

    <td class="text-center">
        {if !empty($data_item.quantity)}
            {$data_item.quantity|number_format:0:".":","}
        {/if}
    </td>
                                                        
    <td>
        <input input-price="{if !empty($data_item.price)}{$data_item.price}{/if}" value="{if !empty($data_item.price)}{$data_item.price}{/if}" class="form-control form-control-sm number-input text-right cursor-p" type="text">
    </td>
        
    <td class="text-right">
        <span label-total-item="">
            0
        </span>
    </td>
</tr>