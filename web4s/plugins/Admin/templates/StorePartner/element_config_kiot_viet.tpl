<form id="kiotviet-form" action="{ADMIN_PATH}/setting/save/store_kiotviet" method="POST" autocomplete="off">

    <div class="kt-portlet__head px-0 pt-0 mb-20">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'thong_tin_co_ban')}
            </h3>
        </div> 
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">

            <div class="form-group">
                <label>
                    {__d('admin', 'trang_thai')}
                </label>
                <div class="kt-radio-inline mt-5">

                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                        <input type="radio" name="config[status]" value="1" {if !empty($config_kiotviet.status)}checked{/if}> 
                            {__d('admin', 'hoat_dong')}
                        <span></span>
                    </label>

                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                        <input type="radio" name="config[status]" value="0" {if empty($config_kiotviet.status)}checked{/if}> 
                            {__d('admin', 'khong_hoat_dong')}
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'thong_tin_cau_hinh')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'ten_ket_noi')}
                    </label>

                    <input name="config[name]" value="{if !empty($config_kiotviet.name)}{$config_kiotviet.name}{/if}" type="text" class="form-control" >
                </div>

                <div class="col-md-6 col-12">
                    <label>
                        Client ID
                    </label>

                    <input name="config[client_id]" value="{if !empty($config_kiotviet.client_id)}{$config_kiotviet.client_id}{/if}" type="text" class="form-control" >
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'ma_bao_mat')}
                    </label>

                    <input name="config[code]" value="{if !empty($config_kiotviet.code)}{$config_kiotviet.code}{/if}" type="text" class="form-control">
                </div>
            </div>
        </div>
    </div>
    
    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mb-20"></div>

    <div class="kt-heading kt-heading--md mt-20 mb-20">
        {__d('admin', 'danh_sach_cua_hang')}
    </div>

    <div class="kt-form__section kt-form__section--first kt-form__partner-store">
        <div class="kt-wizard-v2__form">
            <div class="form-group mb-20">
                <span btn-action="sync-stores" class="btn btn-sm btn-info">
                    <i class="fa fa-store"></i>
                    {__d('admin', 'dong_bo_cua_hang_tu_kiotviet')}
                </span>
            </div>

            <table class="table nh-table-item">
                <thead class="thead-light">
                    <tr>
                        <th>
                            {__d('admin', 'cua_hang')}
                        </th>

                        <th>
                            {__d('admin', 'so_dien_thoai')}
                        </th>
                        <th>
                            {__d('admin', 'email')}
                        </th>
                        <th>
                            {__d('admin', 'dia_chi')}
                        </th>
                        <th>
                            {__d('admin', 'mac_dinh')}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {if !empty($stores_kiotviet)}
                        {foreach from = $stores_kiotviet item = store}
                            <tr>
                                <td>
                                    {if !empty($store.name)}
                                        {$store.name}
                                    {/if}
                                </td>

                                <td>
                                    {if !empty($store.phone)}
                                        {$store.phone}
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($store.email)}
                                        {$store.email}
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($store.address)}
                                        {$store.address}
                                    {/if}
                                </td>

                                <td class="text-center">
                                    <label class="kt-radio kt-radio--bold" style="top: -4px;"> 
                                        <input nh-is-default type="radio" class="nh-is-default" data-id="{if !empty($store.id)}{$store.id}{/if}" 
                                         {if !empty($store.is_default == 1)}checked{/if}/> 
                                        <span></span> 
                                    </label>
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="5" class="text-center">
                                <i>
                                    {__d('admin', 'khong_co_thong_tin_cua_hang')}
                                </i>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>    
        </div>
    </div>

    {* đăng ký webhook *}
    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mb-20"></div>

    <div class="kt-heading kt-heading--md mt-20 mb-20">
        {__d('admin', 'dang_ky_webhook')}
    </div>
    <div class="form-group">
        <span btn-action="delete-webhook" nh-url="" class="btn btn-sm btn-info btn-custom-key">
            <i class="fa fa-window-close"></i>
            {__d('admin', 'huy_webhook')}
        </span>
    </div>

    <div class="kt-form__section kt-form__section--first kt-form__partner-store">
        <div class="kt-wizard-v2__form">
            <table class="table nh-table-item">
                <thead class="thead-light">
                    <tr>
                        <th class="w-15">
                            {__d('admin', 'loai_webhook')}
                        </th>

                        <th class="w-50">
                            {__d('admin', 'duong_dan')}
                        </th>
                        <th>
                            
                        </th>
                        
                        <th>
                            
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <tr class="item">
                        <td>
                            <input nh-type-webhook value="stock.update" type="text" class="form-control" disabled>
                        </td>

                        <td>
                            <input value="{if !empty($list_webhook['stock_update'])}{$list_webhook['stock_update']['url']}{/if}" type="text" class="form-control" disabled>
                            <input nh-url-webhook value="{$this->Utilities->getUrlWebsite()}/kiot-viet/webhooks/stock-update" type="hidden" class="form-control" disabled>
                        </td>

                        <td>
                            <div class="kt-wizard-v2__nav-label-desc">
                                {if !empty($webhooks_kiotviet_format['stock_update']) && !empty($list_webhook['stock_update']) && $webhooks_kiotviet_format['stock_update'] == $list_webhook['stock_update']['type']}
                                    <i class="kt-font-success fw-400 fs-12">
                                        {__d('admin', 'dang_hoat_dong')}
                                    </i>
                                {else}
                                    <i class="kt-font-danger fw-400 fs-12">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </i>
                                {/if}
                            </div>
                        </td>
                        

                        <td class="text-center">
                            <span btn-action="register-webhook" nh-url="" class="btn btn-sm btn-info w-100 btn-custom-key">
                                <i class="fa fa-store"></i>
                                {__d('admin', 'dang_ky')}
                            </span>
                        </td>
                    </tr>
                    <tr class="item">
                        <td>
                            <input nh-type-webhook value="product.update" type="text" class="form-control" disabled>
                        </td>

                        <td>
                            <input value="{if !empty($list_webhook['product_update'])}{$list_webhook['product_update']['url']}{/if}" type="text" class="form-control" disabled>
                            <input nh-url-webhook value="{$this->Utilities->getUrlWebsite()}/kiot-viet/webhooks/product-update" type="hidden" class="form-control" disabled>
                        </td>
                        <td>
                            <div class="kt-wizard-v2__nav-label-desc">
                                {if !empty($webhooks_kiotviet_format['product_update']) && !empty($list_webhook['product_update']) && $webhooks_kiotviet_format['product_update'] == $list_webhook['product_update']['type']}
                                    <i class="kt-font-success fw-400 fs-12">
                                        {__d('admin', 'dang_hoat_dong')}
                                    </i>
                                {else}
                                    <i class="kt-font-danger fw-400 fs-12">
                                        {__d('admin', 'khong_hoat_dong')}
                                    </i>
                                {/if}
                            </div>
                        </td>
                        
                        <td class="text-center">
                            <span btn-action="register-webhook" class="btn btn-sm btn-info w-100 btn-custom-key btn-custom-key">
                                <i class="fa fa-store"></i>
                                {__d('admin', 'dang_ky')}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>    
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mb-20"></div>
    <div class="kt-heading kt-heading--md mt-20 mb-20">
        {__d('admin', 'dong_bo')}
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <span btn-action="attribute-webhook" class="btn btn-sm btn-info btn-custom-key w-100">
                    <i class="fa fa-share-alt-square"></i>
                    {__d('admin', 'dong_bo_thuoc_tinh')}
                </span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-0">
                <a class="btn btn-sm btn-info btn-custom-key w-100" href="javascript:;" data-toggle="modal" data-target="#sync-all-product-modal">
                    <img src="/admin/assets/media/store_partner/kiotviet.png" class="img-kiotviet">
                    {__d('admin', 'dong_bo_tat_ca_san_pham_{0}', ['KiotViet'])}
                </a>
            </div>
        </div>
    </div>
</form>

    
