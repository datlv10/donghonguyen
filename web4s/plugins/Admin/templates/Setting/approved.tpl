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
        </div>        
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'duyet_bai_viet')}
                </h3>
            </div>
        </div>

        <div class="kt-portlet__body">
            <form id="approved-article-form" action="{ADMIN_PATH}/setting/save/approved_article" method="POST" autocomplete="off">
                <div class="form-group mb-30">
                    <label>
                        {__d('admin', 'kich_hoat_duyet_bai_viet')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="approved" value="1" {if !empty($approved_article.approved)}checked{/if}>
                            {__d('admin', 'co')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="approved" value="0" {if empty($approved_article.approved)}checked{/if}>
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'quyen_duoc_duyet_bai')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        {if !empty($roles)}
                            {foreach from = $roles key = role_id item = name}
                                <div class="clearfix mb-10">
                                    {assign var = role_selected value = false}
                                    {if !empty($approved_article.role_id) && $role_id|in_array:$approved_article.role_id}
                                        {assign var = role_selected value = true}
                                    {/if}

                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                        <input type="checkbox" name="role_id[]" value="{$role_id}" {if $role_selected}checked="true"{/if}>
                                        {$name}
                                        <span></span>
                                    </label>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-font-bold fs-11 text-danger">
                        {__d('admin', 'luu_y')}:
                        {__d('admin', 'khi_kich_hoat_duyet_bai_viet_thi_nhung_tai_khoan_khong_nam_trong_nhom_quyen_duoc_duyet_bai_khi_cap_nhat_bai_viet_trang_thai_cua_bai_viet_se_chuyen_ve_cho_duyet')}
                    </label>
                </div>

                <div class="form-group">
                    <button id="btn-save-approved-article" type="button" class="btn btn-sm btn-brand">
                        {__d('admin', 'cap_nhat')}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {if !empty($plugins[{PRODUCT}])}
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'duyet_san_pham')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <form id="approved-product-form" action="{ADMIN_PATH}/setting/save/approved_product" method="POST" autocomplete="off">
                    <div class="form-group mb-30">
                        <label>
                            {__d('admin', 'kich_hoat_duyet_san_pham')}
                        </label>

                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="approved" value="1" {if !empty($approved_product.approved)}checked{/if}>
                                {__d('admin', 'co')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--danger">
                                <input type="radio" name="approved" value="0" {if empty($approved_product.approved)}checked{/if}>
                                {__d('admin', 'khong')}
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="kt-font-bold">
                            {__d('admin', 'quyen_duoc_duyet_bai')}
                        </label>

                        <div class="kt-radio-inline mt-5">
                            {if !empty($roles)}
                                {foreach from = $roles key = role_id item = name}
                                    <div class="clearfix mb-10">
                                        {assign var = role_selected value = false}
                                        {if !empty($approved_product.role_id) && $role_id|in_array:$approved_product.role_id}
                                            {assign var = role_selected value = true}
                                        {/if}
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                            <input type="checkbox" name="role_id[]" value="{$role_id}" {if $role_selected}checked="true"{/if}>
                                            {$name}
                                            <span></span>
                                        </label>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>

                    <div class="form-group">
                        <button id="btn-save-approved-product" type="button" class="btn btn-sm btn-brand">
                            {__d('admin', 'cap_nhat')}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    {/if}
</div>
