<div data-repeater-item class="row">
    <div class="col-xl-3 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'ma_truong')}
                <span class="kt-font-danger">*</span>
            </label>
            <input name="code" value="{if !empty($field.code)}{$field.code}{/if}" class="form-control form-control-sm required" type="text" message-required="{__d('admin', 'vui_long_nhap_thong_tin')}">
        </div>
    </div>

    <div class="col-xl-3 col-lg-3">
        <div class="form-group">
            <label>
                {__d('admin', 'ten_truong')}
                <span class="kt-font-danger">*</span>
            </label>
            <input name="label" value="{if !empty($field.label)}{$field.label}{/if}" class="form-control form-control-sm required" type="text" message-required="{__d('admin', 'vui_long_nhap_thong_tin')}">
        </div>
    </div>

    <div class="col-xl-2 col-lg-2">
        <div class="form-group">
            <label></label>
            <div class="clearfix">
                <span data-repeater-delete class="btn btn-sm btn-danger mt-5">
                    <i class="la la-trash-o"></i>
                    {__d('admin', 'xoa')}
                </span>
            </div>
        </div>
    </div>
</div>