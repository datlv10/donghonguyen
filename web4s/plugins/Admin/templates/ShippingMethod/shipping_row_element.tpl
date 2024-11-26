{assign var = order_location value = []}
{if !empty($item.order_location)}
    {assign var = order_location value = $item.order_location}
{/if}

<tr>
    <td class="pl-0">
        <div class="input-group">
            <input name="order_from" value="{if !empty($item.order_from)}{$item.order_from}{/if}" class="form-control form-control-sm number-input" autocomplete="off" type="text"  placeholder="{__d('admin', 'tu')}">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fa fa-long-arrow-alt-right"></i>
                </span>
            </div>
            <input name="order_to" value="{if !empty($item.order_to)}{$item.order_to}{/if}" class="form-control form-control-sm number-input" name="price_to" autocomplete="off" type="text"  placeholder="{__d('admin', 'den')}">
        </div>
    </td>
        
    <td>
        {$this->Form->select('order_location', $this->LocationAdmin->getListCitiesForDropdown(), ['default' => $order_location, 'title' =>  "{__d('admin', 'tat_ca')}", 'class' => 'form-control form-control-sm select2-multile-select', 'multiple' => 'multiple'])}
    </td>

    <td>
        <input name="order_shipping_fee" value="{if !empty($item.order_shipping_fee)}{$item.order_shipping_fee}{/if}" class="form-control form-control-sm number-input" type="text" placeholder="{__d('admin', 'phi_van_chuyen')}">
    </td>

    <td class="text-center">
        <i btn-action="remove-item" class="fa fa-trash-alt text-danger cursor-p"></i>
    </td>
</tr>