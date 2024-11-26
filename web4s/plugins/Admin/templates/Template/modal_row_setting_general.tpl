<div id="row-setting-general-modal" class="modal fade modal-setting-template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thiet_lap_thong_tin_cua_dong')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div id="wrap-setting-general" class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">
                        ID Row
                    </label>
                    <div class="col-lg-6">
                        <input name="id_row" type="text" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">
                        Style Class
                    </label>
                    <div class="col-lg-6">
                        <input name="style_class" type="text" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">
                        {__d('admin', 'toan_man_hinh')}
                    </label>
                    <div class="col-lg-6">
                        <div class="kt-checkbox-inline">
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                                <input name="full_screen" value="1" type="checkbox"> {__d('admin', 'rong_toan_man_hinh')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="save-row-setting-general" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>