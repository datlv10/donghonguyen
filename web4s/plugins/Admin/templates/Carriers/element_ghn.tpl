<form id="ghn-form" action="{ADMIN_PATH}/setting/carriers/{GIAO_HANG_NHANH}" method="POST" autocomplete="off">
    <div class="kt-heading kt-heading--md mt-10">
        {__d('admin', 'thong_tin_co_ban')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">

            <div class="form-group">
                <label>
                    {__d('admin', 'trang_thai')}
                </label>
                <div class="kt-radio-inline mt-5">

                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                        <input type="radio" name="status" value="1" {if !empty($item.status)}checked{/if}> 
                            {__d('admin', 'dang_hoat_dong')}
                        <span></span>
                    </label>

                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                        <input type="radio" name="status" value="0" {if !isset($item.status) || empty($item.status)}checked{/if}> 
                            {__d('admin', 'khong_hoat_dong')}
                        <span></span>
                    </label>
                </div>
            </div>            
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'thong_tin_cau_hinh')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        Client ID
                    </label>

                    <input name="config[client_id]" value="{if !empty($item.config.client_id)}{$item.config.client_id}{/if}" type="text" class="form-control" >
                </div>

                <div class="col-md-6 col-12">
                    <label>
                        API Token
                    </label>

                    <input name="config[api_token]" value="{if !empty($item.config.api_token)}{$item.config.api_token}{/if}" type="text" class="form-control">
                </div>
            </div>            

            <div class="form-group">
                <label>
                    {__d('admin', 'che_do')}
                </label>

                {$this->Form->select('config[mode]', $this->ListConstantAdmin->listMode(), ['empty' => '', 'default' => "{if !empty($item.config.mode)}{$item.config.mode}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
            </div>

            <div class="form-group">
                <label>
                    Webhook URL
                </label>

                <p>
                    <i class="kt-font-bold kt-badge kt-badge--success kt-badge--inline fs-13 pt-5 pb-5 pl-10 pr-10">
                        {$this->Utilities->getUrlWebsite()}/shipping/webhook/{GIAO_HANG_NHANH}
                    </i>
                </p>
            </div>

        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'danh_sach_cua_hang')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <div class="form-group">
                <span btn-action="sync-stores" class="btn btn-sm btn-info">
                    <i class="fa fa-store"></i>
                    {__d('admin', 'dong_bo_cua_hang_tu_giao_hang_nhanh')}
                </span>
            </div>
            
            <table class="table nh-table-item">
                <thead class="thead-light">
                    <tr>
                        <th class="w-30">
                            {__d('admin', 'cua_hang')}
                        </th>

                        <th class="w-20">
                            {__d('admin', 'so_dien_thoai')}
                        </th>

                        <th class="w-50">
                            {__d('admin', 'dia_chi')}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {if !empty($item.config.stores)}
                        {foreach from = $item.config.stores item = store}
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
                                    {if !empty($store.address)}
                                        {$store.address}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="5" class="text-center">
                                <i>
                                    {__d('admin', 'chua_co_cua_hang_nao')}
                                </i>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>            
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'dong_bo_du_lieu_tinh_thanh')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <table class="table nh-table-item">
                <thead class="thead-light">
                    <tr>
                        <th class="w-30">
                            {__d('admin', 'dong_bo_du_lieu')}
                        </th>

                        <th class="w-40">
                            {__d('admin', 'trang_thai')}
                        </th>

                        <th class="w-20">

                        </th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {__d('admin', 'dong_bo_tinh_thanh')}
                        </td>

                        <td>
                            {if !empty($item.config.sync_data.citites)}
                                <b class="text-success">
                                    {__d('admin', 'da_dong_bo')} 
                                </b>
                            {else}
                                <b class="text-danger">
                                    {__d('admin', 'chua_dong_bo')} 
                                </b>            
                            {/if}
                        </td>

                        <td class="text-right">
                            <span btn-action="sync-cities" class="btn btn-sm btn-info">
                                <i class="fa fa-sync-alt"></i>
                                {__d('admin', 'thuc_hien')}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            {__d('admin', 'dong_bo_quan_huyen')}
                        </td>

                        <td>
                            {if !empty($item.config.sync_data.districts)}
                                <b class="text-success">
                                    {__d('admin', 'da_dong_bo')} 
                                </b>
                            {else}
                                <b class="text-danger">
                                    {__d('admin', 'chua_dong_bo')} 
                                </b>
                            {/if}
                        </td>

                        <td class="text-right">
                            <span btn-action="sync-districts" class="btn btn-sm btn-info">
                                <i class="fa fa-sync-alt"></i>
                                {__d('admin', 'thuc_hien')}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            {__d('admin', 'dong_bo_phuong_xa')}
                        </td>

                        <td>
                            {if !empty($item.config.sync_data.wards)}
                                <b class="text-success">
                                    {__d('admin', 'da_dong_bo')} 
                                </b>
                            {else}
                                <b class="text-danger">
                                    {__d('admin', 'chua_dong_bo')} 
                                </b>            
                            {/if}
                        </td>

                        <td class="text-right">
                            <span btn-action="sync-wards" class="btn btn-sm btn-info">
                                <i class="fa fa-sync-alt"></i>
                                {__d('admin', 'thuc_hien')}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>   
        </div>
    </div>

    {* khởi tạo dữ liệu tỉnh thành quận huyện từ HGN về hệ thống *}
    <div class="form-group mt-30 d-none">
        <span btn-action="initialization-cities" class="btn btn-sm btn-warning">
            <i class="fa fa-map-marked-alt"></i>
            {__d('admin', 'khoi_tao_du_lieu_tinh_thanh')}
        </span>

        <span btn-action="initialization-districts" class="btn btn-sm btn-warning">
            <i class="fa fa-map-marked-alt"></i>
            {__d('admin', 'khoi_tao_du_lieu_quan_huyen')}
        </span>

        <span btn-action="initialization-wards" class="btn btn-sm btn-warning">
            <i class="fa fa-map-marked-alt"></i>
            {__d('admin', 'khoi_tao_du_lieu_phuong_xa')}
        </span>
    </div>
</form>