<div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>

<div class="form-group">
    <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
        <i class="fa fa-images"></i>
        {__d('admin', 'chon_anh_giao_dien')}
    </span>
    <input id="image_template" type="hidden" value="">
    
    {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

    <span data-src="{$url_select_image}" btn-select-media-block="cdn" action="copy" data-type="iframe" class="btn btn-sm btn-brand">
        <i class="fa fa-photo-video"></i>
        {__d('admin', 'chon_anh_tu_cdn')}
    </span>

    <span nh-btn="full-screen-html-editor" class="btn btn-sm btn-secondary float-right">
        <i class="fa fa-expand"></i>
        {__d('admin', 'toan_man_hinh')}
    </span>
</div>


<div class="form-group">
    <div id="editor-html" class="nh-editor"></div>
    <input id="html-content" name="config[html_content]" value="{if !empty($config.html_content)}{htmlentities($config.html_content)}{/if}" type="hidden">
</div>