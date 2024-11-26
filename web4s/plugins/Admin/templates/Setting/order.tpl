<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'ap_dung_tru_so_luong_san_pham_sau_khi_tao_don_hang')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <div class="clearfix mb-10">
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="minus_quantity_product" value="1" {if !empty($order.minus_quantity_product)}checked{/if}>
                                {__d('admin', 'co')}
                                <span></span>
                            </label>
                        </div>

                        <div class="clearfix">
                            <label class="kt-radio kt-radio--tick kt-radio--danger">
                                <input type="radio" name="minus_quantity_product" value="0" {if empty($order.minus_quantity_product)}checked{/if}>
                                {__d('admin', 'khong')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'dieu_kien_ap_dung_tru_so_luong_san_pham')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <div class="clearfix mb-10">
                            <label class="kt-radio kt-radio--tick kt-radio--warning mr-20">
                                <input type="radio" name="quantity_conditions" value="create_new" {if empty($order.quantity_conditions) || $order.quantity_conditions == 'create_new'}checked{/if}>
                                {__d('admin', 'sau_khi_tao_don_hang_moi')}
                                <span></span>
                            </label>
                        </div>

                        <div class="clearfix mb-10">
                            <label class="kt-radio kt-radio--tick kt-radio--warning">
                                <input type="radio" name="quantity_conditions" value="{CONFIRM}" {if !empty($order.quantity_conditions) && $order.quantity_conditions == 'confirm'}checked{/if}>
                                {__d('admin', 'sau_khi_xac_nhan_don_hang')}
                                <span></span>
                            </label>
                        </div>

                        <div class="clearfix mb-10">
                            <label class="kt-radio kt-radio--tick kt-radio--warning">
                                <input type="radio" name="quantity_conditions" value="{EXPORT}" {if !empty($order.quantity_conditions) && $order.quantity_conditions == "{EXPORT}"}checked{/if}>
                                {__d('admin', 'sau_khi_xuat_kho')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
