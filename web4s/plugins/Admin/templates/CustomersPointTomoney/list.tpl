<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <span data-toggle="modal" data-target="#modal-created-request-withdrawal" class="kt-nav__link btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'tao_yeu_cau')}
            </span>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            {$this->element('../CustomersPointTomoney/search_advanced')}
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>
{$this->element('Admin.page/popover_quick_change')}
{$this->element("../CustomersPointTomoney/modal_created_request-withdrawal")}