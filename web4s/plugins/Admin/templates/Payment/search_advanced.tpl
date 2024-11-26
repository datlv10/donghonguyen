<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nguoi_nhan_nop')}, {__d('admin', 'ma_giao_dich')} ..." autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status', $this->PaymentAdmin->getListStatus(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('payment_method', $this->PaymentAdmin->getListPaymentsForDropdown(), ['id'=>'payment_method', 'empty' => "-- {__d('admin', 'phuong_thuc_thanh_toan')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-icon collapse-search-advanced" data-toggle="collapse" data-target="#collapse-search-advanced">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="collapse-search-advanced" class="collapse collapse-search-advanced-content">
        <div class="kt-margin-t-20">
            <div class="form-group row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_tien')}
                        </label>
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__group">
                                <div class="input-group">
                                    <input id="price_from" type="text" class="form-control form-control-sm number-input" name="price_from" placeholder="{__d('admin', 'tu')}" autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-long-arrow-alt-right"></i></span>
                                    </div>
                                    <input id="price_to" type="text" class="form-control form-control-sm number-input" name="price_to" placeholder="{__d('admin', 'den')}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ngay_tao')}
                        </label>
                        <div class="input-group">
                            <input id="create_from" type="text" class="form-control form-control-sm kt_datepicker" name="create_from" autocomplete="off" placeholder="{__d('admin', 'tu')}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            <input id="create_to" type="text" class="form-control form-control-sm kt_datepicker" name="create_to" autocomplete="off" placeholder="{__d('admin', 'den')}">
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ghi_chu')}
                        </label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="note" name="note" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_theo_ghi_chu')}..." autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>