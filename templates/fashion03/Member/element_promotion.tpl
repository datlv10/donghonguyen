<div nh-promotion class="h-100 bg-white p-4">
    {assign var = list_coupon value = $this->Promotion->getPublicPromotions()}
    {if !empty($list_coupon)}
        {foreach from = $list_coupon item = item}
            {assign var = value value = $item.value}
            {assign var = type_discount value = ''}
            {if !empty($value.type_value_discount)}
                {assign var = type_discount value = $value.type_value_discount}
            {/if}

            {assign var = value_discount value = ''}
            {assign var = unit_discount value = ''}
            {assign var = max_value value = ''}

            {if !empty($item.max_value)}
                {math assign = max_value equation = 'x/y' x = $value.max_value y = 1000} 
            {/if}

            {if !empty($type_discount) && !empty($value.value_discount) && $type_discount == 'percent'}
                {assign var = value_discount value = $value.value_discount}
                {assign var = unit_discount value = '%'}
            {/if}

            {if !empty($type_discount) && !empty($value.value_discount) && $type_discount == 'money'}
                {assign var = value_discount value = $value.value_discount} 
                {math assign = value_discount equation = 'x/y' x = $value.value_discount y = 1000} 
                {assign var = unit_discount value = 'K'}
            {/if}

            <div class="coupon-plan">
                <div class="row mx-n3">
                    <div class="col-4 col-lg-3 px-3">
                        <div class="coupon-plan-price">
                            <div>
                                <span>
                                    {if !empty($value_discount)}
                                        {$value_discount}{$unit_discount}
                                    {/if}
                                </span>
                            
                                {if !empty($item.code)}
                                    {__d('template', 'ma')}: 
                                    <strong>
                                    	{$item.code}
                                    </strong>
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="col-8 col-lg-9 px-3">
                        <div class="coupon-plan-description">
                            {if !empty($item.name)}
                                <div class="h5">
                                    {$item.name}
                                </div>
                            {/if}

                            {if !empty($item.condition_description)}
                                {foreach from = $item.condition_description item = condition_description}
                                    <p class="mb-0">- {$condition_description}</p>
                                {/foreach}
                            {/if}
                        </div>
                        <div class="coupon-plan-button">
                            {if !empty($max_value)}
                                ({__d('template', 'giam_toi_da')}: {$max_value}K)
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    {else}
        {__d('template', 'hien_khong_co_phieu_giam_gia_nao_duoc_ap_dung')}
    {/if}
</div>