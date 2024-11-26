<div class="row">
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                {__d('admin', 'mau_block')} (color_block)
            </label>
            <div class="input-group">
                <span>
                    <input type="hidden" class="js-minicolors-select" value="{if !empty($config_layout.color_block)}{$config_layout.color_block}{/if}">
                </span>
                <input name="color_block" value="{if !empty($config_layout.color_block)}{$config_layout.color_block}{/if}" class="form-control form-control-sm js-minicolors-input" data-position="bottom left" type="text">
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                {__d('admin', 'mau_item')} (color_item)
            </label>
            <input name="color_item" value="{if !empty($config_layout.color_item)}{$config_layout.color_item}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                {__d('admin', 'mau_tieu_de')} (color_title)
            </label>
            <input name="color_title" value="{if !empty($config_layout.color_title)}{$config_layout.color_title}{/if}" class="form-control form-control-sm js-minicolors" data-position="bottom left" type="text" maxlength="100">
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="form-group">
            <label>
                Border Radius
            </label>
            <input name="border_radius" class="form-control form-control-sm number-input" type="text" value="{if !empty($config_layout.border_radius)}{$config_layout.border_radius}{/if}">
        </div>
    </div>
    
</div>