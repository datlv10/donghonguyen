{assign var = url_list value = "{ADMIN_PATH}/order"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-default">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>

            {if !empty($order.status)}
                {$this->element('../Order/element_btn_submit', ['order_status' => "{$order.status}"])}
            {/if}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/order/change-status/{if !empty($id)}{$id}{/if}" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                {if !$order.status|in_array:[CANCEL, DRAFT]}
                    <div class="kt-portlet nh-portlet">
                        <div class="kt-portlet__body">
                            {$this->element('../Order/element_status')}
                        </div>
                    </div>
                {/if}

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
                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'trang_thai')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($order.status) && $order.status == {NEW_ORDER}}
                                                <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'don_hang_moi')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {CONFIRM}}
                                                <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'xac_nhan')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {PACKAGE}}
                                                <span class="kt-badge kt-badge--brand kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'dong_goi')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {EXPORT}}
                                                <span class="kt-badge kt-badge--brand kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'xuat_kho')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {DONE}}
                                                <span class="kt-badge kt-badge--success kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'thanh_cong')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {CANCEL}}
                                                <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'huy')}
                                                </span>
                                            {/if}

                                            {if !empty($order.status) && $order.status == {DRAFT}}
                                                <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill">
                                                    {__d('admin', 'khach_hang_chua_xac_nhan')}
                                                </span>
                                            {/if}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'ma_don_hang')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($order.code)}
                                                {$order.code}
                                            {/if}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'ngay_lap_don')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($order.date_create)}
                                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($order.date_create)}
                                            {/if}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'ghi_chu')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($order.note)}
                                                {$order.note}
                                            {/if}
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'nguon_don_hang')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {assign var = list_source value = $this->ObjectAdmin->getListOrderSource()}
                                            {if !empty($order.source) && !empty($list_source[$order.source])}
                                                {$list_source[$order.source]}
                                            {/if}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group form-group-xs row">
                                    <label class="col-lg-4 col-xl-4 col-form-label">
                                        {__d('admin', 'nhan_vien_cham_soc')}
                                    </label>
                                    <div class="col-lg-8 col-xl-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($order.staff.full_name)}
                                                {$order.staff.full_name}
                                            {/if}
                                        </span>
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
                        {if !empty($order.contact)}
                            <div id="customer-info" class="clearfix">
                                <div class="kt-widget kt-widget--user-profile-1 pb-0">
                                    <div class="kt-widget__head">
                                        <div class="kt-widget__content p-0">
                                            <div class="kt-widget__section">
                                                <a href="{if !empty($order.contact.customer_id)}{ADMIN_PATH}/customer/detail/{$order.contact.customer_id}{/if}" target="_blank" class="kt-widget__username fs-16">

                                                    <span class="kt-link">
                                                        {if !empty($order.contact.full_name)}
                                                            {$order.contact.full_name}
                                                        {/if}
                                                    </span>                                            
                                                </a>

                                                <span class="kt-widget__subtitle">
                                                    {if !empty($order.contact.phone)}
                                                        {$order.contact.phone}
                                                    {/if}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                    <div class="kt-widget__body">
                                        <div class="kt-widget__content p-0">
                                            <div class="kt-widget__info">
                                                <span class="kt-widget__data kt-font-transform-u kt-font-bold">
                                                    {__d('admin', 'dia_chi_giao_hang')}
                                                </span>
                                            </div>

                                            <div class="kt-widget__info">
                                                <span class="kt-widget__data">
                                                    {if !empty($order.contact.address_name)}
                                                        {$order.contact.address_name}
                                                    {/if}
                                                </span>
                                            </div>

                                            <div class="kt-widget__info">
                                                <span class="kt-widget__data">
                                                    {if !empty($order.contact.phone)}
                                                        {$order.contact.phone}
                                                    {/if}
                                                </span>
                                            </div>

                                            <div class="kt-widget__info">
                                                <span class="kt-widget__data">
                                                    {if !empty($order.contact.full_address)}
                                                        {$order.contact.full_address}
                                                    {/if}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {else}
                            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                                <div class="kt-widget__body">
                                    <div class="kt-widget__content p-0">
                                        <div class="kt-widget__info">
                                            <a href="{ADMIN_PATH}/order/update/{$order.id}">
                                                {__d('admin', 'cap_nhat_thong_tin_khach_hang')}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>

                {$this->element('Admin.page/list_product_of_order')}

                {if !empty($order.promotion_id)}
                    {assign var = promotion_info value = $this->PromotionAdmin->getDetail($order.promotion_id)}
                    <div class="kt-portlet nh-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-gift mr-5"></i>
                                    {__d('admin', 'khuyen_mai')}
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'dang_ap_dung')}: 
                                </label>

                                <b>
                                    {if !empty($promotion_info.name)}
                                        {$promotion_info.name}
                                    {/if}
                                </b>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="kt-portlet nh-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            {if $debt > 0}
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-money-bill-alt mr-5"></i>
                                    {__d('admin', 'thanh_toan_don_hang')}
                                </h3>
                            {else}
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-check-circle mr-10 text-success"></i>
                                    {__d('admin', 'don_hang_da_duoc_thanh_toan_toan_bo')}
                                </h3>
                            {/if}
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-actions">
                                {if ($debt - $paid_pending) > 0}
                                    <span class="text-danger fw-400 mr-20">
                                        {__d('admin', 'con_phai_tra')}:
                                        <strong> 
                                            {if !empty((float)$order.debt)}
                                                {$order.debt|number_format:0:".":","}
                                            {else}
                                                0
                                            {/if}
                                        </strong>
                                    </span>
                                {else}
                                    <span class="text-success fw-400 mr-20">
                                        {__d('admin', 'da_thanh_toan')}:
                                        <strong>
                                            {if !empty((float)$order.paid)}
                                                {$order.paid|number_format:0:".":","}
                                            {else}
                                                0
                                            {/if}
                                        </strong>
                                    </span>
                                {/if}

                                {if !empty($can_payment)}
                                    <span class="btn btn-sm btn-secondary btn-confirm-payment">
                                        {__d('admin', 'xac_nhan_thanh_toan')}
                                    </span>
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body p-0">
                        {if !empty($order.point_paid) || !empty($order.point_promotion_paid)}
                            <div class="paid-info pt-15 pl-15 pr-15">
                                <p class="kt-font-bolder text-success mb-5">
                                    {__d('admin', 'da_thanh_toan')} :
                                    <span class="kt-font-bolder">
                                        {$order.paid|number_format:0:".":","}
                                    </span>
                                </p>

                                <div class="row">
                                    {if !empty($order.point)}
                                        <div class="col-md-12 col-sm-4 col-12">
                                            <p class="mb-2">
                                                <span class="kt-font-bold">
                                                    {__d('admin', 'su_dung_diem_vi')}: 
                                                </span>
                                                <span class="text-success kt-font-bolder">
                                                    {$order.point|number_format:0:".":","} {__d('admin', 'diem')}
                                                    (
                                                        {if !empty($order.point_paid)}
                                                            = {$order.point_paid|number_format:0:".":","} VND
                                                        {/if}
                                                    )
                                                </span>
                                            </p>
                                        </div>
                                    {/if}

                                    {if !empty($order.point_promotion)}
                                        <div class="col-md-12 col-sm-4 col-12">
                                            <p class="mb-2">
                                                <span class="kt-font-bold">
                                                    {__d('admin', 'su_dung_diem_thuong')}: 
                                                </span>
                                                <span class="text-success kt-font-bolder">
                                                    {$order.point_promotion|number_format:0:".":","} {__d('admin', 'diem')}
                                                    (
                                                        {if !empty($order.point_promotion_paid)}
                                                            = {$order.point_promotion_paid|number_format:0:".":","} VND
                                                        {/if}
                                                    )
                                                </span>
                                            </p>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {/if}

                        {$this->element('../Order/element_list_payment', ['payments' => $payments])}
                    </div>
                </div>

                <div class="kt-portlet nh-portlet {if empty($order_returned)}mb-100{/if}">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            {if !empty($shipped)}
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-check-circle mr-5 text-success"></i>
                                    {__d('admin', 'tat_ca_san_pham_da_duoc_giao')}
                                </h3>
                            {else}
                                <h3 class="kt-portlet__head-title">
                                    <i class="fa fa-truck-moving mr-5"></i>
                                    {__d('admin', 'dong_goi_va_giao_hang')}
                                </h3>
                            {/if}
                            
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-actions">
                                {if !empty($can_shipping)}
                                    <span class="btn btn-sm btn-brand btn-confirm-shipping">
                                        {__d('admin', 'giao_hang')}
                                    </span>
                                {/if}
                            </div>
                        </div>

                    </div>

                    <div class="kt-portlet__body p-0">
                        {$this->element('../Order/element_list_shipping', ['shippings' => $shippings])}
                    </div>
                </div>

                {if !empty($order_returned)}
                    <div class="kt-portlet nh-portlet mb-100 d-none">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                {if !empty($returned_all)}
                                    <h3 class="kt-portlet__head-title">
                                        <i class="fa fa-check-circle mr-10 text-success"></i>
                                        {__d('admin', 'da_tra_hang_hang_toan_bo')}
                                    </h3>
                                {else}
                                    <h3 class="kt-portlet__head-title">
                                        <i class="fa fa-undo"></i>
                                        {__d('admin', 'hoan_tra_hang_da_giao')}
                                    </h3>
                                {/if}                                
                            </div>

                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-actions">
                                    {if empty($returned_all)}
                                        <a href="{ADMIN_PATH}/order/return/create/{if !empty($order.id)}{$order.id}{/if}" class="btn btn-sm btn-secondary bg-white float-right">
                                            {__d('admin', 'hoan_tra')}
                                        </a>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <div class="kt-portlet__body p-0">
                            {$this->element('../Order/element_list_returned', ['order_returned' => $order_returned])}
                        </div>
                    </div>
                {/if}
            </div>
        </div>

        <div class="d-none">
            <input name="contact" value="{if !empty($order.contact)}{htmlentities({$order.contact|@json_encode})}{/if}" type="hidden">        
            <input name="customer_id" value="{if !empty($order.contact.customer_id)}{$order.contact.customer_id}{/if}" type="hidden">
            
            <input name="status" value="{if !empty($order.status)}{$order.status}{/if}" type="hidden">
            <input name="total" value="{if !empty($order.total)}{$order.total}{/if}" type="hidden">
            <input name="shipping_method" value="" type="hidden">

            <input id="paid" value="{if !empty($order.paid)}{$order.paid}{/if}" type="hidden">
            <input id="debt" value="{if !empty($order.debt)}{$order.debt}{/if}" type="hidden">
            <input id="products-item" value="{if !empty($order.items)}{htmlentities({$order.items|@json_encode})}{/if}" type="hidden">
        </div>
    </form>
