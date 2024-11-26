<!--begin::Modal-->
<div class="modal fade" id="modal-add-account" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{__d('admin', 'them_tai_khoan')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la la-remove"></span>
                </button>
            </div>
            <form id="account-form" class="kt-form kt-form--fit" action="{ADMIN_PATH}/customer/add-account{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off" >
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_dang_nhap')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <input name="username" value="" class="form-control form-control-sm" type="text">
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'mat_khau')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <input name="password" value="" class="form-control form-control-sm" type="text">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary btn-account-save">{__d('admin', 'them_moi')}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->