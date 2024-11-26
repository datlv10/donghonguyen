<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cai_dat_thong_tin_app')}
                </h3>
            </div>
        </div>

        <div class="kt-portlet__body pb-0">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-cogs" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/setting" class="kt-widget5__title">
                                        {__d('admin', 'cai_dat_chung')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cai_dat_thong_tin_mobile_app')}
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

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'thiet_lap_giao_dien_app')}
                </h3>
            </div>
        </div>

        <div class="kt-portlet__body pb-0">
            <div class="row">

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-mobile-alt" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/template" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_giao_dien')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_va_thiet_lap_giao_dien_mac_dinh')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                            
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-palette" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/template/setting-general" class="kt-widget5__title">
                                        {__d('admin', 'cai_dat_chung')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cai_dat_cac_cau_hinh_chung_cua_giao_dien')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                            
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-magic" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/template/customize" class="kt-widget5__title">
                                        {__d('admin', 'cai_dat_giao_dien')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cai_dat_giao_dien_mobile_app')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-images" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/template/media" class="kt-widget5__title">
                                        {__d('admin', 'thu_vien_anh')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thu_vien_anh_danh_cho_app')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-tags" style="font-size: 2.5rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/template/text" class="kt-widget5__title">
                                        {__d('admin', 'nhan_giao_dien')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_tat_ca_cac_nhan_cua_giao_dien')}
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

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'thiet_lap_block')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body pb-0">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-th" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/block" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_block')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_cac_block_cua_app')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-th-large" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/mobile-app/block/add" class="kt-widget5__title">
                                        {__d('admin', 'them_block')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'them_block_moi_cho_app')}
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