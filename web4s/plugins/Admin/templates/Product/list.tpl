<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'danh_sach_san_pham')}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/product/add" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_san_pham')}
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

                    <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#import-excel-modal">
                        <i class="fa fa-file-excel"></i>
                        {__d('admin', 'import_san_pham_qua_excel')}
                    </a>
                </div>
            </div>
            <div class="btn-group">
                <button data-link="" type="button" class="btn btn-sm btn-brand">
                    <i class="la la-gears"></i>
                    {__d('admin', 'hanh_dong')}
                </button>
                
                <button type="button" class="btn btn-brand btn-bold dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#discount-product-modal">
                        <i class="fa fa-hourglass-end"></i>
                        {__d('admin', 'giam_gia_hang_loat')}
                    </a>
                </div>
            </div>
            {$this->element('Admin.page/language')}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            {$this->element('../Product/search_advanced')}

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
                                            {__d('admin', 'trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item nh-change-status-all" data-status="1" href="javascript:;">
                                                {__d('admin', 'hoat_dong')}
                                            </a>
                                            <a class="dropdown-item nh-change-status-all" data-status="0" href="javascript:;">
                                                {__d('admin', 'khong_hoat_dong')}
                                            </a>
                                            <a class="dropdown-item nh-change-status-all" data-status="2" href="javascript:;">
                                                {__d('admin', 'ngung_kinh_doanh')}
                                            </a>
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
        <input id="kiotviet" type="hidden" name="kiotviet" value="{if !empty($kiotviet)}{$kiotviet}{/if}">
    </div>
</div>

<div id="quick-upload" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cap_nhat_hinh_anh')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

{$this->element('Admin.page/popover_quick_change')}
{$this->element('Admin.page/popover_kiotviet_change')}
{$this->element('Admin.page/modal_detail')}
{$this->element('Admin.page/modal_import_excel')}
{$this->element('Admin.page/modal_discount_product')}
