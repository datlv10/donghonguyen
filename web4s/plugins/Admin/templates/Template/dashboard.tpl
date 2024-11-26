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
                    {__d('admin', 'thiet_lap_giao_dien')}
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
                                    <i class="fa fa-magic" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/template/customize" class="kt-widget5__title">
                                        {__d('admin', 'cai_dat_giao_dien')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cai_dat_va_thiet_lap_giao_dien')}
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
                                    <i class="fa fa-edit" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/template/modify/view" class="kt-widget5__title">
                                        {__d('admin', 'chinh_sua_giao_dien')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'chinh_sua_file_giao_dien_goc')}
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
                                    <i class="fab fa-css3-alt" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/template/modify/css-custom" class="kt-widget5__title">
                                        {__d('admin', 'tuy_chinh_css')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'tuy_chinh_css_cho_website')}
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
                                    <i class="fab fa-js-square" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/template/modify/js-custom" class="kt-widget5__title">
                                        {__d('admin', 'tuy_chinh_javascript')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'tuy_chinh_javascript_cho_website')}
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
                                    <a href="{ADMIN_PATH}/template/block/list" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_block')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_cac_block_cua_website')}
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
                                    <a href="{ADMIN_PATH}/template/block/add" class="kt-widget5__title">
                                        {__d('admin', 'them_block')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'them_block_moi_cho_website')}
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