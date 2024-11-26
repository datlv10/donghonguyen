<div class="kt-grid__item kt-wizard-v2__aside pt-20 pb-20">
    <div class="kt-wizard-v2__nav">
        <div class="kt-wizard-v2__nav-items kt-wizard-v2__nav-items--clickable">
            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                <div class="kt-wizard-v2__nav-body">
                    <div class="kt-wizard-v2__nav-icon">
                    	<img src="/admin/assets/media/store_partner/kiotviet.png" class="h-30px">
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            KiotViet
                        </div>
                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($config_kiotviet.status)}
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
                    
                    <div class="kt-wizard-v2__nav-icon">
                    	<img src="/admin/assets/media/store_partner/nhanh.png" class="h-30px">
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            </i> {__d('admin', 'nhanh')}
                        </div>

                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($item_partner_stores.status)}
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
