{if !empty($shipping_carries)}
    <div class="kt-separator kt-separator--space-md kt-separator--border-soild"></div>

    <table id="table-shipping-carriers" class="table mb-30 nh-table-item">
        <thead class="thead-light">
            <tr>
                <th class="text-left">
                    {__d('admin', 'doi_tac_van_chuyen')}
                </th>

                <th class="text-left">
                    {__d('admin', 'dich_vu')}
                </th>

                <th>
                    {__d('admin', 'phi_du_kien')}
                </th>
            </tr>
        </thead>

        <tbody>
            {foreach from = $shipping_carries key = carrier_code item = carrier}
                {if !empty($shipping_fee[$carrier_code])}
                    {assign var = number_service value = count($shipping_fee[$carrier_code])}
                    {foreach from = $shipping_fee[$carrier_code] item = service}
                        {assign var = service_id value = "{if !empty($service.service_id)}{$service.service_id}{/if}"}
                        {assign var = service_type_id value = "{if !empty($service.service_type_id)}{$service.service_type_id}{/if}"}

                        <tr>
                            {if $service@first}
                                <td rowspan="{$number_service}">
                                    <img src="/admin/assets/media/carrier/{$carrier_code}.png" class="w-150px mr-10">
                                    {if !empty($carrier.name)}
                                        <b>{$carrier.name}</b>
                                    {/if}
                                </td>
                            {/if}
                                
                            <td>
                                {if !empty($service.service_name)}
                                    <label class="kt-radio kt-radio--tick kt-radio--success">
                                        <input name="carrier_shipping_fee" value="{if !empty($service.fee)}{$service.fee}{else}0{/if}" data-carrier="{$carrier_code}" data-carrier-service="{$service_id}" data-carrier-service-type="{$service_type_id}" type="radio" {if $service_id == $carrier_service_code}checked="true"{/if}>
                                            {$service.service_name}
                                        <span></span>
                                    </label>
                                {/if}
                            </td>
                                
                            <td>
                                {if !empty($service.fee)}
                                    {$service.fee|number_format:0:".":","}
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                {/if}
            {/foreach}
        </tbody>
    </table>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            {if !empty($ghn_shop)}
                {assign var = selected value = false}
                {if !empty($carrier_shop_id) && $carrier_code == GIAO_HANG_NHANH}
                    {$selected = true}
                {/if}

                <div nh-carrier-shop="{GIAO_HANG_NHANH}" class="form-group {if !$selected}d-none{/if}">
                    <label>
                        {__d('admin', 'cua_hang')}
                        (GHN)
                    </label>
                    {$this->Form->select('ghn_shop', $ghn_shop, ['id' => 'ghn-shop', 'empty' => null, 'default' => $carrier_shop_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'select-shop' => "{GIAO_HANG_NHANH}"])}
                </div>
            {/if}

            {if !empty($ghtk_shop)}
                {assign var = selected value = false}
                {if !empty($carrier_shop_id) && $carrier_code == GIAO_HANG_TIET_KIEM}
                    {$selected = true}
                {/if}

                <div nh-carrier-shop="{GIAO_HANG_TIET_KIEM}" class="form-group {if !$selected}d-none{/if}">
                    <label>
                        {__d('admin', 'cua_hang')}
                        (GHTK)
                    </label>
                    {$this->Form->select('ghtk_shop', $ghtk_shop, ['id' => 'ghtk-shop', 'empty' => null, 'default' => $carrier_shop_id, 'class' => 'form-control form-control-sm kt-selectpicker', 'select-shop' => "{GIAO_HANG_TIET_KIEM}"])}
                </div>
            {/if}
        </div>
    </div>
{/if}