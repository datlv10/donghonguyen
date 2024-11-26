<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-database mr-5"></i>
                {__d('admin', 'cau_hinh_du_lieu')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form action="{ADMIN_PATH}/mobile-app/block/save-data-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'noi_dung')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-file-alt"></i>
                                </span>
                            </div>
                            <input name="text" value="{if !empty($config_data.text)}{$config_data.text}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'chuyen_huong')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-external-link-alt"></i>
                                </span>
                            </div>
                            <input name="action" value="{if !empty($config_data.action)}{htmlentities($item.action)}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-tablet-alt mr-5"></i>
                {__d('admin', 'cau_hinh_giao_dien')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form action="{ADMIN_PATH}/mobile-app/block/save-layout-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'font_size')}
                        </label>
                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-text-height"></i>
                                </span>
                            </div>
                            <input name="font_size" value="{if !empty($config_layout.font_size)}{$config_layout.font_size}{/if}" class="form-control form-control-sm" type="number">
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'can_chinh')}
                        </label>
                        {$this->Form->select('align', $this->MobileTemplateAdmin->listAlign(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config_layout.align)}{$config_layout.align}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'mau_chu')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                            </div>
                            <input name="text_color" value="{if !empty($config_layout.text_color)}{$config_layout.text_color}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom right" type="text">
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'mau_nen')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-palette"></i>
                                </span>
                            </div>
                            <input name="background_color" value="{if !empty($config_layout.background_color)}{$config_layout.background_color}{/if}" class="form-control form-control-sm js-minicolors more-input-group" data-position="bottom right" type="text">
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'chieu_cao_block')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-arrows-alt-v"></i>
                                </span>
                            </div>
                            <input name="block_height" value="{if !empty($config_layout.block_height)}{$config_layout.block_height}{/if}" class="form-control form-control-sm" type="number">
                        </div>            
                    </div>
                </div>
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
