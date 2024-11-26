{assign var = url_list value = "{ADMIN_PATH}/mobile-app/dashboard"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-default">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/mobile-app/template/save-media" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'anh_template')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <span id="add-item" class="btn btn-sm btn-success">
                        <i class="fa fa-image"></i>
                        {__d('admin', 'them_anh')}
                    </span>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div id="wrap-item-config" class="row">                    
                    {if !empty($images)}
                        {foreach from = $images key = key item = item}
                            {assign var = image_url value = "{if !empty($item.image)}{$item.image}{/if}"}
                            {assign var = image_code value = "{if !empty($item.code)}{$item.code}{/if}"}
                            {assign var = image_source value = "{if !empty($item.source)}{$item.source}{/if}"}
                            {$this->element("../MobileTemplate/item_media", [
                                'image_url' => $image_url,
                                'image_code' => $image_code,
                                'image_source' => $image_source
                            ])}
                        {/foreach}
                    {else}
                        {$this->element("../MobileTemplate/item_media", [
                            'image_url' => '',
                            'image_code' => '',
                            'image_source' => ''
                        ])}
                    {/if}
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>