{assign var = config_vphone  value = []}
{if !empty($config.vphone)}
    {assign var = config_vphone value = $config.vphone}
{/if}

{assign var = config_momo  value = []}
{if !empty($config.momo)}
    {assign var = config_momo value = $config.momo}
{/if}

{if !empty($config.comment)}
    {assign var = config_comment value = $config.comment}
{/if}

{if !empty($config.social_login)}
    {assign var = config_social_login value = $config.social_login}
{/if}

{if !empty($config.social)}
    {assign var = config_social value = $config.social}
{/if}

{if !empty($config.contact)}
    {assign var = config_contact value = $config.contact}
{/if}

{assign var = url_list value = "{ADMIN_PATH}/mobile-app/dashboard"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-default">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_thong_tin_app')}
                </h3>
            </div>
        </div>
        <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-info-app" method="POST" autocomplete="off">
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ten_app')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-th-large"></i>
                                        </span>
                                    </div>

                                    <input id="app_name" name="app_name" value="{if !empty($app_info.app_name)}{$app_info.app_name}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'app_id')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-barcode"></i>
                                        </span>
                                    </div>

                                    <input id="app_id" name="app_id" value="{if !empty($app_info.app_id)}{$app_info.app_id}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="button" class="btn btn-sm btn-brand btn-save">
                                    {__d('admin', 'luu_thong_tin')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
        
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_vfone')}
                </h3>
            </div>
        </div>

        <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-info-vphone" method="POST" autocomplete="off">
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ten_hotline')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="vfone_hotline_name" value="{if !empty($config_vphone.vfone_hotline_name)}{$config_vphone.vfone_hotline_name}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ten_mien')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-globe" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="vfone_domain" value="{if !empty($config_vphone.vfone_domain)}{$config_vphone.vfone_domain}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tai_khoan')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="vfone_username" value="{if !empty($config_vphone.vfone_username)}{$config_vphone.vfone_username}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'mat_khau')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-key" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="vfone_password" value="{if !empty($config_vphone.vfone_password)}{$config_vphone.vfone_password}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'cong')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="vfone_port" value="{if !empty($config_vphone.vfone_port)}{$config_vphone.vfone_port}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group mb-0">
                                <button type="button" class="btn btn-sm btn-brand btn-save">
                                    {__d('admin', 'luu_thong_tin')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cong_thanh_toan_momo_app_to_app')}
                </h3>
            </div>
        </div>

        <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-config-momo" method="POST" autocomplete="off">
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    MoMo Merchant ID
                                </label>

                                <div class="input-group">
                                    <input name="momo_merchant_id" value="{if !empty($config_momo.momo_merchant_id)}{$config_momo.momo_merchant_id}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    MoMo Merchant Name
                                </label>

                                <div class="input-group">
                                    <input name="momo_merchant_name" value="{if !empty($config_momo.momo_merchant_name)}{$config_momo.momo_merchant_name}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    MoMo Partner Code
                                </label>

                                <div class="input-group">
                                    <input name="momo_partner_code" value="{if !empty($config_momo.momo_partner_code)}{$config_momo.momo_partner_code}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    MoMo SecretKey
                                </label>

                                <div class="input-group">
                                    <input name="momo_secret_key" value="{if !empty($config_momo.momo_secret_key)}{$config_momo.momo_secret_key}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            MoMo PublicKey
                        </label>

                        <div class="input-group">
                            <input name="momo_public_key" value="{if !empty($config_momo.momo_public_key)}{$config_momo.momo_public_key}{/if}" class="form-control" type="text">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group mb-0">
                                <button type="button" class="btn btn-sm btn-brand btn-save">
                                    {__d('admin', 'luu_thong_tin')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'mang_xa_hoi')}
                </h3>
            </div>
        </div>

        <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-info-social" method="POST" autocomplete="off">
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'facebook')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fab fa-facebook-square" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="facebook" value="{if !empty($config_social.facebook)}{$config_social.facebook}{/if}" class="form-control" type="text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'instagram')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fab fa-instagram" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="instagram" value="{if !empty($config_social.instagram)}{$config_social.instagram}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'youtube')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fab fa-youtube" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <input name="youtube" value="{if !empty($config_social.youtube)}{$config_social.youtube}{/if}" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="form-group mb-0">
                                <button type="button" class="btn btn-sm btn-brand btn-save">
                                    {__d('admin', 'luu_thong_tin')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'cau_hinh_binh_luan')}
                        </h3>
                    </div>
                </div>
                <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-info-comment" method="POST" autocomplete="off">
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'cho_quan_tri_phe_duyet')}
                                        </label>
                                        
                                        <div class="kt-radio-inline mt-5">
                                            <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                                <input type="radio" name="awaiting_approval" value="0" {if empty($config_comment.awaiting_approval)  || !isset($config_comment.awaiting_approval)}checked{/if}> 
                                                    {__d('admin', 'khong')}
                                                <span></span>
                                            </label>

                                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                                <input type="radio" name="awaiting_approval" value="1" {if !empty($config_comment.awaiting_approval)}checked{/if}> 
                                                    {__d('admin', 'co')}
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'gioi_han_dang_tai_anh')}
                                        </label>
                                        
                                        <div class="input-group">
                                            <input id="max_upload" name="max_upload" value="{if !empty($config_comment.max_upload)}{$config_comment.max_upload}{/if}" class="form-control" type="text">
                                        </div>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'neu_khong_nhap_mac_dinh_gioi_han_dang_tai_anh_la_5')}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-0">
                                        <button type="button" class="btn btn-sm btn-brand btn-save">
                                            {__d('admin', 'luu_thong_tin')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'cau_hinh_lien_he')}
                        </h3>
                    </div>
                </div>

                <form id="main-form" action="{ADMIN_PATH}/mobile-app/setting/save-contact" method="POST" autocomplete="off">
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'so_dien_thoai')}
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                                </span>
                                            </div>

                                            <input name="phone" value="{if !empty($config_contact.phone)}{$config_contact.phone}{/if}" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            Zalo
                                        </label>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <svg fill="#000000" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="16px" height="16px"><path d="M 9 4 C 6.2504839 4 4 6.2504839 4 9 L 4 41 C 4 43.749516 6.2504839 46 9 46 L 41 46 C 43.749516 46 46 43.749516 46 41 L 46 9 C 46 6.2504839 43.749516 4 41 4 L 9 4 z M 9 6 L 15.580078 6 C 12.00899 9.7156859 10 14.518083 10 19.5 C 10 24.66 12.110156 29.599844 15.910156 33.339844 C 16.030156 33.549844 16.129922 34.579531 15.669922 35.769531 C 15.379922 36.519531 14.799687 37.499141 13.679688 37.869141 C 13.249688 38.009141 12.97 38.430859 13 38.880859 C 13.03 39.330859 13.360781 39.710781 13.800781 39.800781 C 16.670781 40.370781 18.529297 39.510078 20.029297 38.830078 C 21.379297 38.210078 22.270625 37.789609 23.640625 38.349609 C 26.440625 39.439609 29.42 40 32.5 40 C 36.593685 40 40.531459 39.000731 44 37.113281 L 44 41 C 44 42.668484 42.668484 44 41 44 L 9 44 C 7.3315161 44 6 42.668484 6 41 L 6 9 C 6 7.3315161 7.3315161 6 9 6 z M 33 15 C 33.55 15 34 15.45 34 16 L 34 25 C 34 25.55 33.55 26 33 26 C 32.45 26 32 25.55 32 25 L 32 16 C 32 15.45 32.45 15 33 15 z M 18 16 L 23 16 C 23.36 16 23.700859 16.199531 23.880859 16.519531 C 24.050859 16.829531 24.039609 17.219297 23.849609 17.529297 L 19.800781 24 L 23 24 C 23.55 24 24 24.45 24 25 C 24 25.55 23.55 26 23 26 L 18 26 C 17.64 26 17.299141 25.800469 17.119141 25.480469 C 16.949141 25.170469 16.960391 24.780703 17.150391 24.470703 L 21.199219 18 L 18 18 C 17.45 18 17 17.55 17 17 C 17 16.45 17.45 16 18 16 z M 27.5 19 C 28.11 19 28.679453 19.169219 29.189453 19.449219 C 29.369453 19.189219 29.65 19 30 19 C 30.55 19 31 19.45 31 20 L 31 25 C 31 25.55 30.55 26 30 26 C 29.65 26 29.369453 25.810781 29.189453 25.550781 C 28.679453 25.830781 28.11 26 27.5 26 C 25.57 26 24 24.43 24 22.5 C 24 20.57 25.57 19 27.5 19 z M 38.5 19 C 40.43 19 42 20.57 42 22.5 C 42 24.43 40.43 26 38.5 26 C 36.57 26 35 24.43 35 22.5 C 35 20.57 36.57 19 38.5 19 z M 27.5 21 C 27.39625 21 27.29502 21.011309 27.197266 21.03125 C 27.001758 21.071133 26.819727 21.148164 26.660156 21.255859 C 26.500586 21.363555 26.363555 21.500586 26.255859 21.660156 C 26.148164 21.819727 26.071133 22.001758 26.03125 22.197266 C 26.011309 22.29502 26 22.39625 26 22.5 C 26 22.60375 26.011309 22.70498 26.03125 22.802734 C 26.051191 22.900488 26.079297 22.994219 26.117188 23.083984 C 26.155078 23.17375 26.202012 23.260059 26.255859 23.339844 C 26.309707 23.419629 26.371641 23.492734 26.439453 23.560547 C 26.507266 23.628359 26.580371 23.690293 26.660156 23.744141 C 26.819727 23.851836 27.001758 23.928867 27.197266 23.96875 C 27.29502 23.988691 27.39625 24 27.5 24 C 27.60375 24 27.70498 23.988691 27.802734 23.96875 C 28.487012 23.82916 29 23.22625 29 22.5 C 29 21.67 28.33 21 27.5 21 z M 38.5 21 C 38.39625 21 38.29502 21.011309 38.197266 21.03125 C 38.099512 21.051191 38.005781 21.079297 37.916016 21.117188 C 37.82625 21.155078 37.739941 21.202012 37.660156 21.255859 C 37.580371 21.309707 37.507266 21.371641 37.439453 21.439453 C 37.303828 21.575078 37.192969 21.736484 37.117188 21.916016 C 37.079297 22.005781 37.051191 22.099512 37.03125 22.197266 C 37.011309 22.29502 37 22.39625 37 22.5 C 37 22.60375 37.011309 22.70498 37.03125 22.802734 C 37.051191 22.900488 37.079297 22.994219 37.117188 23.083984 C 37.155078 23.17375 37.202012 23.260059 37.255859 23.339844 C 37.309707 23.419629 37.371641 23.492734 37.439453 23.560547 C 37.507266 23.628359 37.580371 23.690293 37.660156 23.744141 C 37.739941 23.797988 37.82625 23.844922 37.916016 23.882812 C 38.005781 23.920703 38.099512 23.948809 38.197266 23.96875 C 38.29502 23.988691 38.39625 24 38.5 24 C 38.60375 24 38.70498 23.988691 38.802734 23.96875 C 39.487012 23.82916 40 23.22625 40 22.5 C 40 21.67 39.33 21 38.5 21 z"/></svg>
                                                </span>
                                            </div>

                                            <input name="zalo" value="{if !empty($config_contact.zalo)}{$config_contact.zalo}{/if}" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group mb-0">
                                        <button type="button" class="btn btn-sm btn-brand btn-save">
                                            {__d('admin', 'luu_thong_tin')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>