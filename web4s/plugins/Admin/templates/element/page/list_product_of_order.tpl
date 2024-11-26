<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'thong_tin_san_pham')}
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="table-responsive nh-table-responsive">
            <table id="table-products" class="table mb-0 nh-table-item">
                <thead class="thead-light">
                    <tr>
                        <th class="text-left" style="width: 150px;">
                            {__d('admin', 'ma')}
                        </th>

                        <th class="text-left">
                            {__d('admin', 'ten_san_pham')}
                        </th>

                        <th style="width: 100px;">
                            {__d('admin', 'so_luong')}
                        </th>

                        <th style="width: 100px;">
                            {__d('admin', 'gia')}
                        </th>

                        <th style="width: 120px;">
                            {__d('admin', 'chiet_khau')}
                        </th>

                        <th style="width: 120px;">
                            {__d('admin', 'VAT')}(%) 
                        </th>

                        <th class="text-right" style="width: 150px;">
                            {__d('admin', 'thanh_tien')}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {if !empty($order.items)}
                        {foreach from = $order.items item = item}
                            {assign var = total_discount value = 0}
                            {if !empty($item.total_discount)}
                                {assign var = total_discount value = $item.total_discount}
                            {/if}

                            {assign var = total_vat value = 0}
                            {if !empty($item.total_vat)}
                                {assign var = total_vat value = $item.total_vat}
                            {/if}

                            {assign var = vat_value value = 0}
                            {if !empty($item.vat_value)}
                                {assign var = vat_value value = $item.vat_value}
                            {/if}

                            {assign var = discount_value value = 0}
                            {if !empty($item.discount_value)}
                                {assign var = discount_value value = $item.discount_value}
                            {/if}

                            {assign var = discount_type value = ''}
                            {if !empty($item.discount_type)}
                                {assign var = discount_type value = $item.discount_type}
                            {/if}

                            {assign var = price_item value = 0}
                            {if !empty($item.price)}
                                {assign var = price_item value = $item.price}
                            {/if}                            

                            {if !empty($discount_value) && $discount_type == "{PERCENT}"}
                                {$price_item = $price_item / ( 100 - $discount_value) * 100}
                            {/if}

                            {if !empty($discount_value) && $discount_type == "{MONEY}"}
                                {$price_item = $price_item + $discount_value}
                            {/if}

                            <tr>
                                <td>
                                    {if !empty($item.code)}
                                        {$item.code}
                                    {/if}
                                </td>
                                    
                                <td>
                                    {if !empty($item.name_extend)}
                                        {$item.name_extend}
                                    {/if}
                                </td>
                                    
                                <td>
                                    {if !empty($item.quantity)}
                                        {$item.quantity|number_format:0:".":","}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($price_item)}
                                        {$price_item|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}
                                </td>
                                                                                    
                                <td>
                                    {if !empty($total_discount)}
                                        {$total_discount|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}

                                    {if !empty($item.discount_value) && !empty($item.discount_type) && $item.discount_type == "{PERCENT}"}
                                        <span class="text-danger">
                                            ({$item.discount_value} %)
                                        </span>
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($total_vat)}
                                        {$total_vat|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}

                                    {if !empty($item.vat_value)}
                                        <span class="text-danger">({$item.vat_value} %)</span>
                                    {/if}
                                </td>

                                <td class="text-right">
                                    {if !empty($item.total_item)}
                                        {$item.total_item|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3"></td>

                        <td colspan="2">
                            {__d('admin', 'tong_tien')}
                            {if !empty($order.count_items)}
                                ({$order.count_items|number_format:0:".":","} sản phẩm)
                            {/if}
                        </td>

                        <td colspan="2" class="text-right">
                            {if !empty($order.total_items)}
                                {$order.total_items|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>

                        <td colspan="2">
                            {assign var = total_discount value = '0'}
                            {if !empty($order.total_discount)}
                                {assign var = total_discount value = $order.total_discount}
                            {/if}

                            {assign var = discount_note value = '0'}
                            {if !empty($order.discount_note)}
                                {assign var = discount_note value = $order.discount_note}
                            {/if}
                            {assign var = html_discount value = "<p>{__d('admin', 'chiet_khau')}: {$total_discount}</p>
                            <p>{__d('admin', 'ly_do')}: {$discount_note}</p>"}
                            <span class="kt-link" data-toggle="kt-popover" data-html="true" data-original-title="{__d('admin', 'chiet_khau_don_hang')}" data-content="{$html_discount}" >
                                {__d('admin', 'chiet_khau')}
                            </span>
                        </td>

                        <td colspan="2" class="text-right">
                            {if !empty($order.total_discount)}
                                {$order.total_discount|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>

                        <td colspan="2">
                            {__d('admin', 'VAT')}
                        </td>

                        <td colspan="2" class="text-right">
                            {if !empty($order.total_vat)}
                                {$order.total_vat|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>

                        <td colspan="2">
                            {__d('admin', 'phi_van_chuyen')}
                        </td>

                        <td colspan="2" class="text-right">
                            {if !empty($order.shipping_fee_customer)}
                                {$order.shipping_fee_customer|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>

                        <td colspan="2">
                            {__d('admin', 'khach_phai_tra')}
                        </td>

                        <td colspan="2" class="text-right">
                            <strong>
                                {if !empty($order.total)}                                            
                                    {$order.total|number_format:0:".":","}                                            
                                {else}
                                    0
                                {/if}                                            
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>