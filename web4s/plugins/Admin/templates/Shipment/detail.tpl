<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'chi_tiet_don_van')}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($shipping.status)}
                <div class="btn-group">
                    {if $shipping.status == {WAIT_DELIVER}}
                        <button type="button" class="btn btn-sm btn-primary btn-shipping-status" data-status="{DELIVERY}">
                            {__d('admin', 'dang_giao_hang')}
                        </button>

                        <button type="button" class="btn btn-sm btn-dark btn-shipping-status" data-status="{CANCEL_PACKAGE}">
                            {__d('admin', 'huy_dong_goi')}
                        </button>
                    {/if}

                    {if $shipping.status == {DELIVERY}}
                        <button type="button" class="btn btn-sm btn-success btn-shipping-status" data-status="{DELIVERED}">
                            {__d('admin', 'da_giao_hang')}
                        </button>

                        <button type="button" class="btn btn-sm btn-dark btn-shipping-status" data-status="{CANCEL_WAIT_DELIVER}">
                            {__d('admin', 'huy_giao_va_cho_nhan')}
                        </button>

                        <button type="button" class="btn btn-sm btn-dark btn-shipping-status" data-status="{CANCEL_DELIVERED}">
                            {__d('admin', 'huy_giao_va_da_nhan')}
                        </button>
                    {/if}

                    {if $shipping.status == {CANCEL_WAIT_DELIVER}}
                        <button type="button" class="btn btn-sm btn-dark btn-shipping-status" data-status="{CANCEL_DELIVERED}">
                            {__d('admin', 'huy_giao_va_da_nhan')}
                        </button>
                    {/if}
                </div>
            {/if}
        </div>
    </div>
</div>
<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    {if !empty($shipping.id)}
        <div class="kt-portlet nh-portlet">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_don_van')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ma_don_van')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.code)}
                                        {$shipping.code}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ma_hoa_don')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($order.code)}
                                        <a href="{ADMIN_PATH}/order/detail/{$order.id}">
                                            {$order.code}
                                        </a>
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'tinh_trang_giao_hang')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                {if isset($shipping.status) && $shipping.status == {WAIT_DELIVER}}
                                    <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'cho_lay_hang')}
                                    </span>
                                {/if}

                                {if isset($shipping.status) && $shipping.status == {DELIVERY}}
                                    <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'dang_giao_hang')}
                                    </span>
                                {/if}

                                {if isset($shipping.status) && $shipping.status == {DELIVERED}}
                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'da_giao_hang')}
                                    </span>
                                {/if}

                                {if isset($shipping.status) && $shipping.status == {CANCEL_PACKAGE}}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'huy_dong_goi')}
                                    </span>
                                {/if}

                                {if isset($shipping.status) && $shipping.status == {CANCEL_WAIT_DELIVER}}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'huy_giao_va_cho_nhan')}
                                    </span>
                                {/if}

                                {if isset($shipping.status) && $shipping.status == {CANCEL_DELIVERED}}
                                    <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20 mt-10">
                                        {__d('admin', 'huy_giao_va_da_nhan')}
                                    </span>
                                {/if}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'phuong_thuc')}</label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if isset($shipping.shipping_method) && $shipping.shipping_method == {RECEIVED_AT_STORE}}
                                        {__d('admin', 'nhan_tai_cua_hang')}
                                    {/if}

                                    {if isset($shipping.shipping_method) && $shipping.shipping_method == {NORMAL_SHIPPING}}
                                        {__d('admin', 'van_chuyen_thong_thuong')}
                                    {/if}

                                    {if isset($shipping.shipping_method) && $shipping.shipping_method == {SHIPPING_CARRIER}}
                                        {__d('admin', 'gui_hang_van_chuyen')}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'tien_thu_ho_cod')}</label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.cod_money)}
                                        {$shipping.cod_money|number_format:0:".":","}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'phi_tinh_cho_khach')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.shipping_fee_customer)}
                                        {$shipping.shipping_fee_customer|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'phi_tra_doi_tac_van_chuyen')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.shipping_fee)}
                                        {$shipping.shipping_fee|number_format:0:".":","}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        {if !empty($shipping.carrier_code)}
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-4 col-form-label">
                                    {__d('admin', 'hang_van_chuyen')}
                                </label>

                                <div class="col-lg-8 col-xl-9">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if $shipping.carrier_code == GIAO_HANG_NHANH}
                                            GIAO HANG NHANH
                                        {/if}

                                        {if $shipping.carrier_code == GIAO_HANG_TIET_KIEM}
                                            GIAO HANG TIET KIEM
                                        {/if}

                                        {if !empty($shipping.carrier_order_code)}
                                            <p class="m-0">
                                                {__d('admin', 'ma_don')}:
                                                {$shipping.carrier_order_code}
                                            </p>
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        {/if}                    
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_nguoi_nhan')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'nguoi_nhan')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.full_name)}
                                        {$shipping.full_name}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'so_dien_thoai')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.phone)}
                                        {$shipping.phone}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'dia_chi')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.full_address)}
                                        {$shipping.full_address}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ghi_chu')}
                            </label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.note)}
                                        {$shipping.note}
                                    {else}
                                        ...
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">{__d('admin', 'trong_luong')}</label>
                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.weight)}
                                        {$shipping.weight} (gam)
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'kich_thuoc')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($shipping.length)}
                                        {$shipping.length}
                                    {else}
                                        ...
                                    {/if}
                                         x 
                                    {if !empty($shipping.width)}
                                        {$shipping.width}
                                    {else}
                                        ...
                                    {/if}
                                         x 
                                    {if !empty($shipping.height)}
                                        {$shipping.height}
                                    {else}
                                        ...
                                    {/if}
                                    (cm)
                                </span>

                                <span>
                                    ({__d('admin', 'dai')} x 
                                    {__d('admin', 'rong')} x 
                                    {__d('admin', 'cao')}) 
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    {/if}

    {$this->element('Admin.page/list_product_of_order')}
</div>

<div id="shipping-status-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" label-title></h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mt-10 mb-10">
                        <span label-message>
                        </span>
                    </div>
                </div>            
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>

                <button id="btn-change-status" data-status="" data-shipping-id="{if !empty($shipping.id)}{$shipping.id}{/if}" data-order-id="{if !empty($shipping.order_id)}{$shipping.order_id}{/if}" type="button" class="btn btn-sm btn-brand">
                </button>
            </div>
        </div>
    </div>
</div>