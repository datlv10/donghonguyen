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
                    {__d('admin', 'cau_hinh_ma_qr')}
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
                                    <i class="fa fa-money-check-alt" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/qr-bank-transaction" class="kt-widget5__title">
                                        {__d('admin', 'ma_qr_giao_dich_don_hang')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_ma_qr_giao_dich_don_hang')}
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
                                    <i class="fa fa-bars" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/qr-normal" class="kt-widget5__title nh-clear-cache">
                                        {__d('admin', 'ma_qr_thuong')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_ma_qr_thuong')}
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
                                    <i class="fa fa-qrcode" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/generate-qr" class="kt-widget5__title">
                                        {__d('admin', 'tao_ma_qr')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'tao_ma_qr_theo_loai')}
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