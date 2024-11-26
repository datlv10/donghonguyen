<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body p-0">
            {assign var = src_iframe value = "{CDN_URL}/myfilemanager/?cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}"}
            <iframe id="media-iframe" src="{$src_iframe}" frameborder="0" width="100%" height="100%" style="min-height: 600px;"></iframe>
        </div>
    </div>
</div>