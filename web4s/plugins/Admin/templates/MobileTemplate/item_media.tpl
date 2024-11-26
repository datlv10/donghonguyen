<div class="col-lg-4 col-sm-6 col-xs-12 wrap-item">
    <div class="kt-portlet kt-portlet--mobile mb-10 nh-template-portlet">
        <div class="kt-portlet__head p-5">
            <div class="kt-portlet__head-label ml-5">
                <h3 class="kt-portlet__head-title header-item">
                    {$image_code}
                </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-group">                        
                    <span class="btn btn-sm btn-icon btn-danger btn-icon-md m-0 btn-delete-item">
                        <i class="la la-trash-o"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body p-10">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'chon_anh')}
                        </label>

                        {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_item"}

                        <div class="clearfix">
                            <span btn-select-media-block="cdn" action="preview" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand mb-10">
                                <i class="fa fa-images"></i>
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

                    {assign var = image_background value = ''}
                    {if !empty($image_url) && $image_source == 'cdn'}
                        {$image_background = "background-image: url('{CDN_URL}{$image_url}');"}
                    {/if}

                    {if !empty($image_url) && $image_source == 'template'}
                        {$image_background = "background-image: url('{$image_url}');background-size: contain;background-position: 50% 50%;"}
                    {/if}         

                    <div block-preview-image="image_item" class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($image_url)}kt-avatar--changed{/if} mb-10">
                        <div class="kt-avatar__holder" style="{$image_background}"></div>
                        <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                            <i class="fa fa-times"></i>
                        </span>

                        <input id="image_item" name="" data-name="image" value="{if !empty($image_url)}{htmlentities($image_url)}{/if}" class="input-select-image" type="hidden" />
                        <input block-image-source="image_item" data-name="source" name="" value="{$image_source}" class="input-image-source" type="hidden" />

                        <input data-name="code" name="" value="{$image_code}" class="input-image-source" type="hidden" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>