<div class="form-group mb-15">
    <label>
        <strong>{__d('admin', 'khoang_cach_block')}</strong>
    </label>
</div>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Padding Top
            </label>
            <input name="padding_top_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.padding_top_block)}{$config_layout.padding_top_block}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Padding Bottom
            </label>
            <input name="padding_bottom_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.padding_bottom_block)}{$config_layout.padding_bottom_block}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Padding Left Right
            </label>
            <input name="padding_left_right_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.padding_left_right_block)}{$config_layout.padding_left_right_block}{/if}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Margin Top
            </label>
            <input name="margin_top_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.margin_top_block)}{$config_layout.margin_top_block}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Margin Bottom
            </label>
            <input name="margin_bottom_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.margin_bottom_block)}{$config_layout.margin_bottom_block}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Margin Left Right
            </label>
            <input name="margin_left_right_block" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.margin_left_right_block)}{$config_layout.margin_left_right_block}{/if}">
        </div>
    </div>
</div>