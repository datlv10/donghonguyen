{assign var = list_language value = $this->LanguageAdmin->getList()}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <div class="btn-group">
                <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'luu_cau_hinh')} (F1)
                </span>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-grid  kt-wizard-v2 kt-wizard-v2--white" id="kt_wizard" data-ktwizard-state="step-first">
                <div class="kt-grid__item kt-wizard-v2__aside pt-20">
                    <div class="kt-wizard-v2__nav">
                        <div class="kt-wizard-v2__nav-items kt-wizard-v2__nav-items--clickable">
                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{COD}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_truc_tiep')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{COD}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{BANK}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_chuyen_khoan')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{BANK}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{VNPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_vnpay')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{VNPAY}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{MOMO}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_momo')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{MOMO}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{ZALOPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_zalopay')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{ZALOPAY}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{ONEPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_onepay')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{ONEPAY}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{ONEPAY_INSTALLMENT}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_onepay_tra_gop')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{ONEPAY_INSTALLMENT}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{AZPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'nhan_hoa_azpay')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{AZPAY}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{PAYPAL}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_paypal')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{PAYPAL}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{BAOKIM}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_bao_kim')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{BAOKIM}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>                            

                            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{VNPTPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_vnptpay')}
                                        </div>
                                        
                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($payment_gateway[{VNPTPAY}]['status'])}
                                                <i class="kt-font-success fw-400 fs-12">
                                                    {__d('admin', 'dang_hoat_dong')}
                                                </i>
                                            {else}
                                                <i class="kt-font-danger fw-400 fs-12">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </i>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-wizard-v2__nav-item" data-ktwizard-type="step">
                                <div class="kt-wizard-v2__nav-body align-items-center">
                                    <img src="/admin/assets/media/payment/{ALEPAY}.png" class="h-30px mr-10 mw-80px object-fit-contain">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'thanh_toan_alepay')}
                                        </div>
                                        
                                        <div class="kt-wizard-v2__nav-label-desc">
                                            <i class="fw-400 fs-12">
                                                {__d('admin', 'dang_phat_trien')}
                                            </i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                    <div class="kt-form p-20">
                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = cod_item value = []}
                            {if !empty($payment_gateway[{COD}])}
                                {assign var = cod_item value = $payment_gateway[{COD}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{COD}", ['list_language' => $list_language, 'item' => $cod_item, 'list_banks' => $list_banks])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = bank_info value = []}
                            {if !empty($payment_gateway[{BANK}])}
                                {assign var = bank_info value = $payment_gateway[{BANK}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{BANK}", ['list_language' => $list_language, 'item' => $bank_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = vnpay_info value = []}
                            {if !empty($payment_gateway[{VNPAY}])}
                                {assign var = vnpay_info value = $payment_gateway[{VNPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{VNPAY}", ['list_language' => $list_language, 'item' => $vnpay_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = momo_info value = []}
                            {if !empty($payment_gateway[{MOMO}])}
                                {assign var = momo_info value = $payment_gateway[{MOMO}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{MOMO}", ['list_language' => $list_language, 'item' => $momo_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = zalopay_info value = []}
                            {if !empty($payment_gateway[{ZALOPAY}])}
                                {assign var = zalopay_info value = $payment_gateway[{ZALOPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{ZALOPAY}", ['list_language' => $list_language, 'item' => $zalopay_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = onepay_info value = []}
                            {if !empty($payment_gateway[{ONEPAY}])}
                                {assign var = onepay_info value = $payment_gateway[{ONEPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{ONEPAY}", ['list_language' => $list_language, 'item' => $onepay_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = onepay_installment_info value = []}
                            {if !empty($payment_gateway[{ONEPAY_INSTALLMENT}])}
                                {assign var = onepay_installment_info value = $payment_gateway[{ONEPAY_INSTALLMENT}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{ONEPAY_INSTALLMENT}", ['list_language' => $list_language, 'item' => $onepay_installment_info])}
                        </div>
                        
                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = azpay_info value = []}
                            {if !empty($payment_gateway[{AZPAY}])}
                                {assign var = azpay_info value = $payment_gateway[{AZPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{AZPAY}", ['list_language' => $list_language, 'item' => $azpay_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = paypal_info value = []}
                            {if !empty($payment_gateway[{PAYPAL}])}
                                {assign var = paypal_info value = $payment_gateway[{PAYPAL}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{PAYPAL}", ['list_language' => $list_language, 'item' => $paypal_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = baokim_info value = []}
                            {if !empty($payment_gateway[{BAOKIM}])}
                                {assign var = baokim_info value = $payment_gateway[{BAOKIM}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{BAOKIM}", ['list_language' => $list_language, 'item' => $baokim_info])}
                        </div>                        

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = vnptpay_info value = []}
                            {if !empty($payment_gateway[{VNPTPAY}])}
                                {assign var = vnptpay_info value = $payment_gateway[{VNPTPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{VNPTPAY}", ['list_language' => $list_language, 'item' => $vnptpay_info])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = alepay_info value = []}
                            {if !empty($payment_gateway[{ALEPAY}])}
                                {assign var = alepay_info value = $payment_gateway[{ALEPAY}]}
                            {/if}
                            {$this->element("../PaymentGateway/element_{ALEPAY}", ['list_language' => $list_language, 'item' => $alepay_info])}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>