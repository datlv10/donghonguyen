<div id="config-id-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_id_du_lieu')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {assign var = config_id value = []}
                {if !empty($migrate.config_id.data)}
                    {assign var = config_id value = $migrate.config_id.data}
                {/if}

                <form id="config-id-form" action="{ADMIN_PATH}/transform-data/export/config-id" method="POST" autocomplete="off">
                    <div class="alert alert-warning" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning"></i>
                        </div>

                        <div class="alert-text">
                            {__d('admin', 'luu_y_kiem_tra_id_trong_cac_bang_cua_database_sau_nay_se_import_du_lieu_dam_bao_rang_id_cau_hinh_cho_cac_bang_la_gia_tri_cao_nhat_hien_tai')}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-8">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_danh_muc')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-align-justify"></i>
                                        </span>
                                    </div>
                                    <input name="category_id_start" value="{if !empty($config_id.category_id_start)}{$config_id.category_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_bai_viet')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-file-alt"></i>
                                        </span>
                                    </div>
                                    <input name="article_id_start" value="{if !empty($config_id.article_id_start)}{$config_id.article_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_san_pham')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-box-open"></i>
                                        </span>
                                    </div>
                                    <input name="product_id_start" value="{if !empty($config_id.product_id_start)}{$config_id.product_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_thuong_hieu')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fab fa-dribbble-square"></i>
                                        </span>
                                    </div>
                                    <input name="brand_id_start" value="{if !empty($config_id.brand_id_start)}{$config_id.brand_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_the_tag')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-tags"></i>
                                        </span>
                                    </div>
                                    <input name="tag_id_start" value="{if !empty($config_id.tag_id_start)}{$config_id.tag_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'id_bat_dau_cua_thuoc_tinh_mo_rong')}
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-indent"></i>
                                        </span>
                                    </div>
                                    <input name="attribute_id_start" value="{if !empty($config_id.attribute_id_start)}{$config_id.attribute_id_start}{else}1000{/if}" class="form-control form-control-sm" type="number">
                                </div>
                            </div>
                        </div>
                    </div>                    
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-config-id" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>