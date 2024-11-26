<!--begin::Modal-->
<div class="modal fade" id="modal-account-status" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">{__d('admin', 'thay_doi_trang_thai_tai_khoan')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la la-remove"></span>
                </button>
            </div>
            <form id="account-status-form" class="kt-form kt-form--fit" action="{ADMIN_PATH}/customer/account-status{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off" >
                <div class="modal-body">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    <select name="account_status" id="account-status" class="form-control form-control-sm kt-selectpicker">
                        <option value="0" {if isset($customer.account_status) && $customer.account_status == 0}selected="true"{/if}>
                            {__d('admin', 'ngung_hoat_dong')}
                        </option>
                        <option value="1" {if isset($customer.account_status) && $customer.account_status == 1}selected="true"{/if}>
                            {__d('admin', 'dang_kich_hoat')}
                        </option>
                        <option value="2" {if isset($customer.account_status) && $customer.account_status == 2}selected="true"{/if}>
                            {__d('admin', 'cho_kich_hoat')}
                        </option>
                   </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary btn-account-status">{__d('admin', 'thay_doi')}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->