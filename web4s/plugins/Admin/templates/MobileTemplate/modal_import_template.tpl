<div id="import-template-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cai_dat_giao_dien_moi')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="form-import-template" action="{ADMIN_PATH}/mobile-app/template/import" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chon_tep')} (.zip)
                                    <span class="kt-font-danger">*</span>
                                </label>

                                <div></div>
                                <div class="custom-file">
                                    <input id="select-file-template" name="template_file" type="file" class="custom-file-input">
                                    <label class="custom-file-label" for="select-file-template">
                                        {__d('admin', 'chon')}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    {__d('admin', 'dat_lam_mac_dinh')}
                                </label>

                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success">
                                        <input type="radio" name="set_default" value="1" checked>
                                        {__d('admin', 'co')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--danger">
                                        <input type="radio" name="set_default" value="0">
                                        {__d('admin', 'khong')}
                                        <span></span>
                                    </label>
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
                
                <button id="btn-import-template" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cai_dat_giao_dien')}
                </button>
            </div>
        </div>
    </div>
</div>