{$this->element('../Report/element_hearder', [
    'title_for_layout' => $title_for_layout
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__body">
            <div class="nh-search-advanced">
                <div class="kt-form">
                    <form nh-form="list-report" action="{ADMIN_PATH}/report/load-staff" method="POST">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="input-daterange input-group">
                                            <input id="create_from" type="text" class="form-control kt_datepicker form-control-sm" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-ellipsis-h"></i>
                                                </span>
                                            </div>
                                            <input id="create_to" type="text" class="form-control kt_datepicker form-control-sm" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                                        </div>
                                    </div>       
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-form__control">
                                            {$this->Form->select('staff_id', $this->UserAdmin->getListUser(), ['id'=>'staff_id', 'empty' => "-- {__d('admin', 'nhan_vien')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'autocomplete' => 'off'])}
                                        </div>
                                    </div>     
                                    
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-form__control">
                                            <select name="status" id="status" class="form-control form-control-sm kt-selectpicker">
                                                <option value="" selected="selected">
                                                    -- {__d('admin', 'trang_thai_don_hang')} --
                                                </option>

                                                <option value="{DONE}">
                                                    {__d('admin', 'thanh_cong')}
                                                </option>
        
                                                <option value="{CANCEL}">
                                                    {__d('admin', 'don_huy')}
                                                </option>
                                            </select>
                                        </div>        
                                    </div>
                                    
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <button id="btn-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                                            <i class="fa fa-undo-alt"></i>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="wrap-report">
            {$this->element("../Report/element_report_staff")}
        </div>
    </div>
</div>