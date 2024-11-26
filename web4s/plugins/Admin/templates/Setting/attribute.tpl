<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_chung')}
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-3 col-xl-3 col-sm-3 col-6">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-indent" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/attribute" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_thuoc_tinh')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_tat_ca_thuoc_tinh_mo_rong')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-xl-3 col-sm-3 col-6">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-file-alt" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/embed-attribute/article" class="kt-widget5__title nh-clear-cache">
                                        {__d('admin', 'them_ma_nhung_thuoc_tinh_vao_bai_viet')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_thuoc_tinh_mo_rong_duoc_phep_nhung_vao_noi_dung_bai_viet')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-xl-3 col-sm-3 col-6">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-dice-d6" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/embed-attribute/product" class="kt-widget5__title">
                                        {__d('admin', 'them_ma_nhung_thuoc_tinh_vao_san_pham')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_thuoc_tinh_mo_rong_duoc_phep_nhung_vao_noi_dung_san_pham')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-3 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-layer-group" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/attribute/config-by-category" class="kt-widget5__title">
                                        {__d('admin', 'thuoc_tinh_theo_danh_muc')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_danh_sach_thuoc_tinh_theo_danh_muc')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>