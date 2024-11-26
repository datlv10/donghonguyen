{assign var = url_list value = "{ADMIN_PATH}/payment"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-default btn-sm">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
            
            {if !empty($payment.status) && $payment.status == 2}
                <button type="button" class="btn btn-sm btn-brand btn-confirm-payment">
                    {__d('admin', 'xac_nhan_thanh_cong')}
                </button>

                <button type="button" class="btn btn-sm btn-dark btn-cancel-payment">
                    {__d('admin', 'huy_giao_dich')}
                </button>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-form">
            <div class="kt-portlet__body">
                {if !empty($payment)}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'ma_giao_dich')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                	<span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.code)}
                                            {$payment.code}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'trang_thai')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                            		{if isset($payment.status) && $payment.status == 0}
                                        <span class="kt-badge kt-badge--dark kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'da_huy')}
                                        </span>
                                    {/if}
                                    {if isset($payment.status) && $payment.status == 1}
                                        <span class="kt-badge kt-badge--success kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'thanh_cong')}
                                        </span>
                                    {/if}
                                    {if isset($payment.status) && $payment.status == 2}
                                        <span class="kt-badge kt-badge--danger kt-font-bold kt-badge--inline kt-badge--pill mt-10">
                                            {__d('admin', 'cho_duyet')}
                                        </span>
                                    {/if}
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'loai_phieu')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                	<span class="form-control-plaintext kt-font-bolder">
                                		{if isset($payment.type) && $payment.type == 0}
                                			{__d('admin', 'phieu_chi')}
                                		{elseif isset($payment.type) && $payment.type == 1}
                                			{__d('admin', 'phieu_thu')}
                                		{else}
                                        ...
                                    	{/if}
                                	</span>
                                </div>
                            </div>                           

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                	{if isset($payment.type) && $payment.type == 0}
                                    	{__d('admin', 'ten_nguoi_nhan')}
                                    {/if}
                                    {if isset($payment.type) && $payment.type == 1}
                                    	{__d('admin', 'ten_nguoi_nop')}
                                    {/if}
                                </label>

                                <div class="col-xl-8 col-lg-8">
                                	<span class="form-control-plaintext kt-font-bolder">                                       
                                        {if !empty($payment.full_name)}
                                            {if isset($payment.object_type) && $payment.object_type == CUSTOMER}
                                                <a href="{ADMIN_PATH}/customer/detail{if !empty($payment.object_id)}/{$payment.object_id}{/if}">{$payment.full_name}</a>
                                            {else}
                                                {$payment.full_name}
                                            {/if}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            {if !empty($payment.order)}
                                <div class="form-group form-group-xs row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">
                                        {__d('admin', 'don_hang')}
                                    </label>
                                    <div class="col-xl-8 col-lg-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {if !empty($payment.order.id)}
                                                <a href="{ADMIN_PATH}/order/detail/{$payment.order.id}" target="_blank" >
                                                    {if !empty($payment.order.code)}
                                                        {$payment.order.code}
                                                    {/if}
                                                </a>
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'so_tien')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.amount)}
                                            {if isset($payment.type) && $payment.type == 0}
                                                <div class="text-danger">
                                                    {$payment.amount|number_format:0:".":","}
                                                </div>
                                            {/if}
                                            {if isset($payment.type) && $payment.type == 1}
                                                <div class="text-success">
                                                    {$payment.amount|number_format:0:".":","}
                                                </div>
                                            {/if}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'phuong_thuc_thanh_toan')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {assign var = payment_method value = $this->PaymentAdmin->getListPaymentsForDropdown()}
                                        {if !empty($payment.payment_method)}
                                            {$payment_method[$payment.payment_method]}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            {if !empty($payment.payment_gateway_code)}                                
                                <div class="form-group form-group-xs row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">
                                        {__d('admin', 'cong_thanh_toan')}
                                    </label>

                                    <div class="col-xl-8 col-lg-8">
                                        <span class="form-control-plaintext kt-font-bolder">
                                            {assign var = list_gateway value = $this->PaymentAdmin->getListGateWay($lang)}
                                            {if !empty($list_gateway[$payment.payment_gateway_code])}
                                                {$list_gateway[$payment.payment_gateway_code]}
                                            {else}
                                                ...
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            {/if}

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'ma_tham_chieu')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.reference)}
                                            {$payment.reference}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row d-none">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'hach_toan_ket_qua_kinh_doanh')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if isset($payment.counted) && $payment.counted == 0}
                                            {__d('admin', 'khong')}
                                        {/if}
                                        {if isset($payment.counted) && $payment.counted == 1}
                                            {__d('admin', 'co')}
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-4 col-lg-4 col-form-label">
                                    {__d('admin', 'ghi_chu')}
                                </label>
                                <div class="col-xl-8 col-lg-8">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.note)}
                                            {$payment.note}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mb-10 mt-10"></div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">
                                    {__d('admin', 'ngay_tao')}
                                </label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.created)}
                                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($payment.created)}
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">
                                    {__d('admin', 'ngay_thanh_toan')}
                                </label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.payment_time)}
                                            {$this->UtilitiesAdmin->convertIntgerToDateTimeString($payment.payment_time)}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group form-group-xs row">
                                <label class="col-xl-2 col-lg-2 col-form-label">
                                    {__d('admin', 'mo_ta')}
                                </label>
                                <div class="col-xl-10 col-lg-10">
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {if !empty($payment.description)}
                                            {$payment.description}
                                        {else}
                                            ...
                                        {/if}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                {else}
                    <span class="kt-datatable--error">
                        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
                    </span>
                {/if}
            </div>
        </div>
    </div>
</div>

<div id="cancel-payment-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'xac_nhan_huy_giao_dich')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <p class="mb-10">
                    <span>
                        {__d('admin', 'ban_chac_chan_muon_huy_giao_dich')} <b>{if !empty($payment.code)}{$payment.code}{/if}</b> ?
                    </span>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-confirm-cancel-payment" data-payment-id="{if !empty($payment.id)}{$payment.id}{/if}" type="button" class="btn btn-sm btn-danger">
                    {__d('admin', 'dong_y')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="payment-confirm-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'xac_nhan_giao_hang_thanh_cong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body p-0">
                <form id="payment-confirm-form" action="{ADMIN_PATH}/payment/change-status{if !empty($payment.id)}/{$payment.id}{/if}" method="POST" autocomplete="off">
                     <p class="mb-0 p-15">
                        <span>
                            {__d('admin', 'ban_chac_chan_muon_xac_nhan_giao_dich')} <b>{if !empty($payment.code)}{$payment.code}{/if}</b> ?
                        </span>
                    </p>
                    <div id="wrap-payment-confirm" class="p-15">
                        <div class="form-group">
                            <label>                                        
                                {__d('admin', 'ma_tham_chieu')}
                            </label>
                            <input name="reference" class="form-control form-control-sm" type="text" value="">
                        </div>
                    </div>
                    <input type="hidden" name="status" value="1">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-payment-confirm" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'xac_nhan_thanh_cong')}
                </button>
            </div>
        </div>
    </div>
</div>
