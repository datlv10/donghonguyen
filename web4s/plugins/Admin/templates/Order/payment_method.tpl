<div id="wrap-payment-confirm" class="p-15 collapse">
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="form-group">
                <label>
                    {__d('admin', 'hinh_thuc_thanh_toan')}
                </label>
                {assign var = list_payment value = $this->PaymentAdmin->getListPaymentsForDropdown()}
                {$this->Form->select('payment_method', $list_payment, ['empty' => null, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="form-group">
                <label>
                    {__d('admin', 'so_tien_thanh_toan')}
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-money-bill-alt"></i>
                        </span>
                    </div>
                    <input name="amount" value="" type="text" class="form-control form-control-sm text-right number-input">
                </div>
                <span class="form-text text-muted">
                    {__d('admin', 'so_tien_thanh_toan_khong_duoc_lon_hon_gia_tri_don_hang')}
                </span>                                        
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="form-group">
                <label>
                    {__d('admin', 'ngay_thanh_toan')}
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input name="payment_time" value="" type="text" class="form-control form-control-sm select-datetime" readonly="true">
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="form-group">
                <label>                                        
                    {__d('admin', 'ma_tham_chieu')}
                </label>
                <input name="reference" class="form-control form-control-sm" type="text" value="">
            </div>
        </div>
    </div>
</div>