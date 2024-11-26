{assign var = url_list value = "{ADMIN_PATH}/order"}
{assign var = url_add value = "{ADMIN_PATH}/order/add"}
{assign var = url_edit value = "{ADMIN_PATH}/order/update"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($order.id)}
                <a href="{ADMIN_PATH}/order/detail/{$order.id}" class="btn btn-sm btn-secondary btn-confirm-payment">
                    {__d('admin', 'huy')}
                </a>

                <button data-update="1" data-link="{ADMIN_PATH}/order/detail" type="button" class="btn btn-sm btn-brand btn-save">
                    {__d('admin', 'cap_nhat')}
                </button>
            {else}
                {$this->element('../Order/element_btn_submit', ['order_status' => "{if !empty($order.status)}{$order.status}{/if}"])}
            {/if}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/order/save{if !empty($order.id)}/{$order.id}{/if}" check-promotion="{if !empty($check_promotion)}1{/if}" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__body">
                        {$this->element('../Order/element_status', ['order_status' => "{if !empty($order.status)}{$order.status}{/if}"])}
                    </div>
                </div>

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_don_hang')}
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ma_don_hang')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-qrcode"></i>
                                            </span>
                                        </div>
                                        <input name="code" value="{if !empty($order.code)}{$order.code}{/if}" type="text" class="form-control form-control-sm {if !empty($order.code)}disabled{/if}" {if !empty($order.code)}readonly="true"{/if}>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'nguon_don_hang')}
                                    </label>
                                    {$this->Form->select('source', $this->ObjectAdmin->getListOrderSource(), ['id' => 'source', 'empty' => "-- {__d('admin', 'nguon_don_hang')} --", 'default' => "{if !empty($order.source)}{$order.source}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ghi_chu')}
                                    </label>

                                    <input name="note" value="{if !empty($order.note)}{$order.note}{/if}" type="text" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'nhan_vien')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-user-circle"></i>
                                            </span>
                                        </div>
                                        <input id="staff-suggest" value="{if !empty($order.staff.full_name)}{$order.staff.full_name}{/if}" type="text" class="form-control form-control-sm">
                                        <input name="staff_id" value="{if !empty($order.staff_id)}{$order.staff_id}{/if}" type="hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_khach_hang')}
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        
                        <div id="customer-search" class="form-group {if !empty($order.contact)}d-none{/if}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="flaticon-search"></i>
                                    </span>
                                </div>
                                <input id="customer-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_khach_hang_f4')}" shortcut="115">                                
                            </div>
                        </div>
                        
                        <div id="customer-info" class="clearfix {if empty($order.contact)}d-none{/if}">
                            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                                <div class="kt-widget__head">
                                    <div class="kt-widget__content p-0">
                                        <div class="kt-widget__section">
                                            <a href="javascript:;" class="kt-widget__username fs-16">                    
                                                <span label-customer-full_name="{if !empty($order.contact.full_name)}{$order.contact.full_name}{/if}" class="kt-link">
                                                    {if !empty($order.contact.full_name)}{$order.contact.full_name}{/if}
                                                </span>
                                                <i id="customer-remove" class="fa fa-times text-muted"></i>                                    
                                            </a>

                                            <span label-customer-phone="{if !empty($order.contact.phone)}{$order.contact.phone}{/if}" class="kt-widget__subtitle">
                                                {if !empty($order.contact.phone)}{$order.contact.phone}{/if}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                <div class="kt-widget__body">
                                    <div class="kt-widget__content p-0">
                                        <div class="kt-widget__info">
                                            <span label-customer-address-name="{if !empty($order.contact.address_name)}{$order.contact.address_name}{/if}" class="kt-widget__data">
                                                {if !empty($order.contact.address_name)}{$order.contact.address_name}{/if}
                                            </span>
                                        </div>

                                        <div class="kt-widget__info">
                                            <span label-customer-phone="{if !empty($order.contact.phone)}{$order.contact.phone}{/if}" class="kt-widget__data">
                                                {if !empty($order.contact.phone)}{$order.contact.phone}{/if}
                                            </span>
                                        </div>

                                        <div class="kt-widget__info">
                                            <span label-customer-address="{if !empty($order.contact.full_address)}{$order.contact.full_address}{/if}" class="kt-widget__data">
                                                {if !empty($order.contact.full_address)}{$order.contact.full_address}{/if}
                                            </span>
                                        </div>

                                        <div class="kt-widget__info">
                                            <a id="cusomer-change-address" href="javascript:;" data-toggle="popover">
                                                {__d('admin', 'thay_doi_dia_chi_giao_hang')}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_san_pham')}
                            </h3>
                        </div>                        
                    </div>
                    <div class="kt-portlet__body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="flaticon-search"></i>
                                    </span>
                                </div>
                                <input id="product-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_san_pham_f3')}" shortcut="114">
                            </div>
                        </div>

                        <div class="table-responsive nh-table-responsive">
                            <table id="table-products" class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left w-10">
                                            {__d('admin', 'ma')}
                                        </th>

                                        <th class="text-left">
                                            {__d('admin', 'ten_san_pham')}
                                        </th>

                                        <th class="text-center w-15">
                                            {__d('admin', 'so_luong')}
                                        </th>

                                        <th class="text-center w-15">
                                            {__d('admin', 'gia')}
                                        </th>
                                        <th class="text-right w-15">
                                            {__d('admin', 'thanh_tien')}
                                        </th>            
                                                        
                                        <th class="text-center w-3">
                                            
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {if !empty($order.items)}
                                        {foreach from = $order.items item = item}
                                            {$this->element('Admin.row_table/row', ['data_item' => $item])}
                                        {/foreach}
                                    {else}
                                        {$this->element('Admin.row_table/row')}
                                    {/if}
                                                                        
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>

                                        <td colspan="2">
                                            {__d('admin', 'tong_tien')}
                                        </td>

                                        <td class="text-right">
                                            <span label-total-items="{if !empty($order.total_items)}{$order.total_items}{/if}">
                                                {if !empty($order.total_items)}
                                                    {$order.total_items|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                            </span>
                                        </td>

                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>

                                        <td colspan="2">
                                            <a data-discount="{if !empty($order.discount_value)}{$order.discount_value}{/if}" data-discount-type="{if !empty($order.discount_type)}{$order.discount_type}{/if}" data-discount-note="{if !empty($order.discount_note)}{$order.discount_note}{/if}" href="javascript:;">
                                                {__d('admin', 'chiet_khau')}
                                                <i class="fa fa-caret-down"></i>
                                            </a>
                                        </td>

                                        <td class="text-right">
                                            <span label-total-discount="{if !empty($order.total_discount)}{$order.total_discount}{/if}">
                                                {if !empty($order.total_discount)}
                                                    {$order.total_discount|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                            </span>
                                        </td>

                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>

                                        <td colspan="2">
                                            {__d('admin', 'VAT')}
                                        </td>

                                        <td class="text-right">
                                            <span label-total-vat="{if !empty($order.total_vat)}{$order.total_vat}{/if}">
                                                {if !empty($order.total_vat)}
                                                    {$order.total_vat|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                            </span>
                                        </td>

                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>

                                        <td colspan="2">
                                            {__d('admin', 'phi_van_chuyen')}
                                        </td>

                                        <td class="text-right">
                                            <span label-shipping-fee-customer="{if !empty($order.shipping_fee_customer)}{$order.shipping_fee_customer}{/if}">
                                                {if !empty($order.shipping_fee_customer)}
                                                    {$order.shipping_fee_customer|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                            </span>
                                        </td>

                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="2"></td>

                                        <td colspan="2" class="kt-font-bolder">
                                            {__d('admin', 'khach_phai_tra')}
                                        </td>

                                        <td class="text-right">
                                            <span label-total="{if !empty($order.total)}{$order.total}{/if}" class="kt-font-bolder">
                                                {if !empty($order.total)}
                                                    {$order.total|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                            </span>
                                        </td>

                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {if !empty($plugins[{PROMOTION}])}
                    <div class="kt-portlet nh-portlet">
                        <div class="kt-portlet__head p-10">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-gift mr-5"></i>
                                    {__d('admin', 'khuyen_mai')}
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-actions">
                                    <span id="select-promotion-btn" class="btn btn-sm btn-success">
                                        <i class="fa fa-gift"></i>
                                        {__d('admin', 'ap_dung_khuyen_mai')}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body p-0">
                            <div nh-wrap-promotion-info class="p-15 d-none">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'dang_ap_dung')}: 
                                    </label>

                                    <b nh-label-promotion-name=""></b>
                                </div>
                            </div>                        
                        </div>
                    </div>
                {/if}

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <i class="fa fa-money-bill-alt mr-5"></i>
                                {__d('admin', 'thanh_toan_don_hang')}
                            </h3>                            
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-actions">
                                {if empty($order.id)}
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                                        <input id="cb-payment-confirm" name="create_payment" type="checkbox"> 
                                        {__d('admin', 'xac_nhan_thanh_toan')}
                                        <span></span>
                                    </label>                                
                                {/if}

                                {if !empty($order) && !empty((float)$order.debt) && (float)$order.debt > 0}
                                    <span class="text-danger fw-400 mr-20">
                                        {__d('admin', 'con_phai_tra')}:
                                        <strong> 
                                            {$order.debt|number_format:0:".":","}
                                        </strong>
                                    </span>
                                {/if}

                                {if !empty($order) && !empty((float)$order.paid) && (float)$order.paid > 0}
                                    <span class="text-success fw-400">
                                        {__d('admin', 'da_thanh_toan')}
                                        <strong> 
                                            {$order.paid|number_format:0:".":","}
                                        </strong>
                                    </span>
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body p-0">
                        {$this->element('../Order/payment_method')}
                    </div>
                </div>

                <div class="kt-portlet nh-portlet mb-100">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <i class="fa fa-truck-moving mr-5"></i>
                                {__d('admin', 'dong_goi_va_giao_hang')}
                            </h3>
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-actions">
                                {if empty($order.id)}
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                                        <input id="cb-confirm-shipping" name="create_shipping" type="checkbox"> 
                                        {__d('admin', 'dong_goi_cho_van_chuyen')}
                                        <span></span>
                                    </label>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__body p-0">
                        {$this->element('../Order/shipping_method')}
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none">
            <input name="contact" value="{if !empty($order.contact)}{htmlentities({$order.contact|@json_encode})}{/if}" type="hidden">
            <input name="customer_id" value="{if !empty($order.contact.customer_id)}{$order.contact.customer_id}{/if}" type="hidden">

            <input name="status" value="{if !empty($order.status)}{$order.status}{/if}" type="hidden">            
            <input name="items" value="{if !empty($order.items)}{htmlentities({$order.items|@json_encode})}{/if}" type="hidden">            

            <input name="promotion_id" value="{if !empty($order.promotion_id)}{$order.promotion_id}{/if}" type="hidden">
            <input name="discount_note" value="{if !empty($order.discount_note)}{$order.discount_note}{/if}" type="hidden">
            <input name="discount_type" value="{if !empty($order.discount_type)}{$order.discount_type}{/if}" type="hidden">
            <input name="discount_value" value="{if !empty($order.discount_value)}{$order.discount_value}{/if}" type="hidden">            
        </div>        
    </form>
</div>

{$this->element('Admin.page/modal_quick_add_product')}
{$this->element('Admin.page/modal_quick_add_customer')}
{$this->element('Admin.page/modal_add_address')}
{$this->element('Admin.page/modal_select_promotion')}
{$this->element('Admin.page/popover_price_bill')}
{$this->element('Admin.page/popover_discount_bill')}
