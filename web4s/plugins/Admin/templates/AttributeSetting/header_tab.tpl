{assign var = get_path value = "{$this->getRequest()->getPath()}"}
<div class="kt-grid__item kt-wizard-v2__aside pt-20 pb-20">
    <div class="kt-wizard-v2__nav">
        <div class="kt-wizard-v2__nav-items kt-wizard-v2__nav-items--clickable">
            <div class="kt-wizard-v2__nav-item mb-10" data-ktwizard-type="step">
                <div class="kt-wizard-v2__nav-body">
                    
                    <div class="kt-wizard-v2__nav-icon">
                        <i class="flaticon-open-box"></i>
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            {__d('admin', 'san_pham')}
                        </div>

                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($product_setting.status)}
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
                        <i class="flaticon-file-2"></i>
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            </i> {__d('admin', 'bai_viet')}
                        </div>

                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($article_setting.status)}
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
                        <i class="flaticon-statistics"></i>
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            
                            {__d('admin', 'phien_ban_san_pham')}
                        </div>
                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($product_item_setting.status)}
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

            <div class="kt-wizard-v2__nav-item mb-0" data-ktwizard-type="step">
                <div class="kt-wizard-v2__nav-body">
                    
                    <div class="kt-wizard-v2__nav-icon">
                        <i class="flaticon-layers"></i>
                    </div>
                    <div class="kt-wizard-v2__nav-label">
                        <div class="kt-wizard-v2__nav-label-title">
                            {__d('admin', 'thuong_hieu')}
                        </div>

                        <div class="kt-wizard-v2__nav-label-desc">
                            {if !empty($brand_setting.status)}
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
