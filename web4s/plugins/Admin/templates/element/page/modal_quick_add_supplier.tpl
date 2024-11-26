<div id="quick-add-supplier-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_moi_nha_cung_cap')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="quick-add-supplier-form" action="{ADMIN_PATH}/supplier/save" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ten_nha_cung_cap')}
                                    <span class="kt-font-danger">*</span>
                                </label>
                                <input name="name" class="form-control form-control-sm required" type="text" value="" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ma_nha_cung_cap')}
                                </label>
                                <input name="code" class="form-control form-control-sm" type="text" value="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'so_dien_thoai')}
                                </label>
                                <input name="phone" class="form-control form-control-sm" type="text" value="" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'email')}
                                </label>
                                <input name="email" class="form-control form-control-sm" type="text" value="" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tinh_thanh')}
                                </label>
                                {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'quan_huyen')}
                                </label>
                                {$this->Form->select('district_id', [], ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'phuong_xa')}
                                </label>
                                {$this->Form->select('ward_id', [], ['id' => 'ward_id', 'empty' => "-- {__d('admin', 'phuong_xa')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>                        
                    </div>

                    <div class="form-group mb-0">
                        <label>
                            {__d('admin', 'dia_chi')}
                        </label>
                        <input name="address" class="form-control form-control-sm" type="text" value="" autocomplete="off">
                    </div>                       
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="quick-add-supplier-btn" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'them_nha_cung_cap')}
                </button>
            </div>
        </div>
    </div>
</div>