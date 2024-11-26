<div id="config-cdn-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_thong_tin_cdn')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {assign var = config_cdn value = []}
                {if !empty($migrate.config_cdn.data)}
                    {assign var = config_cdn value = $migrate.config_cdn.data}
                {/if}

                <form id="config-cdn-form" action="{ADMIN_PATH}/transform-data/export/config-cdn" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'duong_dan_cdn_hien_tai')}
                            <span class="kt-font-danger">*</span>
                        </label>

                        <div class="input-group mb-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-link"></i>
                                </span>
                            </div>
                            <input name="url_cdn" value="{if !empty($config_cdn.url_cdn)}{$config_cdn.url_cdn}{/if}" class="form-control form-control-sm" type="text" placeholder="Ví dụ: https://cdn001.com">                            
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'duong_dan_cdn_moi')}
                            <span class="kt-font-danger">*</span>
                        </label>

                        <div class="input-group mb-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-link"></i>
                                </span>
                            </div>
                            <input name="url_cdn_new" value="{if !empty($config_cdn.url_cdn_new)}{$config_cdn.url_cdn_new}{/if}" class="form-control form-control-sm" type="text" placeholder="Ví dụ: https://cdn001.com">                            
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-config-cdn" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>