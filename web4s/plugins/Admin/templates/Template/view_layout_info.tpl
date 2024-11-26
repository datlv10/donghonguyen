<input type="hidden" name="type" value="{LAYOUT}">
<input type="hidden" name="code" value="{if !empty($page_info.code)}{$page_info.code}{/if}">

<div class="form-group">
    <label>
        {__d('admin', 'ten_trang')}
        <span class="kt-font-danger">*</span>
    </label>

    <input name="name" value="{if !empty($page_info.name)}{$page_info.name}{/if}" type="text" class="form-control form-control-sm">
</div>