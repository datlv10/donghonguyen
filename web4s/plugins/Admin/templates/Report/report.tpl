<div class="kt-subheader  kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <div class="kt-subheader__wrapper">
                <a href="javascript:;" class="btn kt-subheader__btn-secondary" load-group-report="week">
                    {__d('admin', 'tuan')}
                </a>
                <a href="javascript:;" class="btn kt-subheader__btn-secondary" load-group-report="month">
                    {__d('admin', 'thang')}
                </a>
                <a href="javascript:;" class="btn kt-subheader__btn-secondary" load-group-report="year">
                    {__d('admin', 'nam_year')}
                </a>

                <a href="javascript:;" class="btn kt-subheader__btn-secondary active" load-group-report="all">
                    {__d('admin', 'tat_ca')}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div id="wrap-revenue" class="kt-portlet kt-portlet--height-fluid-half entire-report-dashboard-half"></div>
        </div>
        <div class="col-xl-3 col-lg-4">
            <div id="wrap-source" class="kt-portlet kt-portlet--height-fluid-half entire-report-dashboard-half"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-7 col-lg-7">
            <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'top_10_san_pham_ban_chay')}
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-datatable mb-0" id="kt_datatable_product"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5">
            <div id="wrap-city" class="kt-portlet entire-report-dashboard"></div>
        </div>
    </div>
</div>