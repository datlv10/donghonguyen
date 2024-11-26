{if !empty($payments)}
    <div class="clearfix p-15">
        <div class="row">
            <div class="col-xl-12 pl-20">
                <div class="kt-timeline-v1 kt-timeline-v1--justified nh-timeline-v1">
                    <div class="kt-timeline-v1__items pt-5 mb-0">
                        {foreach from = $payments item = payment key = k_payment}
                            {assign var = type_badge value = ''}
                            {if isset($payment.status) &&  $payment.status == 0}
                                {assign var = type_badge value = 'dark'}
                            {/if}

                            {if isset($payment.status) &&  $payment.status == 1}
                                {assign var = type_badge value = 'success'}
                            {/if}

                            {if isset($payment.status) &&  $payment.status == 2}
                                {assign var = type_badge value = 'danger'}
                            {/if}

                            <div class="kt-timeline-v1__marker"></div>
                            <div class="kt-timeline-v1__item">
                                <div class="kt-timeline-v1__item-circle">
                                    <div class="kt-bg-{$type_badge}"></div>
                                </div>

                                <div class="kt-timeline-v1__item-content p-10">
                                    <div class="kt-timeline-v1__item-title p-5">
                                        {if !empty($payment.amount)}
                                            {if isset($payment.status) &&  $payment.status == 0}
                                                {__d('admin', 'thanh_toan_loi')}:
                                            {/if}

                                            {if isset($payment.status) &&  $payment.status == 1}
                                                {__d('admin', 'xac_nhan_thanh_toan')}:
                                            {/if}

                                            {if isset($payment.status) &&  $payment.status == 2}
                                                {if !empty($payment.payment_method) && $payment.payment_method == {COD}}
                                                    {__d('admin', 'cho_thu_ho')} COD:
                                                {else}
                                                    {__d('admin', 'dang_cho_xac_nhan')}:
                                                {/if}
                                            {/if}

                                            <span>                                                
                                                {$payment.amount|number_format:0:".":","}
                                            </span>
                                        {/if}                                        

                                        <i data-toggle="collapse" href="#bill-payment-{$payment.id}" class="fa fa-caret-right ml-20 mt-2 cursor-p h-20 w-10px float-right icon-arrow-payment"></i>

                                        {if !empty($payment.payment_time)}
                                            <span class="fs-13 float-right">
                                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($payment.payment_time)}
                                            </span>
                                        {/if}
                                    </div>
                                    <div id="bill-payment-{$payment.id}" class="clearfix collapse">
                                        <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                        <div class="kt-timeline-v1__item-body mt-10 p-5">
                                            <div class="row">
                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'trang_thai_thanh_toan')}
                                                        </label>
                                                        <div class="col-12">
                                                            {if isset($payment.status) && $payment.status == 0}
                                                                <span class="kt-badge kt-badge--dark kt-badge--inline">
                                                                    {__d('admin', 'da_huy')}
                                                                </span>
                                                            {/if}

                                                            {if isset($payment.status) && $payment.status == 1}
                                                                <span class="kt-badge kt-badge--success kt-badge--inline">
                                                                    {__d('admin', 'thanh_toan_thanh_cong')}
                                                                </span>
                                                            {/if}

                                                            {if isset($payment.status) && $payment.status == 2}
                                                                <span class="kt-badge kt-badge--danger kt-badge--inline">
                                                                    {__d('admin', 'dang_cho_duyet')}
                                                                </span>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'phuong_thuc_thanh_toan')}
                                                        </label>
                                                        <div class="col-12">
                                                            <span class="kt-font-bolder">
                                                                {if !empty($payment.payment_method) && $payment.payment_method == {CASH}}
                                                                    {__d('admin', 'thanh_toan_tien_mat')}
                                                                {/if}

                                                                {if !empty($payment.payment_method) && $payment.payment_method == {BANK}}
                                                                    {__d('admin', 'thanh_toan_chuyen_khoan')}
                                                                {/if}

                                                                {if !empty($payment.payment_method) && $payment.payment_method == {CREDIT}}
                                                                    {__d('admin', 'thanh_toan_quet_the')}
                                                                {/if}

                                                                {if !empty($payment.payment_method) && $payment.payment_method == {GATEWAY}}
                                                                    {__d('admin', 'thanh_toan_qua_cong_thanh_toan')}
                                                                {/if}

                                                                {if !empty($payment.payment_method) && $payment.payment_method == {VOUCHER}}
                                                                    {__d('admin', 'thanh_toan_bang_voucher')}
                                                                {/if}

                                                                {if !empty($payment.payment_method) && $payment.payment_method == {COD}}
                                                                    {__d('admin', 'thanh_toan_thu_ho')}
                                                                {/if}

                                                                {if !empty($payment.payment_gateway_code)}
                                                                    {assign var = list_gateway value = $this->PaymentAdmin->getListGateWay($lang)}
                                                                    {if !empty($list_gateway[$payment.payment_gateway_code])}
                                                                        ({$list_gateway[$payment.payment_gateway_code]})
                                                                    {/if}
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-xl-4">
                                                    <div class="form-group form-group-xs row">
                                                        <label class="col-12">
                                                            {__d('admin', 'ma_phieu_thanh_toan')}
                                                        </label>
                                                        <div class="col-12">
                                                            <span class="kt-font-bolder">
                                                                {if !empty($payment.code)}
                                                                    <a href="{ADMIN_PATH}/payment/detail/{$payment.code}" target="_blank">
                                                                        {$payment.code}
                                                                    </a>
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                                                
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