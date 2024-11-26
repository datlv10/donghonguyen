<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'thong_tin_khach_hang')}
                            </label>
                            <div class="kt-input-icon kt-input-icon--left">
                                <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'khach_hang')}, {__d('admin', 'so_dien_thoai')}, {__d('admin', 'email')} ..." autocomplete="off">
                                <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                    <span><i class="la la-search"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'trang_thai')}
                            </label>
                            <div class="kt-form__group">
                                <div class="kt-form__control">
                                    {$this->Form->select('status', $this->ListConstantAdmin->listStatusPointHistory(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ngay_yeu_cau')}
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
                                {__d('admin', 'ngay_duyet')}
                            </label>
                            <div class="input-group">
                                <input id="confirm_from" type="text" class="form-control form-control-sm kt_datepicker" name="confirm_from" autocomplete="off" placeholder="{__d('admin', 'tu')}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                </div>
                                <input id="confirm_to" type="text" class="form-control form-control-sm kt_datepicker" name="confirm_to" autocomplete="off" placeholder="{__d('admin', 'den')}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>