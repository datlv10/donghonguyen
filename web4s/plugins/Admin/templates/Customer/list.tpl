<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/customer/add" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi')}
            </a>

            <div class="btn-group">
                <button data-link="" type="button" class="btn btn-sm btn-brand">
                    <i class="fa fa-file-excel"></i>
                    {__d('admin', 'excel')}
                </button>
                
                <button type="button" class="btn btn-brand btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:;" nh-export="current">
                        <i class="fa fa-file-excel"></i>
                        {__d('admin', 'xuat_excel_trang_hien_tai')}
                    </a>

                    <a class="dropdown-item" href="javascript:;" nh-export="all">
                        <i class="fa fa-file-excel"></i>
                        {__d('admin', 'xuat_excel_toan_bo_cac_trang')}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            <div class="kt-form">
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="row align-items-center">
                            <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                <div class="kt-input-icon kt-input-icon--left">
                                    <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}" autocomplete="off">
                                    <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                        <span><i class="la la-search"></i></span>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                <div class="kt-form__group">
                                    <div class="kt-form__control">
                                        {$this->Form->select('status', $this->ListConstantAdmin->listStatus(), ['id'=>'nh_status', 'empty' => {__d('admin', 'trang_thai')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                                    <i class="fa fa-undo-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>
{$this->element('../Customer/add_account_modal')}
{$this->element('../Customer/account_status_modal')}