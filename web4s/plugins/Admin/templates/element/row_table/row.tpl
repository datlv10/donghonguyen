{assign var = item_id value = ''}
{if !empty($data_item.id)}
    {assign var = item_id value = $data_item.id}
{/if}

{assign var = product_id value = ''}
{if !empty($data_item.product_id)}
    {assign var = product_id value = $data_item.product_id}
{/if}

{assign var = product_item_id value = ''}
{if !empty($data_item.product_item_id)}
    {assign var = product_item_id value = $data_item.product_item_id}
{/if}

{assign var = discount_type value = ''}
{if !empty($data_item.discount_type)}
    {assign var = discount_type value = $data_item.discount_type}
{/if}

{assign var = discount_value value = ''}
{if !empty($data_item.discount_value)}
    {assign var = discount_value value = $data_item.discount_value}
{/if}

{assign var = total_discount value = ''}
{if !empty($data_item.total_discount)}
    {assign var = total_discount value = $data_item.total_discount}
{/if}

{assign var = vat_value value = ''}
{if !empty($data_item.vat_value)}
    {assign var = vat_value value = $data_item.vat_value}
{/if}

{assign var = total_vat value = ''}
{if !empty($data_item.total_vat)}
    {assign var = total_vat value = $data_item.total_vat}
{/if}

{assign var = width value = ''}
{if !empty($data_item.width)}
    {assign var = width value = $data_item.width}
{/if}

{assign var = length value = ''}
{if !empty($data_item.length)}
    {assign var = length value = $data_item.length}
{/if}

{assign var = height value = ''}
{if !empty($data_item.height)}
    {assign var = height value = $data_item.height}
{/if}

{assign var = weight value = ''}
{if !empty($data_item.weight)}
    {assign var = weight value = $data_item.weight}
{/if}

{assign var = width_unit value = ''}
{if !empty($data_item.width_unit)}
    {assign var = width_unit value = $data_item.width_unit}
{/if}

{assign var = length_unit value = ''}
{if !empty($data_item.length_unit)}
    {assign var = length_unit value = $data_item.length_unit}
{/if}

{assign var = height_unit value = ''}
{if !empty($data_item.height_unit)}
    {assign var = height_unit value = $data_item.height_unit}
{/if}

{assign var = weight_unit value = ''}
{if !empty($data_item.weight_unit)}
    {assign var = weight_unit value = $data_item.weight_unit}
{/if}

<tr data-id="{$item_id}" data-product-id="{$product_id}" data-product-item-id="{$product_item_id}" data-discount-type="{$discount_type}" data-discount="{$discount_value}" data-total-discount="{$total_discount}" data-vat="{$vat_value}" data-total-vat="{$total_vat}" data-width="{$width}"  data-length="{$length}" data-height="{$height}" data-weight="{$weight}" data-width_unit="{$width_unit}" data-length_unit="{$length_unit}" data-height_unit="{$height_unit}" data-weight_unit="{$weight_unit}" class="fw-400 {if empty($item_id)}d-none{/if}">
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
        <input input-quantity="{if !empty($data_item.quantity)}{$data_item.quantity}{/if}" value="{if !empty($data_item.quantity)}{$data_item.quantity}{/if}" class="form-control form-control-sm number-input text-right" type="text">
    </td>
                                                        
    <td class="text-right">
        <input input-price="{if !empty($data_item.price)}{$data_item.price}{/if}" value="{if !empty($data_item.price)}{$data_item.price}{/if}" class="form-control form-control-sm number-input text-right cursor-p" type="text" readonly="true" data-toggle="popover">
        <span label-total-discount-product="{$total_discount}" class="fs-12 text-danger"></span>
    </td>
        
    <td class="text-right">
        <span label-total-item="{if !empty($data_item.total_item)}{$data_item.total_item}{/if}">
            {if !empty($data_item.total_item)}
                {$data_item.total_item|number_format:0:".":","}
            {/if}
        </span>
    </td>

    <td class="text-right">
        <i action-item="delete" class="la la-close cursor-p"></i>
    </td>
</tr>