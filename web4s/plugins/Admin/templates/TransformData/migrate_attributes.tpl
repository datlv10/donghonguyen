<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    <i class="fa fa-file-alt mr-5"></i>
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{ADMIN_PATH}/transform-data/export/products" class="btn btn-sm btn-secondary">
                        <i class="fa fa-angle-double-left"></i>
                        {__d('admin', 'quay_lai_buoc_truoc')}            
                    </a>

                    <a href="{ADMIN_PATH}/transform-data/export/tags" class="btn btn-sm btn-brand">
                        {__d('admin', 'tiep_tuc')}
                        <i class="fa fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-xl-6 col-lg-8">
                    <div class="kt-widget4">
                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-file-alt fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'tong_so_thuoc_tinh')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate.total_record)}
                                    {$migrate.total_record|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-check-circle text-success fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'da_chuyen_doi')}
                            </span>

                            <span id="label-migrated" class="kt-widget4__number kt-font-success">
                                {if !empty($migrate.migrated)}
                                    {$migrate.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-sync-alt fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'chuyen_doi_du_lieu')}                        
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                <span id="btn-migrate" class="btn btn-sm btn-brand {if !empty($migrate.done)}disabled{/if}" type="attributes">
                                    <i class="fa fa-sync-alt fs-14"></i>
                                    <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                                    {__d('admin', 'thuc_hien')}
                                </span>
                            </span>
                        </div>
                    </div>

                    {if !empty($migrate.done)}
                        <div class="alert alert-success mt-20 p-10" role="alert">
                            <div class="alert-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>

                            <div class="alert-text">
                                Chuyển đổi thành công
                            </div>
                        </div>
                    {/if}
                </div>
            </div>            
        </div>
    </div>
</div>