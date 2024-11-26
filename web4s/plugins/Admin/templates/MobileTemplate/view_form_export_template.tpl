<form id="form-export-template" action="{ADMIN_PATH}/mobile-app/template/export" method="POST" autocomplete="off">
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'ten_giao_dien')}
                    <span class="kt-font-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-desktop"></i>
                        </span>
                    </div>
                    <input name="name" value="{if !empty($template.name)}{$template.name}{/if}" class="form-control form-control-sm" type="text">
                </div>
            </div>

            <div class="form-group">
                <label>
                    {__d('admin', 'ma_giao_dien')}
                    <span class="kt-font-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-qrcode"></i>
                        </span>
                    </div>
                    <input name="code" value="{if !empty($template.code)}{$template.code}{/if}" class="form-control form-control-sm" type="text" placeholder="electronic01, electronic02 ">
                </div>
            </div>

            <div class="form-group">
                <label>
                    {__d('admin', 'tac_gia')}
                    <span class="kt-font-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-user-tie"></i>
                        </span>
                    </div>
                    <input name="author" value="{if !empty($template.author)}{$template.author}{/if}" class="form-control form-control-sm" type="text">
                </div>
            </div>

            <div class="form-group">
                <label>
                    {__d('admin', 'phien_ban')}
                    <span class="kt-font-danger">*</span>
                </label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fab fa-gg-circle"></i>
                        </span>
                    </div>
                    <input name="version" value="{if !empty($template.version)}{$template.version}{/if}" class="form-control form-control-sm" type="text" placeholder="1.0, 2.1">
                </div>
            </div>

            <div class="form-group">
                <label>
                    {__d('admin', 'anh_dai_dien')} (.png)
                    <span class="kt-font-danger">*</span>
                </label>
                <div class="clearfix">
                    <div class="kt-avatar kt-avatar--outline kt-avatar--circle-">
                        {assign var = screen_shot value = ''}
                        {if !empty($template.code)}
                            {assign var = screen_shot value = "/templates/{$template.code}/screenshot.png"}
                        {/if}
                        <div class="kt-avatar__holder" style="background-image: url({$screen_shot});background-size: contain;background-position: 50% 50%;"></div>

                        <label id="btn-select-image" class="kt-avatar__upload">
                            <i class="fa fa-pen"></i>
                        </label>

                        <input id="input-image-avatar" name="image_avatar" value="" type="file" accept="image/x-png, image/jpeg" class="d-none" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-12">            
            <div class="form-group">
                <label>
                    {__d('admin', 'mo_ta')}
                </label>
                <textarea name="description" class="form-control form-control-sm" rows="9">{if !empty($template.description)}{$template.description}{/if}</textarea>
            </div>
        </div>

        <input name="template_id" value="{if !empty($template.id)}{$template.id}{/if}" type="hidden">
    </div>
</form>