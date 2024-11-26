{if !empty($shippings)}
    <div class="clearfix p-15">
        <div class="row">
            <div class="col-xl-12 pl-20">
                <div class="kt-timeline-v1 kt-timeline-v1--justified nh-timeline-v1">
                    <div class="kt-timeline-v1__items pt-5 mb-0">
                        {foreach from = $shippings item = shipping key = k_shipping name = 'shipping_foreach'}
                            {assign var = type_badge value = ''}
                            {if isset($shipping.status) && $shipping.status|in_array:[CANCEL_PACKAGE, CANCEL_WAIT_DELIVER, CANCEL_DELIVERED]}
                                {assign var = type_badge value = 'dark'}
                            {/if}

                            {if isset($shipping.status) && $shipping.status == {DELIVERED}}
                                {assign var = type_badge value = 'success'}
                            {/if}

                            {if isset($shipping.status) && $shipping.status == {WAIT_DELIVER}}
                                {assign var = type_badge value = 'danger'}
                            {/if}

                            {if isset($shipping.status) && $shipping.status == {DELIVERY}}
                                {assign var = type_badge value = 'brand'}
                            {/if}

                            <div class="kt-timeline-v1__marker"></div>
                            <div class="kt-timeline-v1__item">
                                <div class="kt-timeline-v1__item-circle">
                                    <div class="kt-bg-{$type_badge}"></div>
                                </div>

                                {assign var = show_shipping value = false}
                                {assign var = status_show value = [WAIT_DELIVER, DELIVERY, CANCEL_WAIT_DELIVER]}
                                {if $smarty.foreach.shipping_foreach.last && $shipping.status|in_array:$status_show}
                                    {assign var = show_shipping value = true}
                                {/if}

                                <div class="kt-timeline-v1__item-content p-10">
                                    <div class="kt-timeline-v1__item-title p-5">
                                        {if !empty($shipping.code)}
                                            <span class="mr-10">
                                                <a href="{ADMIN_PATH}/shipment/detail/{if !empty($shipping.id)}{$shipping.id}{/if}" target="_blank">
                                                    {$shipping.code}
                                                </a>                                                
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {WAIT_DELIVER}}
                                            <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'cho_lay_hang')}
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {DELIVERY}}
                                            <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'dang_giao_hang')}
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {DELIVERED}}
                                            <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'da_giao_hang')}
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {CANCEL_PACKAGE}}
                                            <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'huy_dong_goi')}
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {CANCEL_WAIT_DELIVER}}
                                            <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'huy_giao_va_cho_nhan')}
                                            </span>
                                        {/if}

                                        {if isset($shipping.status) && $shipping.status == {CANCEL_DELIVERED}}
                                            <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20">
                                                {__d('admin', 'huy_giao_va_da_nhan')}
                                            </span>
                                        {/if}

                                        <i data-toggle="collapse" href="#bill-shipping-{$shipping.id}" class="fa {if !empty($show_shipping)}fa-caret-down{else}fa-caret-right{/if} ml-10 mt-2 cursor-p h-20 float-right icon-arrow-shipping"></i>
                                    </div>

                                    <div id="bill-shipping-{$shipping.id}" class="clearfix collapse {if !empty($show_shipping)}show{/if}">
                                        <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                        <div class="kt-timeline-v1__item-body mt-10 p-5">
                                            <div class="row mb-10">
                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'phuong_thuc_van_chuyen')}
                                                        </label>

                                                        <div class="col-12">
                                                            {if isset($shipping.shipping_method) && $shipping.shipping_method == {RECEIVED_AT_STORE}}
                                                                <span class="kt-font-bolder">
                                                                    {__d('admin', 'nhan_tai_cua_hang')}
                                                                </span>
                                                            {/if}

                                                            {if isset($shipping.shipping_method) && $shipping.shipping_method == {NORMAL_SHIPPING}}
                                                                <span class="kt-font-bolder">
                                                                    {__d('admin', 'tu_van_chuyen')}
                                                                </span>
                                                            {/if}

                                                            {if isset($shipping.shipping_method) && $shipping.shipping_method == {SHIPPING_CARRIER}}
                                                                <span class="kt-font-bolder">
                                                                    {__d('admin', 'gui_qua_hang_van_chuyen')}
                                                                </span>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                </div>

                                                {if !empty($shipping.carrier_code)}
                                                    <div class="col-lg-4 col-xl-4">
                                                        <div class="form-group form-group-xs row">
                                                            <label class="col-12">
                                                                {__d('admin', 'hang_van_chuyen')}
                                                            </label>
                                                            
                                                            <div class="col-12">
                                                                {if $shipping.carrier_code == GIAO_HANG_NHANH}
                                                                    <span class="kt-font-bolder">
                                                                        GIAO HANG NHANH
                                                                    </span>
                                                                {/if}

                                                                {if $shipping.carrier_code == GIAO_HANG_TIET_KIEM}
                                                                    <span class="kt-font-bolder">
                                                                        GIAO HANG TIET KIEM
                                                                    </span>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}

                                                {if !empty($shipping.carrier_code)}
                                                    <div class="col-lg-4 col-xl-4">
                                                        <div class="form-group form-group-xs row">
                                                            <label class="col-12">
                                                                {__d('admin', 'ma_don_hang_van_chuyen')}
                                                            </label>
                                                            
                                                            <div class="col-12">
                                                                {if !empty($shipping.carrier_order_code)}
                                                                    <span class="kt-font-bolder">
                                                                        {$shipping.carrier_order_code}
                                                                    </span>
                                                                {else}
                                                                    <i class="text-danger fs-11">
                                                                        {__d('admin', 'chua_gui_don_sang_hang_van_chuyen')}
                                                                    </i>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            </div> 

                                            <div class="row">
                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'tien_thu_ho')}
                                                        </label>
                                                        <div class="col-12">
                                                            <span class="kt-font-bolder">
                                                                {if !empty($shipping.cod_money)}
                                                                    {$shipping.cod_money|number_format:0:".":","}
                                                                {else}
                                                                    0
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'phi_tra_doi_tac_van_chuyen')}
                                                        </label>
                                                        <div class="col-12">
                                                            <span class="kt-font-bolder">
                                                                {if !empty($shipping.shipping_fee)}
                                                                    {$shipping.shipping_fee|number_format:0:".":","}
                                                                {else}
                                                                    0
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'phi_van_chuyen_thu_cua_khach')}
                                                        </label>
                                                        <div class="col-12">
                                                            <span class="kt-font-bolder">
                                                                {if !empty($shipping.shipping_fee_customer)}
                                                                    {$shipping.shipping_fee_customer|number_format:0:".":","}
                                                                {else}
                                                                    0
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {assign var = wait_deliver_action value = false}
                                            {assign var = delivery_action value = false}
                                            {assign var = delivered_action value = false}
                                            {assign var = cancel_wait_deliver_action value = false}
                                            {assign var = cancel_delivered_action value = false}

                                            {if $smarty.foreach.shipping_foreach.last && isset($shipping.status) && $shipping.status == {WAIT_DELIVER}}
                                                {assign var = wait_deliver_action value = true}
                                            {/if}

                                            {if $smarty.foreach.shipping_foreach.last && isset($shipping.status) && $shipping.status == {DELIVERY}}
                                                {assign var = delivery_action value = true}
                                            {/if}

                                            {if $smarty.foreach.shipping_foreach.last && isset($shipping.status) && $shipping.status == {DELIVERED}}
                                                {assign var = delivered_action value = true}
                                            {/if}

                                            {if $smarty.foreach.shipping_foreach.last && isset($shipping.status) && $shipping.status == {CANCEL_WAIT_DELIVER}}
                                                {assign var = cancel_wait_deliver_action value = true}
                                            {/if}

                                            {if $smarty.foreach.shipping_foreach.last && isset($shipping.status) && $shipping.status == {CANCEL_DELIVERED}}
                                                {assign var = cancel_delivered_action value = true}
                                            {/if}

                                            {if !empty($wait_deliver_action)}
                                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                                <div class="row mt-20">
                                                    <div class="col-12">
                                                        <span data-status="{CANCEL_PACKAGE}" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" class="btn btn-sm btn-secondary bg-white btn-shipping-status">
                                                            {__d('admin', 'huy_dong_goi')}
                                                        </span>

                                                        <span data-status="{DELIVERY}" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" class="btn btn-sm btn-brand float-right btn-shipping-status" >
                                                            {__d('admin', 'xuat_kho')}
                                                        </span>
                                                    </div>
                                                </div>
                                            {/if}

                                            {if !empty($delivery_action)}
                                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                                <div class="row mt-20">
                                                    <div class="col-12">
                                                        <span data-status="{CANCEL_WAIT_DELIVER}" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" class="btn btn-sm btn-secondary bg-white btn-shipping-status">
                                                            {__d('admin', 'huy_giao_hang')}
                                                        </span>

                                                        <span data-status="{CANCEL_DELIVERED}" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" class="btn btn-sm btn-secondary bg-white btn-shipping-status">
                                                            {__d('admin', 'huy_giao_va_nhan_lai_hang')}
                                                        </span>

                                                        <span data-status="{DELIVERED}" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" class="btn btn-sm btn-brand float-right btn-shipping-status">
                                                            {__d('admin', 'da_giao_hang')}
                                                        </span>
                                                    </div>
                                                </div>
                                            {/if}

                                            {if !empty($delivered_action) && empty($order_returned)}
                                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10 d-none"></div>

                                                <div class="row mt-20 d-none">
                                                    <div class="col-12">
                                                        <a href="{ADMIN_PATH}/order/return/create/{if !empty($order.id)}{$order.id}{/if}" class="btn btn-sm btn-secondary bg-white float-right">
                                                            {__d('admin', 'hoan_tra')}
                                                        </a>
                                                    </div>
                                                </div>
                                            {/if}

                                            {if !empty($cancel_wait_deliver_action)}
                                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>
                                                
                                                <div class="row mt-20">
                                                    <div class="col-12">
                                                        <span class="btn btn-sm btn-secondary bg-white float-right btn-shipping-status" data-status="{CANCEL_DELIVERED}">
                                                            {__d('admin', 'nhan_hang')}
                                                        </span>
                                                    </div>
                                                </div>
                                            {/if}
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}                                            
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}