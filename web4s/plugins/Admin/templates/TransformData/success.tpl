<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title text-success">
                    <i class="fa fa-check-circle"></i>
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{ADMIN_PATH}/transform-data/export/tags" class="btn btn-sm btn-secondary">
                        <i class="fa fa-angle-double-left"></i>
                        {__d('admin', 'quay_lai_buoc_truoc')}
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
                                <i class="fa fa-align-justify fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'danh_muc_bai_viet_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.categories_article.migrated)}
                                    {$migrate_info.categories_article.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-align-justify fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'danh_muc_san_pham_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.categories_product.migrated)}
                                    {$migrate_info.categories_product.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-file-alt fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'bai_viet_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.articles.migrated)}
                                    {$migrate_info.articles.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-box fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'san_pham_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.products.migrated)}
                                    {$migrate_info.products.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-code-branch fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'thuong_hieu_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.brands.migrated)}
                                    {$migrate_info.brands.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-indent fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'thuoc_tinh_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.attributes.migrated)}
                                    {$migrate_info.attributes.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-tags fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'the_tag_da_chuyen_doi')}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                {if !empty($migrate_info.tags.migrated)}
                                    {$migrate_info.tags.migrated|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-file-export fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'xuat_file_du_lieu')}
                                {if !empty($migrate_info.success.export)}
                                    <i class="text-success fs-12 ml-10">
                                        {__d('admin', 'da_thuc_hien')}
                                    </i>
                                    <i class="fa fa-check-circle text-success fs-14"></i>
                                {/if}
                            </span>

                            <span class="kt-widget4__number kt-font-info">
                                <span id="btn-export" class="btn btn-sm btn-brand">
                                    <i class="fa fa-file-export fs-14"></i>
                                    <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                                    {__d('admin', 'xuat_file_du_lieu')}
                                </span>
                            </span>
                        </div>

                        {if !empty($migrate_info.success.export)}
                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-box-open fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'tai_file_du_lieu')}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <a href="{ADMIN_PATH}/transform-data/export/download-file-data" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-file-download fs-14"></i>
                                        {__d('admin', 'tai_file_du_lieu')}
                                    </a>
                                </span>
                            </div>

                            <div class="kt-widget4__item">
                                <span class="kt-widget4__icon">
                                    <i class="fa fa-file-download fs-14"></i>
                                </span>

                                <span class="kt-widget4__title kt-widget4__title--light">
                                    {__d('admin', 'tai_media')}
                                </span>

                                <span class="kt-widget4__number kt-font-info">
                                    <a href="{ADMIN_PATH}/transform-data/export/download-file-media" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-image fs-14"></i>
                                        {__d('admin', 'tai_media')}
                                    </a>

                                    <a href="{ADMIN_PATH}/transform-data/export/download-file-thumb" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-images fs-14"></i>
                                       {__d('admin', 'tai_thumbs')}
                                    </a>
                                </span>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>