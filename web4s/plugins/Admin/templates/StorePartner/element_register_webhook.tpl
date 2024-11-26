<div class="kt-heading kt-heading--md mt-20 mb-20">
    {__d('admin', 'dang_ky_webhook')}
</div>
<div class="kt-form__section kt-form__section--first kt-form__partner-store">
    <div class="kt-wizard-v2__form">
        <table class="table nh-table-item">
            <thead class="thead-light">
                <tr>
                    <th>
                        {__d('admin', 'ten')}
                    </th>

                    <th class="w-20">
                        {__d('admin', 'loai_webhook')}
                    </th>

                    <th class="w-50">
                        {__d('admin', 'duong_dan')}
                    </th>
                    <th>
                        
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr class="item">
                    <td>
                        {__d('admin', 'cap_nhat_ton_kho')}
                    </td>
                    <td>
                        <input nh-type_webhook value="stock.update" type="text" class="form-control" disabled>
                    </td>

                    <td>
                        <input  nh-url_webhook value="{$this->Utilities->getUrlWebsite()}/kiot-viet/webhooks/stock-update" type="text" class="form-control" disabled>
                    </td>
                    <td class="text-center">
                        <span btn-action="register-webhook" class="btn btn-sm btn-info">
                            <i class="fa fa-store"></i>
                            {__d('admin', 'dang_ky')}
                        </span>
                    </td>
                </tr>
                <tr class="item">
                    <td>
                        {__d('admin', 'cap_nhat_san_pham')}
                    </td>

                    <td>
                        <input nh-type_webhook value="product.update" type="text" class="form-control" disabled>
                    </td>

                    <td>
                        <input nh-url_webhook value="{$this->Utilities->getUrlWebsite()}/kiot-viet/webhooks/product-update" type="text" class="form-control" disabled>
                    </td>
                    <td class="text-center">
                        <span btn-action="register-webhook" class="btn btn-sm btn-info">
                            <i class="fa fa-store"></i>
                            {__d('admin', 'dang_ky')}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>    
    </div>
</div>

<div class="kt-heading kt-heading--md mt-20 mb-20">
    {__d('admin', 'danh_sach_cua_hang')}
</div>
<div class="kt-form__section kt-form__section--first kt-form__partner-store">
    <div class="kt-wizard-v2__form">
        <div class="form-group mb-20">
            <span btn-action="list-webhook" class="btn btn-sm btn-info">
                <i class="fa fa-store"></i>
                {__d('admin', 'dong_bo_danh_sach_webhook')}
            </span>
        </div>

        <table class="table nh-table-item">
            <thead class="thead-light">
                <tr>
                    <th>
                        Id
                    </th>

                    <th>
                        Type
                    </th>
                    <th>
                        Url
                    </th>
                </tr>
            </thead>

            <tbody>
                {if !empty($webhook_kiotviet)}
                    {foreach from = $webhook_kiotviet item = item}
                        <tr>
                            <td>
                                {if !empty($item.id)}
                                    {$item.id}
                                {/if}
                            </td>

                            <td>
                                {if !empty($item.type)}
                                    {$item.type}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item.url)}
                                    {$item.url}
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="5" class="text-center">
                            <i>
                                {__d('admin', 'chua_co_danh_sach_webhook')}
                            </i>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>    
    </div>
</div>