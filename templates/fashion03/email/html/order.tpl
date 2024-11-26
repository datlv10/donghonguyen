{assign var = order_info value = $this->Order->getInfoOrder($id_record)}

{if !empty($order_info)}
    {assign var = unit value = 'đ'}
    {assign var = contact value = []}
    {if !empty($order_info.contact)}
        {assign var = contact value = $order_info.contact}
    {/if}    

    <div style="margin-bottom:10px">
        {__d('template', 'chao_khach_hang')}
        {if !empty($contact.full_name)}
            <strong>
                {$contact.full_name}
            </strong>
        {/if}
    </div>

    <div style="margin-bottom: 10px">
        {__d('template', 'cam_on_ban_da_mua_sam_tren_website_cua_chung_toi_hy_vong_ban_se_hai_long_voi_nhung_san_pham_da_mua')}
        <br>
        {__d('template', 'don_hang')} {if !empty($order_info.code)}<strong>{$order_info.code}</strong>{/if} {__d('template', 'cua_ban_hien_dang_xu_ly_va_se_thong_bao_cho_ban_ngay_khi_hang_duoc_xuat_kho')}
    </div>

    <div style="margin-bottom: 10px">
        <strong style="font-size:12px">
            {__d('template', 'thong_tin_khach_hang')}:
        </strong>
    </div>

    <table width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #4c4e4e; border-top: none; margin-bottom:20px">
        <tbody>
            <tr>
                <td width="25%" style="border-top: 1px solid #4c4e4e; border-right: 1px solid #4c4e4e; padding: 10px;">
                    {__d('template', 'ho_va_ten')}
                </td>

                <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                    {if !empty($contact.full_name)}
                        {$contact.full_name}
                    {/if}
                </td>
            </tr>

            <tr>
                <td style="border-top: 1px solid #4c4e4e; border-right: 1px solid #4c4e4e; padding: 10px;">
                    Email
                </td>

                <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                    {if !empty($contact.email)}
                        {$contact.email}
                    {/if}
                </td>
            </tr>

            <tr>
                <td style="border-top: 1px solid #4c4e4e; border-right: 1px solid #4c4e4e; padding: 10px;">
                    {__d('template', 'so_dien_thoai')}
                </td>

                <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                    {if !empty($contact.phone)}
                        {$contact.phone}
                    {/if}
                </td>
            </tr>

            <tr>
                <td style="border-top: 1px solid #4c4e4e; border-right: 1px solid #4c4e4e; padding: 10px;">
                    {__d('template', 'dia_chi')}
                </td>

                <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                    {if !empty($contact.full_address)}
                        {$contact.full_address}
                    {/if}
                </td>
            </tr>
        </tbody>
    </table>

    {if !empty($order_info.items)}
        <div style="margin-bottom:10px">
            <strong style="font-size:12px">
                {__d('template', 'thong_tin_san_pham')}:
            </strong>
        </div>

        <table width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #4c4e4e">
            <thead>
                <tr style="background: #4c4e4e; color: #fff;">
                    <th align="left" style="padding: 10px; font-weight: normal;">
                        {__d('template', 'san_pham')}
                    </th>

                    <th align="left" style="padding: 10px; font-weight: normal;">
                        {__d('template', 'don_gia')}
                    </th>

                    <th align="left" style="padding: 10px; font-weight: normal;">
                        {__d('template', 'so_luong')}
                    </th>

                    <th align="right" style="padding: 10px; font-weight: normal;">
                        {__d('template', 'tien')}
                    </th>
                </tr>
            </thead>

            <tbody>
                {foreach from = $order_info.items item = item key = key}
                    <tr>
                        <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {if !empty($item.name_extend)}
                                {$item.name_extend}
                            {/if}
                        </td>

                        <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {if !empty($item.price)}
                                {$item.price|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </td>

                        <td style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {if !empty($item.quantity)}
                                {$item.quantity|number_format:0:".":","}
                            {/if}
                        </td>

                        <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {if !empty($item.price) && !empty($item.quantity)}
                                {($item.quantity * $item.price)|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                            {$unit}
                        </td>
                    </tr>
                {/foreach}
            </tbody>

            <tfoot>
                <tr>
                    <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                        {__d('template', 'phi_van_chuyen')}
                    </td>
                    
                    <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                        {if !empty($order_info.shipping_fee_customer)}
                            + {$order_info.shipping_fee_customer|number_format:0:".":","}
                        {else}
                            0
                        {/if}
                        {$unit}
                    </td>
                </tr>

                {if !empty($order_info.total_coupon)}
                    <tr>
                        <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {__d('template', 'phieu_giam_gia')}
                        </td>
                        
                        <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            - {$order_info.total_coupon|number_format:0:".":","} {$unit}
                        </td>
                    </tr>
                {/if}

                {if !empty($order_info.total_affiliate)}
                    <tr>
                        <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            {__d('template', 'ma_gioi_thieu')}
                        </td>
                        
                        <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            - {$order_info.total_affiliate|number_format:0:".":","} {$unit}
                        </td>
                    </tr>
                {/if}

                {if !empty($order_info.total_vat)}
                    <tr>
                        <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            VAT
                        </td>
                        
                        <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                            + {$order_info.total_vat|number_format:0:".":","} {$unit}
                        </td>
                    </tr>
                {/if}

                <tr>
                    <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                        <strong>
                            {__d('template', 'tong_tien')}
                        </strong>
                    </td>
                    
                    <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                        <strong>
                            {if !empty($order_info.total)}
                                {$order_info.total|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                            {$unit}
                        </strong>
                    </td>
                </tr>
                {if !empty($order_info.point_promotion_paid) || !empty($order_info.point_paid)} 
                    {if !empty($order_info.point_promotion_paid)}
                        <tr>
                            <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                {__d('template', 'thanh_toan_bang_diem_khuyen_mai')}
                            </td>
                            
                            <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                -  {$order_info.point_promotion_paid|number_format:0:".":","} {$unit}
                            </td>
                        </tr>
                    {/if}

                    {if !empty($order_info.point_paid)}
                        <tr>
                            <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                {__d('template', 'thanh_toan_bang_diem_vi')}
                            </td>
                            
                            <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                - {$order_info.point_paid|number_format:0:".":","} {$unit}
                            </td>
                        </tr>
                    {/if}
                    {if !empty($order_info.debt)}
                        <tr>
                            <td align="right" colspan="3" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                {__d('template', 'con_phai_thanh_toan')}
                            </td>
                            
                            <td align="right" style="border-top: 1px solid #4c4e4e; padding: 10px;">
                                {$order_info.debt|number_format:0:".":","} {$unit}
                            </td>
                        </tr>
                    {/if}
                {/if}

            </tfoot>
        </table>
    {/if}
{/if}