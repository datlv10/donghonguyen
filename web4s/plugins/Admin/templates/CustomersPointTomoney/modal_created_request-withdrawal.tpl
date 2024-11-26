{assign var = point_to_money value = $this->SettingAdmin->getPointToMoneyInfo()}
<div id="modal-created-request-withdrawal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'tao_yeu_cau_rut_tien_theo_ky_han')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <form id="quick-save-request-withdrawal" action="{ADMIN_PATH}/customer/affiliate/point-tomoney/save" method="POST" autocomplete="off">
                <div class="modal-body">
                    <div nh-wrap-select="{CUSTOMER}">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'thong_tin_khach_hang')}
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="flaticon-search"></i>
                                    </span>
                                </div>
                                <input suggest-item="{CUSTOMER}" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_khach_hang')}" autocomplete="off">
                            </div>
                        </div>

                        <div nh-item-selected class="clearfix">
                            <p class="mb-0 name-partner">
                                <label class="fw-600">
                                    {__d('admin', 'ho_ten')}: 
                                </label>
                                <span></span>
                            </p>

                            <p class="mb-0 point-partner">
                                <label class="fw-600 mb-0">
                                    {__d('admin', 'diem_vi')}: 
                                </label>
                                <span></span>
                            </p>
                        </div>
                        <input type="hidden" class="form-control form-control-sm" value="" name="customer_id">
                    </div>

                    <div class="form-group">
                        <label for="bank_id">
                            {__d('admin', 'ngan_hang')}
                            <span class="required">*</span>
                        </label>
                        {$this->Form->select('bank_id', [], ['id' => 'bank_id', 'empty' => "-- {__d('admin', 'chon_ngan_hang')} --", 'class' => 'form-control form-control-sm selectpicker input-hover', 'data-size' => 10])}
                    </div>

                    <div class="form-group">
                        <label for="point">
                            {__d('template', 'diem')}: 
                            <span class="required">*</span>
                            <small class="text-muted font-weight-normal">
                                ({__d('admin', 'so_diem_co_the_rut')}: 
                                <span class="withdrawable-points">0</span>
                                {__d('template', 'diem')})
                            </small>
                        </label>
                        <div class="input-group">
                            <input id="point" nh-point-money="{$point_to_money}" nh-point-max="0" name="point" value="" type="text" class="form-control form-control-sm number-input" autocomplete="off">
                            <input id="money" name="money" value="" type="hidden">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-success color-white" type="button">
                                    <span class="number-input point-to-money" inputmode="decimal">0</span>
                                    <small>VND</small>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="note">
                            {__d('template', 'ghi_chu')}: 
                        </label>
                        <input name="note" value="" type="text" class="bg-white border form-control rounded input-hover" autocomplete="off">
                    </div>
                    <input type="hidden" value="0" name="type">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        {__d('admin', 'dong')}
                    </button>
                    
                    <button id="quick-add-request-withdrawal" type="button" class="btn btn-sm btn-primary">
                        {__d('admin', 'tao_yeu_cau')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>