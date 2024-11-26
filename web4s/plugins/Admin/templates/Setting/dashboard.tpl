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
                    {__d('admin', 'cai_dat_chung')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-info-circle" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/website-info" class="kt-widget5__title">
                                        {__d('admin', 'thong_tin_website')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_thong_tin_website')}
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
                                    <i class="fa fa-language" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/language" class="kt-widget5__title">
                                        {__d('admin', 'ngon_ngu')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_ngon_ngu_website')}
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
                                    <i class="fa fa-envelope-open-text" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/email" class="kt-widget5__title">
                                        {__d('admin', 'cau_hinh_email')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_cau_hinh_email')}
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
                                    <i class="fa fa-city" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/country" class="kt-widget5__title">
                                        {__d('admin', 'tinh_thanh')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'danh_sach_tinh_thanh')}
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
                                    <i class="fab fa-telegram" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/contact/form" class="kt-widget5__title">
                                        {__d('admin', 'form_lien_he')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_thong_tin_form_lien_he')}
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
                                    <i class="fa fa-indent" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/dashboard-attribute" class="kt-widget5__title">
                                        {__d('admin', 'thuoc_tinh_mo_rong')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'tuy_chinh_cac_thuoc_tinh_mo_rong_cua_san_pham_va_bai_viet')}
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
                                    <i class="fab fa-facebook" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/social" class="kt-widget5__title">
                                        {__d('admin', 'mang_xa_hoi')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_cac_thong_tin_lien_quan_den_cac_trang_mang_xa_hoi')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-xl-3 col-sm-4 col-6 d-none">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-link" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/link" class="kt-widget5__title">
                                        {__d('admin', 'duong_dan_tinh')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_duong_dan_tinh')}
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
                                    <i class="fa fa-code" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/embed-code" class="kt-widget5__title">
                                        {__d('admin', 'ma_nhung')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'quan_ly_ma_nhung')}
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
                                    <i class="fa fa-qrcode" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/qr-code" class="kt-widget5__title">
                                        {__d('admin', 'ma_qr')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'cau_hinh_ma_qr')}
                                    </p>
                                </div>
                            </div>
                            <div class="kt-widget5__content"></div>
                        </div>
                    </div>
                </div>
                
                {if !empty($plugins.notification)}
                    <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                        <div class="kt-widget5">
                            <div class="kt-widget5__item">
                                <div class="kt-widget5__content">
                                    <div class="kt-widget5__pic">
                                        <i class="fa fa-bell" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/notification" class="kt-widget5__title">
                                            {__d('admin', 'gui_thong_bao')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_cau_hinh_gui_thong_bao')}
                                        </p>
                                    </div>
                                </div>
                                <div class="kt-widget5__content"></div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'he_thong')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fab fa-dev" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/change-mode" class="kt-widget5__title">
                                        {__d('admin', 'doi_che_do')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thay_doi_che_do_website')}
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
                                    <i class="fa fa-trash-restore-alt" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="javascript:;" class="kt-widget5__title nh-clear-cache">
                                        {__d('admin', 'xoa_cache')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'xoa_bo_nho_luu_tru_tam_thoi_cua_website')}
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
                                    <i class="fa fa-random" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/redirect/setting" class="kt-widget5__title">
                                        {__d('admin', 'chuyen_huong')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'chuyen_huong_duong_dan_website')}
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
                                    <i class="fa fa-history" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/log" class="kt-widget5__title">
                                        Log
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'lich_su_cap_nhat_cua_tai_khoan')}
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

    {if !empty($plugins[{PRODUCT}])}
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ban_hang')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                        <div class="kt-widget5">
                            <div class="kt-widget5__item">
                                <div class="kt-widget5__content">
                                    <div class="kt-widget5__pic">
                                        <i class="fa fa-dice-d6" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/product" class="kt-widget5__title">
                                            {__d('admin', 'san_pham')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_cau_hinh_san_pham')}
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
                                        <i class="fa fa-shopping-basket" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/order" class="kt-widget5__title">
                                            {__d('admin', 'don_hang')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_cau_hinh_don_hang')}
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
                                        <i class="fa fa-print" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/print-form" class="kt-widget5__title">
                                            {__d('admin', 'mau_in')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_cau_hinh_mau_in')}
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
                                        <i class="fa fa-luggage-cart" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/source" class="kt-widget5__title">
                                            {__d('admin', 'nguon_don_hang')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'them_va_quan_ly_nguon_tao_ra_don_hang')}
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
                                        <i class="fa fa-truck-moving" style="font-size: 3rem;"></i>                                        
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/shipping-method" class="kt-widget5__title">
                                            {__d('admin', 'van_chuyen')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'cau_hinh_gia_va_phuong_thuc_van_chuyen')}
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
                                        <i class="fa fa-shipping-fast" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/carriers" class="kt-widget5__title">
                                            {__d('admin', 'hang_van_chuyen')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_hang_van_chuyen')}
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
                                        <i class="fab fa-react" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/setting-store-partner" class="kt-widget5__title">
                                            {__d('admin', 'doi_tac_quan_ly_kho_hang')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'cau_hinh_thong_tin_phan_mem_quan_ly_kho_hang_cua_doi_tac')}
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
                        {__d('admin', 'giao_dich')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                        <div class="kt-widget5">
                            <div class="kt-widget5__item">
                                <div class="kt-widget5__content">
                                    <div class="kt-widget5__pic">
                                        <i class="fa fa-money-bill" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/currency" class="kt-widget5__title">
                                            {__d('admin', 'tien_te')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_tien_te_cua_he_thong')}
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
                                        <i class="fa fa-landmark" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/payment-gateway" class="kt-widget5__title">
                                            {__d('admin', 'cong_thanh_toan')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'thiet_lap_cong_thanh_toan')}
                                        </p>
                                    </div>
                                </div>
                                <div class="kt-widget5__content"></div>
                            </div>
                        </div>
                    </div>

                    {if !empty($plugins[{POINT}])}
                        <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                            <div class="kt-widget5">
                                <div class="kt-widget5__item">
                                    <div class="kt-widget5__content">
                                        <div class="kt-widget5__pic">
                                            <i class="fa fa-wallet" style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="kt-widget5__section">
                                            <a href="{ADMIN_PATH}/setting/point" class="kt-widget5__title">
                                                {__d('admin', 'diem_khach_hang')}
                                            </a>
                                            <p class="kt-widget5__desc">
                                                {__d('admin', 'thiet_lap_cau_hinh_diem_khach_hang')}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="kt-widget5__content"></div>
                                </div>
                            </div>
                        </div>
                    {/if}

                    {if !empty($plugins[{AFFILIATE}])}
                        <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                            <div class="kt-widget5">
                                <div class="kt-widget5__item">
                                    <div class="kt-widget5__content">
                                        <div class="kt-widget5__pic">
                                            <i class="fas fa-project-diagram" style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="kt-widget5__section">
                                            <a href="{ADMIN_PATH}/setting/affiliate" class="kt-widget5__title">
                                                {__d('admin', 'affiliate')}
                                            </a>
                                            <p class="kt-widget5__desc">
                                                {__d('admin', 'thiet_lap_cau_hinh_affiliate')}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="kt-widget5__content"></div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/if}
    
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'tai_khoan')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-users" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/user" class="kt-widget5__title">
                                        {__d('admin', 'danh_sach_tai_khoan')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_tai_khoan')}
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
                                    <i class="fa fa-users-cog" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/role" class="kt-widget5__title">
                                        {__d('admin', 'nhom_quyen')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_nhom_quyen')}
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
                                    <i class="fa fa-user-edit" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/role/permission" class="kt-widget5__title">
                                        {__d('admin', 'phan_quyen')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_phan_quyen')}
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
                                    <a href="{ADMIN_PATH}/setting/approved" class="kt-widget5__title">
                                        {__d('admin', 'duyet_bai_viet')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_quyen_duyet_bai_viet')}
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
                    {__d('admin', 'ket_noi')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fab fa-battle-net" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/api" class="kt-widget5__title">
                                        API
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_thong_tin_api')}
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
                                    <i class="fa fa-comment-dots" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/send-messages" class="kt-widget5__title">
                                        {__d('admin', 'gui_tin_nhan')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_gui_tin_nhan')}
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
                                    <i class="fa fa-sms" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/sms-brandname" class="kt-widget5__title">
                                        {__d('admin', 'sms_brandname')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_cau_hinh_sms_brandname')}
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
                    {__d('admin', 'bao_mat')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                    <div class="kt-widget5">
                        <div class="kt-widget5__item">
                            <div class="kt-widget5__content">
                                <div class="kt-widget5__pic">
                                    <i class="fa fa-user-shield" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/customer" class="kt-widget5__title">
                                        {__d('admin', 'khach_hang')}
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'thiet_lap_cau_hinh_khach_hang')}
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
                                    <i class="fab fa-expeditedssl" style="font-size: 3rem;"></i>
                                </div>
                                <div class="kt-widget5__section">
                                    <a href="{ADMIN_PATH}/setting/recaptcha" class="kt-widget5__title">
                                        reCAPTCHA v3
                                    </a>
                                    <p class="kt-widget5__desc">
                                        {__d('admin', 'chong_spam_va_bao_ve_thong_tin_du_lieu')}
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

    {if $supper_admin}
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'chuc_nang_he_thong')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-sm-4 col-12">
                        <div class="kt-widget5">
                            <div class="kt-widget5__item">
                                <div class="kt-widget5__content">
                                    <div class="kt-widget5__pic">
                                        <i class="fa fa-desktop" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/template/list" class="kt-widget5__title">
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
                                        <i class="fa fa-cog" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/plugin" class="kt-widget5__title">
                                            {__d('admin', 'plugins')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'quan_ly_va_thiet_lap_cac_tien_ich_cho_website')}
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
                                        <i class="fa fa-trash-restore" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/clear-data" class="kt-widget5__title">
                                            {__d('admin', 'clear_data')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'xoa_bo_du_lieu_mau_tren_website')}
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
                                        <i class="fa fa-link" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/cdn-path" class="kt-widget5__title">
                                            {__d('admin', 'duong_dan_cdn')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'cap_nhat_thong_tin_duong_dan_cdn')}
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
                                        <i class="fa fa-download" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/transform-data/export" class="kt-widget5__title">
                                            {__d('admin', 'export_du_lieu')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'export_du_lieu_mau_cho_website')}
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
                                        <i class="fa fa-server" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="/api/website/migrate" class="kt-widget5__title" target="_blank">
                                            {__d('admin', 'migrate')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'migrate_thong_tin_du_lieu_cho_website')}
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
                                        <i class="fa fa-keyboard" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="kt-widget5__section">
                                        <a href="{ADMIN_PATH}/setting/replace-search-unicode" class="kt-widget5__title">
                                            {__d('admin', 'search_unicode')}
                                        </a>
                                        <p class="kt-widget5__desc">
                                            {__d('admin', 'cap_nhat_lai_thong_tin_search_unicode')}
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
    {/if}
</div>