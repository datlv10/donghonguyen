<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <span add-genealogy class="kt-nav__link btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi')}
            </span>
            <div class="btn-group d-none" group-excel>
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

            {$this->element('Admin.page/language')}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-portlet__body">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item ">
                        <a class="nav-link border active" data-toggle="tab" href="#genealogy_vertical" nh-tab-genealogy>
                            <i class="la la-sitemap"></i>
                            Phả đồ dọc
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border" data-toggle="tab" href="#genealogy_list" nh-tab-genealogy>
                            <i class="la la-list-ol"></i>
                            Xem danh sách
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="genealogy_vertical" role="tabpanel">
                        <div id="kt_tree" class="tree-demo"></div>
                    </div>

                    <div class="tab-pane" id="genealogy_list" role="tabpanel">
                        {$this->element('../Genealogy/search_advanced')}

                        <div class="kt-datatable mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{$this->element("../Genealogy/modal_update")}
{$this->element("../Genealogy/modal_detail")}