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
                <div class="col-lg-6 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'chon_anh')}
                                </label>

                                {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_item"}

                                <div class="clearfix">
                                    <span btn-select-media-block="cdn" action="preview" data-src="{$url_select_image}&field_id=image_item" data-type="iframe" class="btn btn-sm btn-brand mb-10">
                                        <i class="fa fa-photo-video"></i>
                                        {__d('admin', 'chon_anh_tu_cdn')}
                                    </span>
                                </div>

                                <div class="clearfix">
                                    <span btn-select-media-block="template" action="preview" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_item" data-type="iframe" class="btn btn-sm btn-success">
                                        <i class="fa fa-images"></i>
                                        {__d('admin', 'chon_anh_giao_dien')}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            {assign var = source value = ''}
                            {if !empty($config_data.source) && !empty($config_data.image)}
                                {assign var = source value = $config_data.source}
                            {/if}

                            {assign var = image_url value = ''}
                            {if !empty($config_data.image) && $source == 'cdn'}
                                {assign var = image_url value = "background-image: url('{CDN_URL}{$config_data.image}');"}
                            {/if}

                            {if !empty($config_data.image) && $source == 'template'}
                                {assign var = image_url value = "background-image: url('{$config_data.image}');background-size: contain;background-position: 50% 50%;"}
                            {/if}           

                            <div block-preview-image="image_item" class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($config_data.image)}kt-avatar--changed{/if} mb-10">
                                <div class="kt-avatar__holder" style="{$image_url}"></div>
                                <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                    <i class="fa fa-times"></i>
                                </span>

                                <input id="image_item" name="image" value="{if !empty($config_data.image)}{$config_data.image}{/if}" class="input-select-image" type="hidden" />
                                <input block-image-source="image_item" name="source" value="{$source}" class="input-image-source" type="hidden" />
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
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
                            <input name="action" value="{if !empty($config_data.action)}{htmlentities($config_data.action)}{/if}" class="form-control form-control-sm" type="text">

                        </div>
                        <span class="form-text text-muted">{literal}{"page_type": "product", "product_id" : 10, "params": "{\"status\":\"featured\", \"limit\": 1}"}{/literal}</span>
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

