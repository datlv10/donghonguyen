<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'danh_sach_don_hang')}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/order/add" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'tao_don_hang_moi')}
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
            {$this->element('../Order/search_advanced')}

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__label kt-form__label-no-wrap">
                                <label class="kt-font-bold kt-font-danger-">
                                    {__d('admin', 'da_chon')}
                                    <span id="nh-selected-number">0</span> :
                                </label>
                            </div>

                            <div class="kt-form__control">
                                <div class="btn-toolbar">
                                    <div class="dropdown mr-10">
                                        <button type="button" class="btn btn-brand btn-sm dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                            {__d('admin', 'thay_doi_trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            {foreach from = $this->ListConstantAdmin->listStatus() key = k_status item = status}
                                                <a class="dropdown-item nh-change-status-all" data-status="{$k_status}" href="javascript:;">
                                                    {$status}
                                                </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                  
                                    <button class="btn btn-sm btn-danger nh-delete-all mobile-mb-5" type="button">
                                        {__d('admin', 'xoa_tat_ca')}
                                    </button>                                   
                                </div>
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

{$this->element('Admin.page/popover_quick_change')}