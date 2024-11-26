<div nh-promotion>
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

            {if !empty($type_discount) && ($type_discount == 'percent')}
                {assign var = value_discount value = $value.value_discount}
                {assign var = unit_discount value = '%'}
            {/if}

            {if !empty($type_discount) && ($type_discount == 'money')}
                {assign var = value_discount value = $value.value_discount} 
                {math assign = value_discount equation = 'x/y' x = $value.value_discount y = 1000} 
                {assign var = unit_discount value = 'K'}
            {/if}

            <div class="coupon-plan">
                <div class="row nx-n3">
                    <div class="col-md-4 col-lg-3 px-3">
                        <div class="coupon-plan-price">
                            <span>
                                {if !empty($value_discount)}
                                    {$value_discount}{$unit_discount}
                                {/if}
                            </span>
                        
                            {if !empty($item.code)}
                                {__d('template', 'ma')}: {$item.code}
                            {/if}
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9 px-3">
                        <div class="coupon-plan-description">
                            {if !empty($item.name)}
                                <h4 class="mb-0">
                                    {$item.name}
                                </h4>
                            {/if}

                            {if !empty($item.condition_description)}
                                {foreach from = $item.condition_description item = condition_description}
                                    <p class="mb-0">- {$condition_description}</p>
                                {/foreach}
                            {/if}
                        </div>
                        <div class="coupon-plan-button">
                            <a nh-btn-action="select-coupon" {if !empty($item.code)}nh-coupon-code="{$item.code}"{/if} href="javascript:;">
                                {__d('template', 'su_dung')}
                            </a>
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