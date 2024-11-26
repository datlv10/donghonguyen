<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/qr-code" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span class="btn btn-sm btn-success btn-preview">
                <i class="fa fa-eye"></i>
                {__d('admin', 'xem_truoc')}
            </span>

            <span class="btn btn-sm btn-brand btn-save">
                <i class="fa fa-check"></i>
                {__d('admin', 'luu_cau_hinh')}
            </span>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid mb-40">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="kt-portlet nh-portlet kt-portlet--height-fluid kt-portlet--responsive-mobile">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'cau_hinh')}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="kt-portlet__body">
                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'cau_hinh_chung')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group mb-0">
                                            <label class="col-form-label">
                                                {__d('admin', 'chieu_cao')} 
                                            </label>
                                            <input name="config[general][height]" value="{if !empty($config.general.height)}{$config.general.height}{/if}" class="form-control form-control-sm" type="number" max="2000" placeholder="620">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group  mb-0">
                                            <label class="col-form-label">
                                                {__d('admin', 'chieu_rong')}
                                            </label>
                                            <input name="config[general][width]" value="{if !empty($config.general.width)}{$config.general.width}{/if}" class="form-control form-control-sm" type="number" max="2000" placeholder="400">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group mb-0">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_nen')} 
                                            </label>
                                            <input name="config[general][background]" value="{if !empty($config.general.background)}{$config.general.background}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group  mb-0">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[general][color]" value="{if !empty($config.general.color)}{$config.general.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[general][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.general.font_size)}{$config.general.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                Font
                                            </label>
                                            {$this->Form->select('config[general][font]', $fonts, ['id' => 'font', 'empty' => null, 'default' => "{if !empty($config.general.font)}{$config.general.font}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'khoang_cach_cac_dong')}
                                            </label>
                                            <input name="config[general][margin_bottom]" value="{if !empty($config.general.margin_bottom)}{$config.general.margin_bottom}{/if}" class="form-control form-control-sm" type="number" max="100" placeholder="10">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'ma_qr')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">                                        
                                        <div class="row">
                                            <div class="col-lg-6 col-xs-12">
                                                <div class="form-group mb-0">
                                                    <label class="col-form-label">
                                                        {__d('admin', 'mau_nen')} 
                                                    </label>
                                                    <input name="config[qrcode][background]" value="{if !empty($config.qrcode.background)}{$config.qrcode.background}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-xs-12">
                                                <div class="form-group mb-0">
                                                    <label class="col-form-label">
                                                        {__d('admin', 'mau_sac')}
                                                    </label>
                                                    <input name="config[qrcode][color]" value="{if !empty($config.qrcode.color)}{$config.qrcode.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'logo')} 
                                            </label>
                                            <div class="clearfix">
                                                {assign var = bg_logo value = ''}
                                                {if !empty($config.qrcode.logo)}
                                                    {assign var = bg_logo value = "background-image: url('{CDN_URL}{$config.qrcode.logo}');background-size: contain;background-position: 50% 50%;"}
                                                {/if}
                                                
                                                {assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=logo&lang={LANGUAGE_ADMIN}"}

                                                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_logo)}kt-avatar--changed{/if}">
                                                    <a {if !empty($config.qrcode.logo)}href="{CDN_URL}{$config.qrcode.logo}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_logo}"></a>
                                                    <label class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-src="{$url_select_avatar}" data-type="iframe">
                                                        <i class="fa fa-pen"></i>
                                                    </label>
                                                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                                        <i class="fa fa-times"></i>
                                                    </span>

                                                    <input id="logo" name="config[qrcode][logo]" value="{if !empty($config.qrcode.logo)}{htmlentities($config.qrcode.logo)}{/if}" type="hidden" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'ten_ngan_hang')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group row mt-30 mb-0">
                                            <label class="col-3 col-form-label">
                                                {__d('admin', 'hien_thi')}
                                            </label>
                                            <div class="col-3">
                                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                    <label>
                                                        <input name="config[bank_name][display]" value="1" type="checkbox" {if !empty($config.bank_name.display)}checked="checked"{/if}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[bank_name][color]" value="{if !empty($config.bank_name.color)}{$config.bank_name.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[bank_name][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.bank_name.font_size)}{$config.bank_name.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'ten_tai_khoan')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group row mt-30 mb-0">
                                            <label class="col-3 col-form-label">
                                                {__d('admin', 'hien_thi')}
                                            </label>
                                            <div class="col-3">
                                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                    <label>
                                                        <input name="config[account_name][display]" value="1" type="checkbox" {if !empty($config.account_name.display)}checked="checked"{/if}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[account_name][color]" value="{if !empty($config.account_name.color)}{$config.account_name.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[account_name][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.account_name.font_size)}{$config.account_name.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}       
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'so_tai_khoan')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group row mt-30 mb-0">
                                            <label class="col-3 col-form-label">
                                                {__d('admin', 'hien_thi')}
                                            </label>
                                            <div class="col-3">
                                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                    <label>
                                                        <input name="config[account][display]" value="1" type="checkbox" {if !empty($config.account.display)}checked="checked"{/if}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[account][color]" value="{if !empty($config.account.color)}{$config.account.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[account][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.account.font_size)}{$config.account.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}       
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'so_tien')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group row mt-30 mb-0">
                                            <label class="col-3 col-form-label">
                                                {__d('admin', 'hien_thi')}
                                            </label>
                                            <div class="col-3">
                                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                    <label>
                                                        <input name="config[amount][display]" value="1" type="checkbox" {if !empty($config.amount.display)}checked="checked"{/if}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[amount][color]" value="{if !empty($config.amount.color)}{$config.amount.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[amount][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.amount.font_size)}{$config.amount.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-section mb-20">
                            <span class="kt-section__info font-weight-bold mb-5">
                                {__d('admin', 'mo_ta_giao_dich')}
                            </span>

                            <div class="kt-section__content kt-section__content--solid pl-10 pr-10 pt-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="form-group row mt-30 mb-0">
                                            <label class="col-3 col-form-label">
                                                {__d('admin', 'hien_thi')}
                                            </label>
                                            <div class="col-3">
                                                <span class="kt-switch kt-switch--outline kt-switch--icon kt-switch--success">
                                                    <label>
                                                        <input name="config[info][display]" value="1" type="checkbox" {if !empty($config.info.display)}checked="checked"{/if}>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'mau_chu')}
                                            </label>
                                            <input name="config[info][color]" value="{if !empty($config.info.color)}{$config.info.color}{/if}" class="form-control form-control-sm js-minicolors" type="text">
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label">
                                                {__d('admin', 'kich_thuoc')}
                                            </label>
                                            {$this->Form->select('config[info][font_size]', $fonts_size, ['id' => 'font-size', 'empty' => {__d('admin', 'mac_dinh')}, 'default' => "{if !empty($config.info.font_size)}{$config.info.font_size}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'xem_truoc')}
                            </h3>
                        </div>
                    </div>
                    
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <img id="image-preview" src="" class="img-qrcode-preview mt-20"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="type" value="{BANK_TRANSACTION}">
    </form>
</div>