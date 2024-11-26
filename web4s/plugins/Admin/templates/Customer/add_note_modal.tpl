<!--begin::Modal-->
<div class="modal fade" id="add_note" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{__d('admin', 'ghi_chu')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la la-remove"></span>
                </button>
            </div>
            <form id="add-note-form" class="kt-form kt-form--fit" action="{ADMIN_PATH}/customer/save-note{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off" >
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'noi_dung')}
                        </label>
                        <input name="comment" type="text" class="form-control" placeholder="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary btn-note-save">{__d('admin', 'them_moi')}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->