<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('action', $actions, ['id' => 'action', 'empty' => "-- {__d('admin', 'hanh_dong')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        {$this->Form->select('type', $list_type, ['id' => 'type', 'empty' => "-- {__d('admin', 'loai')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>   

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        {$this->Form->select('type', $users, ['id' => 'user_id', 'empty' => "-- {__d('admin', 'nguoi_thuc_hien')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <span class="btn btn-outline-secondary btn-sm btn-icon collapse-search-advanced" data-toggle="collapse" data-target="#collapse-search-advanced">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        <span id="btn-refresh-search" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </span>
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
                            {__d('admin', 'thoi_gian')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="create_from" type="text" class="form-control kt_datepicker" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-ellipsis-h"></i>
                                </span>
                            </div>
                            <input id="create_to" type="text" class="form-control kt_datepicker" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>