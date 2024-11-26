<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fab fa-skyatlas"></i>
                            </span>
                        </div>
                        <input name="cdn_url" value="{if !empty($profile.cdn_url)}{$profile.cdn_url}{/if}" type="text" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
