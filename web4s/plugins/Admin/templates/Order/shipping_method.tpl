{assign var = shipping_carries value = $this->ShippingAdmin->getListShippingCarrier()}

<div id="wrap-shipping-method" class="clearfix p-15 collapse">
    <div class="kt-section mb-10">
        <div class="kt-section__content kt-section__content--solid p-10">
            {if !empty($order.shipping_method_id)}
                {assign var = method_name value = $this->ShippingAdmin->getShippingMethodName($order.shipping_method_id, LANGUAGE_ADMIN)}
                <div class="form-group">
                    <label>
                        {__d('admin', 'phuong_thuc_van_chuyen_da_chon')}:
                    </label>
                    <p class="font-weight-bold">
                        {if !empty($method_name)}
                            {$method_name}
                        {/if}
                    </p>
                </div>
            {/if}

            {* nếu khách hàng đã chọn phương thức vận chuyển và đã tính phí ship thì không được thay đổi giá vận chuyển của khách hàng nữa *}
            {assign var = fix_shipping_fee_customer value = false}
            {if !empty($order.shipping_method_id)}
                {$fix_shipping_fee_customer = true}
            {/if}
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="form-group mb-5">
                        <label>
                            {__d('admin', 'phi_van_chuyen_thu_khach_hang')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-money-bill-alt"></i>
                                </span>
                            </div>
                            <input name="shipping_fee_customer" value="{if !empty($order.shipping_fee_customer)}{$order.shipping_fee_customer}{/if}" type="text" class="form-control form-control-sm text-right number-input {if $fix_shipping_fee_customer}disabled{/if}" {if $fix_shipping_fee_customer}readonly="true"{/if}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                                
    <div class="clearfix">
        <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-brand mb-10" role="tablist">
            <li class="nav-item mr-30">
                <a class="nav-link active" data-shipping="{RECEIVED_AT_STORE}" href="#shipping-1" data-toggle="tab"  role="tab">
                    <i class="fa fa-store"></i>
                    {__d('admin', 'nhan_tai_cua_hang')}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-shipping="{NORMAL_SHIPPING}" href="#shipping-2" data-toggle="tab" role="tab">
                    <i class="fa fa-truck-moving"></i> 
                    {__d('admin', 'tu_van_chuyen')}
                </a>
            </li>

            {if !empty($shipping_carries)}
                <li class="nav-item">
                    <a class="nav-link" data-shipping="{SHIPPING_CARRIER}" href="#shipping-2" role="tab" data-toggle="tab">
                        <i class="fa fa-shipping-fast"></i> 
                        {__d('admin', 'gui_qua_hang_van_chuyen')}
                    </a>
                </li>
            {/if}
        </ul>

        <div class="tab-content">
            <div id="shipping-1" class="tab-pane active" role="tabpanel">
                <div class="kt-section mb-10">
                    <div class="kt-section__content kt-section__content--solid p-10">
                        <p class="mb-0">
                            {__d('admin', 'khach_hang_se_den_cua_hang_cua_ban_de_lay_hang')}
                        </p>
                    </div>
                </div>
            </div>

            <div id="shipping-2" class="tab-pane" role="tabpanel">
                <div data-type="shipping_info" class="wrap-shipping-method clearfix">
                    <div class="kt-section mb-30">
                        <span class="kt-section__info">
                            {__d('admin', 'dia_chi_giao_hang')}:
                        </span>

                        <div class="kt-section__content kt-section__content--solid p-10">
                            <p>
                                <span label-shipping-address-name></span>
                            </p>

                            <p>
                                <span label-shipping-phone></span>
                            </p>

                            <p class="mb-0">
                                <span label-shipping-address></span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tien_thu_ho')}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-money-bill-alt"></i>
                                        </span>
                                    </div>                                        
                                    <input name="cod_money" value="" type="text" class="form-control form-control-sm text-right number-input">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ghi_chu')}
                                </label>
                                <input name="shipping_note" value="" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="kt-separator kt-separator--space-md kt-separator--border-soild"></div>

                    <div class="row">
                        <div class="col-xl-4 col-lg-4">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'phi_tra_doi_tac_van_chuyen')}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-money-bill-alt"></i>
                                        </span>
                                    </div>
                                    <input name="shipping_fee" value="" type="text" class="form-control form-control-sm text-right number-input">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'khoi_luong')} (gam)
                                </label>
                                <input name="weight" value="" type="text" class="form-control form-control-sm number-input">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chieu_dai')}
                                </label>
                                <input name="length" value="" type="text" class="form-control form-control-sm number-input">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chieu_rong')}
                                </label>
                                <input name="width" value="" type="text" class="form-control form-control-sm number-input">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chieu_cao')} (cm)
                                </label>
                                <input name="height" value="" type="text" class="form-control form-control-sm number-input">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            {if !$fix_shipping_fee_customer}
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ap_dung_phi_van_chuyen_cho_khach_hang')}
                                    </label>
                                    <div class="kt-radio-inline mt-5">
                                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                                            <input type="radio" name="apply_for_customer" value="1"> 
                                            {__d('admin', 'co')}
                                            <span></span>
                                        </label>

                                        <label class="kt-radio kt-radio--tick kt-radio--success">
                                            <input type="radio" name="apply_for_customer" value="0" checked> 
                                            {__d('admin', 'khong')}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            {/if}

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'yeu_cau')}
                                </label>
                                {$this->Form->select('required_note', $this->ShippingAdmin->getListRequiredNote(), ['empty' => null, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            
                        </div>
                    </div>                    
                    
                    {if !empty($shipping_carries)}
                        <div id="wrap-shipping-carries"></div>
                    {/if}
                </div>

                <div data-type="no_customer" class="wrap-shipping-method d-none">
                    <div class="kt-section mb-10">
                        <div class="kt-section__content kt-section__content--solid p-10">
                            <span label-error-message>
                                {__d('admin', 'ban_hay_them_khach_hang_de_su_dung_dich_vu_giao_hang')}
                            </span>.                          
                            <a class="trigger-customer" href="javascript:;">
                                {__d('admin', 'cap_nhat_tai_day')}
                            </a>
                        </div>
                    </div>
                </div>

                <div data-type="no_district" class="wrap-shipping-method d-none">
                    <div class="kt-section mb-10">
                        <div class="kt-section__content kt-section__content--solid p-10">
                            <span label-error-message>
                                {__d('admin', 'ban_hay_them_khu_vuc_quan_huyen_cua_khach_hang_de_su_dung_dich_vu_giao_hang')}
                            </span>.                          
                            <a class="trigger-district" href="javascript:;">
                                {__d('admin', 'cap_nhat_tai_day')}
                            </a>
                        </div>
                    </div>
                </div>

                <div data-type="no_ward" class="wrap-shipping-method d-none">
                    <div class="kt-section mb-10">
                        <div class="kt-section__content kt-section__content--solid p-10">
                            <span label-error-message>
                                {__d('admin', 'ban_hay_them_khu_vuc_phuong_xa_cua_khach_hang_de_su_dung_dich_vu_hang_van_chuyen')}
                            </span>.                          
                            <a class="trigger-district" href="javascript:;">
                                {__d('admin', 'cap_nhat_tai_day')}
                            </a>
                        </div>
                    </div>
                </div>

                <div data-type="no_product" class="wrap-shipping-method d-none">
                    <div class="kt-section mb-10">
                        <div class="kt-section__content kt-section__content--solid p-10">
                            <span label-error-message>
                                {__d('admin', 'ban_hay_them_san_pham_vao_don_hang_de_su_dung_dich_vu_giao_hang')}
                            </span>.
                            <a class="trigger-product" href="javascript:;">
                                {__d('admin', 'cap_nhat_tai_day')}
                            </a>
                        </div>
                    </div>            
                </div>
            </div>
        </div>
    </div>

    <input name="shipping_method" value="" type="hidden">
    
    <input name="carrier_code" value="" type="hidden">
    <input name="carrier_service_code" value="" type="hidden">
    <input name="carrier_service_type_code" value="" type="hidden">
    <input name="carrier_shop_id" value="" type="hidden">    
</div>