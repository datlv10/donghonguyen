<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($migrate.done)}
                <a href="{ADMIN_PATH}/transform-data/export/categories/article" class="btn btn-sm btn-brand">
                    {__d('admin', 'tiep_tuc')}
                </a>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/transform-data/export/process" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'buoc_khoi_tao')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group">
                    <label class="kt-font-bold text-danger">
                        {__d('admin', 'luu_y')}:
                    </label>
                    <span class="form-text text-muted">
                        - {__d('admin', 'thuc_hien_cac_buoc_export_theo_dung_thu_tu')} 
                    </span>

                    <span class="form-text text-muted">
                        - {__d('admin', 'so_ban_ghi_can_export_cang_it_thi_export_du_lieu_cang_nhanh')} 
                    </span>

                    <span class="form-text text-muted">
                        - {__d('admin', 'du_lieu_export_mau_duoc_luu_tam_trong_thu_muc_tmp_trong_qua_trinh_export_du_lieu_khong_duoc_xoa_cache_he_thong_de_tranh_xay_ra_loi_khi_export_du_lieu_mau')} 
                    </span>

                    <span class="form-text text-muted">
                        - {__d('admin', 'sau_khi_thuc_hien_export_du_lieu_va_tai_file_export_du_lieu_thanh_cong_hay_xoa_cache_de_tranh_chiem_dung_luong_cua_website')} 
                    </span>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-lg-8">
                        <div class="kt-widget4 mt-20">
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-folder-plus fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'khoi_tao_moi_quy_trinh_export_du_lieu')}
                                    {if !empty($migrate.initialization.status) && $migrate.initialization.status == "{SUCCESS}"}
                                        <i class="text-success fs-12 ml-10">
                                            {__d('admin', 'da_khoi_tao')}
                                        </i>
                                        <i class="fa fa-check-circle text-success fs-14"></i>
                                    {/if}

                                    {if !empty($migrate.initialization.status) && $migrate.initialization.status == "{ERROR}"}
                                        <i class="text-danger fs-12 ml-10">
                                            {__d('admin', 'xay_ra_loi')}
                                        </i>
                                        <i class="fa fa-window-close text-danger fs-14"></i>
                                    {/if}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <span class="btn btn-sm btn-secondary btn-export-data" type="initialization">
                                        <i class="fa fa-check fs-14"></i>
                                        {__d('admin', 'thuc_hien')}
                                    </span>
                                </span>
                            </div>

                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-search fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'doc_thong_tin_du_lieu')}
                                    {if !empty($migrate.read_database.status) && $migrate.read_database.status == "{SUCCESS}"}
                                        <i class="text-success fs-12 ml-10">
                                            {__d('admin', 'da_thuc_hien')}
                                        </i>
                                        <i class="fa fa-check-circle text-success fs-14"></i>
                                    {/if}

                                    {if !empty($migrate.read_database.status) && $migrate.read_database.status == "{ERROR}"}
                                        <i class="text-danger fs-12 ml-10">
                                            {__d('admin', 'xay_ra_loi')}
                                        </i>
                                        <i class="fa fa-window-close text-danger fs-14"></i>
                                    {/if}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <span class="btn btn-sm btn-secondary btn-export-data" type="read_database">
                                        <i class="fa fa-check fs-14"></i>
                                        {__d('admin', 'thuc_hien')}
                                    </span>
                                </span>
                            </div>

                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-flag-checkered fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'cau_hinh_du_lieu_export')}
                                    {if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}
                                        <i class="text-success fs-12 ml-10">
                                            {__d('admin', 'da_thuc_hien')}
                                        </i>
                                        <i class="fa fa-check-circle text-success fs-14"></i>
                                    {/if}

                                    {if !empty($migrate.config_data.status) && $migrate.config_data.status == "{ERROR}"}
                                        <i class="text-danger fs-12 ml-10">
                                            {__d('admin', 'xay_ra_loi')}
                                        </i>
                                        <i class="fa fa-window-close text-danger fs-14"></i>
                                    {/if}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <span id="btn-show-config-data" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-check fs-14"></i>
                                        {__d('admin', 'thuc_hien')}
                                    </span>
                                </span>
                            </div>

                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-indent fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'cau_hinh_id_bat_dau_cho_du_lieu')}
                                    {if !empty($migrate.config_id.status) && $migrate.config_id.status == "{SUCCESS}"}
                                        <i class="text-success fs-12 ml-10">
                                            {__d('admin', 'da_thuc_hien')}
                                        </i>
                                        <i class="fa fa-check-circle text-success fs-14"></i>
                                    {/if}

                                    {if !empty($migrate.config_id.status) && $migrate.config_id.status == "{ERROR}"}
                                        <i class="text-danger fs-12 ml-10">
                                            {__d('admin', 'xay_ra_loi')}
                                        </i>
                                        <i class="fa fa-window-close text-danger fs-14"></i>
                                    {/if}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <span id="btn-show-config-id" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye fs-14"></i>
                                        {__d('admin', 'cau_hinh')}
                                    </span>
                                </span>
                            </div>

                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-cogs fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'cau_hinh_thong_tin_cdn')}
                                    {if !empty($migrate.config_cdn.status) && $migrate.config_cdn.status == "{SUCCESS}"}
                                        <i class="text-success fs-12 ml-10">
                                            {__d('admin', 'da_thuc_hien')}
                                        </i>
                                        <i class="fa fa-check-circle text-success fs-14"></i>
                                    {/if}

                                    {if !empty($migrate.config_cdn.status) && $migrate.config_cdn.status == "{ERROR}"}
                                        <i class="text-danger fs-12 ml-10">
                                            {__d('admin', 'xay_ra_loi')}
                                        </i>
                                        <i class="fa fa-window-close text-danger fs-14"></i>
                                    {/if}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <span id="btn-show-config-cdn" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye fs-14"></i>
                                        {__d('admin', 'cau_hinh')}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{$this->element("../TransformData/config_data_export", ['migrate' => $migrate])}
{$this->element("../TransformData/config_id_export", ['migrate' => $migrate])}
{$this->element("../TransformData/config_cdn_export", ['migrate' => $migrate])}