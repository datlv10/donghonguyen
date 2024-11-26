<div id="row-setting-column-modal" class="modal fade modal-setting-template" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thiet_lap_so_cot')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div id="wrap-setting-column" class="modal-body">
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chon_so_cot')}
                            </label>
                            <select id="number-column-select" class="form-control form-control-sm kt-selectpicker">
                                <option value="1">
                                    1 {__d('admin', 'cot')}
                                </option>

                                <option value="2">
                                    2 {__d('admin', 'cot')}
                                </option>

                                <option value="3">
                                    3 {__d('admin', 'cot')}
                                </option>

                                <option value="4">
                                    4 {__d('admin', 'cot')}
                                </option>

                                <option value="5">
                                    5 {__d('admin', 'cot')}
                                </option>

                                <option value="6">
                                    6 {__d('admin', 'cot')}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="kt-portlet nh-template-portlet">
                    <div class="kt-portlet__body p-0">
                        <div class="kt-portlet__content p-10">                                
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered nh-table-config-column">
                                        <tbody>
                                            <tr>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                    
                            </div>
                            
                        </div>
                    </div>
                </div>          
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="save-row-setting-column" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>