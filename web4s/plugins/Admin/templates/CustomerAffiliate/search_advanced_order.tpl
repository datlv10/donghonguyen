<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'trang_thai_don_hang')}
                            </label>
                            <div class="kt-form__group">
                                <div class="kt-form__control">
                                    {$this->Form->select('status', $this->ListConstantAdmin->listStatusOrder(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tong_tien')}
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
                </div>
            </div>
        </div>
    </div>
</div>