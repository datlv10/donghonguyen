<div class="form-group mb-15">
    <label>
        <strong>{__d('admin', 'khoang_cach_item')}</strong>
    </label>
</div>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Gridview Padding Top
            </label>
            <input name="gridview_padding_top" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.gridview_padding_top)}{$config_layout.gridview_padding_top}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Item Line Spacing
            </label>
            <input name="item_line_spacing" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.item_line_spacing)}{$config_layout.item_line_spacing}{/if}">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Margin Left Right
            </label>
            <input name="margin_left_right_item" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.margin_left_right_item)}{$config_layout.margin_left_right_item}{/if}">
        </div>
    </div>
</div>