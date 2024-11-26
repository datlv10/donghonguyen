<!--begin::Modal-->
<div class="modal fade" id="modal-change-pass" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{__d('admin', 'thay_doi_mat_khau')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la la-remove"></span>
                </button>
            </div>
            <form id="change-pass" class="kt-form kt-form--fit" action="{ADMIN_PATH}/customer/change-password{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off" >
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'mat_khau')}
                        </label>
                        <input name="password" type="text" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary btn-password-save">{__d('admin', 'thay_doi')}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->