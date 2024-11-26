<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
            
            <a href="javascript:;" id="btn-add-order-source" class="btn btn-sm btn-brand" data-toggle="modal" data-target="#add-order-source">
                <i class="la la-plus"></i>
                {__d('admin', 'them_nguon_don_hang')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>

<div id="add-order-source" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cap_nhat_nguon_don_hang')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="add-order-source-form" action="{ADMIN_PATH}/object/save" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_nguon_hang')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <input name="name" class="form-control form-control-sm required" type="text" value="" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma_nguon_don_hang')}
                        </label>
                        <input name="code" class="form-control form-control-sm" type="text" value="" autocomplete="off">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-save-order-source" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>