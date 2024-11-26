<div class="table-responsive nh-table-responsive">
    <table class="table mb-0 nh-table-item">
        <thead class="thead-light">
            <tr>                
                <th>
                    {__d('admin', 'ten_chuong_trinh')}
                </th>

                <th class="w-20">
                    {__d('admin', 'loai_khuyen_mai')}
                </th>

                <th class="w-30">
                    {__d('admin', 'chiet_khau')}
                </th>

                <th class="text-center w-10">
                    {__d('admin', 'chon')}
                </th>
            </tr>
        </thead>

        <tbody>
            {if !empty($promotion_invalid)}
                {assign var = list_type value = $this->PromotionAdmin->getListTypePromotion()}
                {foreach from = $promotion_invalid key = promotion_id item = promotion}
                    <tr nh-promotion-info="{htmlentities($promotion|@json_encode)}">                        
                        
                        <td>
                            {if !empty($promotion.name)}
                                {$promotion.name}
                            {/if}
                        </td>
                            
                        <td>
                            {if !empty($promotion.type_discount) && !empty($list_type[$promotion.type_discount])}
                                {$list_type[$promotion.type_discount]}
                            {/if}
                        </td>

                        <td>
                            {if !empty($promotion.value.value_discount)}
                                <p>
                                    {__d('admin', 'gia_tri_chiet_khau')}:
                                    <strong class="text-primary">
                                        {$promotion.value.value_discount|number_format:0:".":","}
                                        {if !empty($promotion.value.type_value_discount) && $promotion.value.type_value_discount == PERCENT}
                                            %
                                        {/if}
                                    </strong>
                                </p>

                                {if !empty($promotion.value.max_value)}
                                    {__d('admin', 'toi_da')}: {$promotion.value.max_value|number_format:0:".":","}
                                {/if}
                                
                            {/if}
                        </td>

                        <td class="text-center">
                            <label  class="kt-radio kt-radio--tick kt-radio--success mb-15">
                                <input radio-select-promotion="{$promotion_id}" name="promotion_selected" value="{$promotion_id}"  type="radio"> 
                                <span></span>
                            </label>
                        </td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan="4" class="text-center">
                        <i>
                            {__d('admin', 'khong_tim_thay_chuong_trinh_khuyen_mai_phu_hop')}
                        </i>
                    </td>
                </tr>
            {/if}        
        </tbody>
    </table>
</div>