</div>

<div id="shipping-confirm-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'dong_goi_va_giao_hang')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <form id="shipping-confirm-form" action="{ADMIN_PATH}/order/shipping-confirm{if !empty($order.id)}/{$order.id}{/if}" method="POST" autocomplete="off">
                    {$this->element('../Order/shipping_method')}
                    <input name="customer" value="" type="hidden">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                <button id="shipping-confirm-btn" type="button" class="btn btn-sm btn-brand">
                    {__d('admin', 'dong_goi')}
                </button>
            </div>
        </div>
    </div>
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
                            {__d('admin', 'ban_co_chac_chan_chan_xuat_kho_don_giao_hang_nay_khong')}
                        </span>
                    </div>
                </div>            
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>

                <button id="btn-change-status" data-status="" data-shipping-id="" data-order-id="{if !empty($order.id)}{$order.id}{/if}" type="button" class="btn btn-sm btn-brand">
                    {__d('admin', 'xuat_kho')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="payment-confirm-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'xac_nhan_thanh_toan')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body p-0">
                <form id="payment-confirm-form" action="{ADMIN_PATH}/order/payment-confirm{if !empty($order.id)}/{$order.id}{/if}" method="POST" autocomplete="off">
                    {$this->element('../Order/payment_method')}
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-payment-confirm" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'thanh_toan')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="cancel-order-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'xac_nhan_huy_don_hang')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="kt-section mb-0">
                    <div class="kt-section__content">
                        <p class="mb-10">
                            <span>
                                {__d('admin', 'ban_chac_chan_muon_huy_don_hang')} <b>{if !empty($order.code)}{$order.code}{/if}</b> ?
                                {__d('admin', 'thao_tac_nay_khong_the_khoi_phuc_va_se_tac_dong_den')}:                                
                            </span>
                        </p>

                        <p class="mb-10">
                            <span>
                                - {__d('admin', 'thay_doi_so_luong_san_pham')}
                            </span>
                        </p>

                        <p class="mb-10">
                            <span>
                                - {__d('admin', 'thay_doi_trang_thai_va_thong_tin_cua_don_hang')}
                            </span>
                        </p>

                        <p class="mb-0">
                            <span>
                                - {__d('admin', 'huy_phieu_giao_van_va_thu_chi_don_hang')}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-confirm-cancel-order" data-order-id="{if !empty($order.id)}{$order.id}{/if}" type="button" class="btn btn-sm btn-danger">
                    {__d('admin', 'dong_y')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="order-contact-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cap_nhat_dia_chi')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="update-contact-form" action="{ADMIN_PATH}/order/update-contact{if !empty($order.id)}/{$order.id}{/if}" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ten_nguoi_nhan')}
                                    <span class="kt-font-danger">*</span>
                                </label>
                                <input name="full_name" value="{if !empty($order.contact.full_name)}{$order.contact.full_name}{/if}" class="form-control form-control-sm required" type="text"  autocomplete="off" placeholder="{__d('admin', 'nha_rieng_co_quan')} ...">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'so_dien_thoai')}
                                    <span class="kt-font-danger">*</span>
                                </label>
                                <input name="phone" value="{if !empty($order.contact.phone)}{$order.contact.phone}{/if}" class="form-control form-control-sm" type="text"  autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tinh_thanh')}
                                </label>
                                {assign var = city_id value = ''}
                                {if !empty($order.contact.city_id)}
                                    {assign var = city_id value = $order.contact.city_id}
                                {/if}
                                {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => $city_id, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'quan_huyen')}
                                </label>
                                {assign var = district_id value = ''}
                                {if !empty($order.contact.district_id)}
                                    {assign var = district_id value = $order.contact.district_id}
                                {/if}
                                {$this->Form->select('district_id', $this->LocationAdmin->getListDistrictForDropdown($city_id), ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => $district_id, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'phuong_xa')}
                                </label>
                                {assign var = ward_id value = ''}
                                {if !empty($order.contact.ward_id)}
                                    {assign var = ward_id value = $order.contact.ward_id}
                                {/if}
                                {$this->Form->select('ward_id', $this->LocationAdmin->getListWardForDropdown($district_id), ['id' => 'ward_id', 'empty' => "-- {__d('admin', 'phuong_xa')} --", 'default' => $ward_id, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>
                            {__d('admin', 'dia_chi')}
                        </label>
                        <input name="address" value="{if !empty($order.contact.address)}{$order.contact.address}{/if}" class="form-control form-control-sm" type="text" autocomplete="off">
                    </div>

                    <div class="d-none">
                        <input name="email" type="hidden" value="{if !empty($order.contact.email)}{$order.contact.email}{/if}">
                        <input name="contact_id" type="hidden" value="{if !empty($order.contact.id)}{$order.contact.id}{/if}">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="save-contact-order-btn" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>