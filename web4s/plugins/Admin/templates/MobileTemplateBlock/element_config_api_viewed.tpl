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
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tieu_de_hien_thi')}
                        </label>
                        <input name="title" value="{if !empty($config_layout.title)}{$config_layout.title}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_san_pham_hien_thi')}
                        </label>            
                        <input name="{NUMBER_RECORD}" value="{if !empty($config_data[{NUMBER_RECORD}])}{$config_data[{NUMBER_RECORD}]}{/if}" class="form-control form-control-sm" type="number">
                        <span class="form-text text-muted">
                            {__d('admin', 'so_luong_ban_ghi_se_hien_thi_trong_block')}
                        </span>
                    </div>
                </div>  
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Mobile)</b>
                        </label>            
                        <input name="number_on_line" value="{if !empty($config_layout.number_on_line)}{$config_layout.number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Ipad/Tablet)</b>
                        </label>            
                        <input name="ipad_number_on_line" value="{if !empty($config_layout.ipad_number_on_line)}{$config_layout.ipad_number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>              
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ti_le_chieu_cao_anh')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-arrows-alt-v"></i>
                                </span>
                            </div>
                            <input name="image_height" value="{if !empty($config_layout.image_height)}{$config_layout.image_height}{/if}" class="form-control form-control-sm" type="number">
                        </div>            
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ti_le_chieu_rong_anh')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-arrows-alt-h"></i>
                                </span>
                            </div>
                            <input name="image_width" value="{if !empty($config_layout.image_width)}{$config_layout.image_width}{/if}" class="form-control form-control-sm" type="number">
                        </div>            
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'dinh_dang')}
                        </label>        
                        {$this->Form->select('format', $this->MobileTemplateAdmin->listFormatItem(), ['empty' => null, 'default' => "{if !empty($config_layout.format)}{$config_layout.format}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            Element
                        </label>        
                        {$this->Form->select('element', $this->MobileTemplateAdmin->listElementProduct(), ['empty' => null, 'default' => "{if !empty($config_layout.element)}{$config_layout.element}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
            </div>
            {$this->element("../MobileTemplateBlock/config_color_block", ['config_layout' => $config_layout])}
            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>
            {$this->element("../MobileTemplateBlock/config_block_spacing", ['config_layout' => $config_layout])}
            {$this->element("../MobileTemplateBlock/config_item_spacing", ['config_layout' => $config_layout])}

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


