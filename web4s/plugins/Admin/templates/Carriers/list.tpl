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

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'luu_cau_hinh')} (F1)
            </span>
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
                                <div class="kt-wizard-v2__nav-body">
                                    <img src="/admin/assets/media/carrier/{GIAO_HANG_NHANH}.png" class="h-30px mr-10">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'giao_hang_nhanh')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($carriers[{GIAO_HANG_NHANH}].status)}
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
                                <div class="kt-wizard-v2__nav-body">
                                    <img src="/admin/assets/media/carrier/{GIAO_HANG_TIET_KIEM}.png" class="h-30px mr-10">

                                    <div class="kt-wizard-v2__nav-label">
                                        <div class="kt-wizard-v2__nav-label-title">
                                            {__d('admin', 'giao_hang_tiet_kiem')}
                                        </div>

                                        <div class="kt-wizard-v2__nav-label-desc">
                                            {if !empty($carriers[{GIAO_HANG_TIET_KIEM}].status)}
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

                        </div>
                    </div>
                </div>

                <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                    <div class="kt-form p-20">
                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = ghn_item value = []}
                            {if !empty($carriers[{GIAO_HANG_NHANH}])}
                                {assign var = ghn_item value = $carriers[{GIAO_HANG_NHANH}]}
                            {/if}
                            {$this->element("../Carriers/element_{GIAO_HANG_NHANH}", ['item' => $ghn_item])}
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {assign var = ghtk_item value = []}
                            {if !empty($carriers[{GIAO_HANG_TIET_KIEM}])}
                                {assign var = ghtk_item value = $carriers[{GIAO_HANG_TIET_KIEM}]}
                            {/if}
                            {$this->element("../Carriers/element_{GIAO_HANG_TIET_KIEM}", ['item' => $ghtk_item])}
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>