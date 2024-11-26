<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <form id="main-form" action="{ADMIN_PATH}/setting/save/affiliate" validate method="POST" autocomplete="off">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thiet_lap_chiet_khau_don_hang')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="kt-font-bold">
                                {__d('admin', 'chiet_khau_don_hang_ap_dung_ma_gioi_thieu')}
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-sm btn-secondary" type="button">%</button>
                                </div>
                                <input name="value_discount" value="{if !empty($affiliate.value_discount)}{$affiliate.value_discount}{/if}" type="text" class="form-control form-control-sm">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'trang_thai_ap_dung_chiet_khau_voi_ma_khuyen_mai')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="status_discount_sale" value="1" {if !empty($affiliate.status_discount_sale)}checked{/if}>
                            {__d('admin', 'hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="status_discount_sale" value="0" {if empty($affiliate.status_discount_sale)}checked{/if}>
                            {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>

                    <span class="form-text text-muted">
                        - {__d('admin', 'mac_dinh_chiet_khau_don_hang_se_la_chiet_khau_theo_ma_gio_thieu')} 
                    </span>

                    <span class="form-text text-muted">
                        - {__d('admin', 'neu_trang_thai_hoat_dong_thi_se_ap_dung_chiet_khau_don_hang_theo_ma_gioi_thieu_va_ma_khuyen_mai')} 
                    </span>
                </div>

                <div class="row">

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="kt-font-bold">
                                {__d('admin', 'chiet_khau_don_hang_ap_dung_ma_gioi_thieu_va_ma_khuyen_mai')}
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-sm btn-secondary" type="button">%</button>
                                </div>
                                <input name="value_discount_sale" value="{if !empty($affiliate.value_discount_sale)}{$affiliate.value_discount_sale}{/if}" type="text" class="form-control form-control-sm">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="kt-portlet">
        <form id="main-form" action="{ADMIN_PATH}/setting/save/affiliate" validate method="POST" autocomplete="off">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thiet_lap_ty_le_hoa_hong')}
                    </h3>
                </div>
                
                <div class="kt-portlet__head-toolbar">
                    <span id="add-new-level" class="btn btn-sm btn-success">
                        {__d('admin', 'them_cap_moi')}
                    </span>
                </div>
            </div>
            <div class="kt-portlet__body">

                <div id="list-level" class="row">
                    {if !empty($commissions)}
                        {foreach from = $commissions key = key item = item}
                            {assign var = image_url value = "{if !empty($item.image)}{$item.image}{/if}"}
                            {assign var = image_source value = "{if !empty($item.source)}{$item.source}{/if}"}
                            {$this->element("../Setting/item_affiliate", [
                                'item' => $item,
                                'key' => $key,
                                'image_url' => $image_url,
                                'image_source' => $image_source
                            ])}
                        {/foreach}
                    {else}
                        {$this->element("../Setting/item_affiliate", [
                            'item' => '',
                            'key' => 0,
                            'image_url' => '',
                            'image_source' => ''
                        ])}
                    {/if}
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{$this->element('Admin.page/popover_quick_change')}