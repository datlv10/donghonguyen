<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_giao_dich')}
                            </label>
                            <div class="kt-input-icon kt-input-icon--left">
                                <input id="nh-code-point" name="code" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'ma_giao_dich')}" autocomplete="off">
                                <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                    <span><i class="la la-search"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'loai_giao_dich')}
                            </label>
                            <div class="kt-form__group">
                                <div class="kt-form__control">
                                    {$this->Form->select('status', $this->ListConstantAdmin->listActionTypePointHistory(), ['id'=>'action_type', 'empty' => "-- {__d('admin', 'loai_giao_dich')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ngay_giao_dich')}
                            </label>
                            <div class="input-group">
                                <input id="point_create_from" type="text" class="form-control form-control-sm kt_datepicker" name="create_from" autocomplete="off" placeholder="{__d('admin', 'tu')}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                </div>
                                <input id="point_create_to" type="text" class="form-control form-control-sm kt_datepicker" name="create_to" autocomplete="off" placeholder="{__d('admin', 'den')}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>