<div class="kt-form kt-form--label-right">
    <div class="kt-form__body">
        <div class="kt-section kt-section--first">
            <div class="kt-section__body">
                <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h3 class="kt-section__title kt-section__title-sm">
                            {__d('admin', 'thong_tin_tai_khoan')}:
                        </h3>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'ten_dang_nhap')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="username" value="{if !empty($user.username)}{$user.username}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                    </div>
                </div>

                {if empty($user)}
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">
                            {__d('admin', 'mat_khau')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <div class="col-lg-9 col-xl-6">
                            <input name="password" id="password" value="" type="password" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">
                            {__d('admin', 'xac_nhan_mat_khau')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <div class="col-lg-9 col-xl-6">
                            <input name="verify_password" type="password" class="form-control form-control-sm" value="">
                        </div>
                    </div>
                {/if}

                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'email')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="email" value="{if !empty($user.email)}{$user.email}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'nhom_quyen')}
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        {assign var = list_role value = $this->RoleAdmin->getList()}
                        {$this->Form->select('role_id', $list_role, ['name'=>'role_id', 'empty' => "{__d('admin', 'lua_chon')} ...", 'default' => "{if !empty($user.role_id)}{$user.role_id}{/if}",'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
        <div class="kt-section kt-section--first">
            <div class="kt-section__body">
                <div class="row">
                    <label class="col-xl-3"></label>
                    <div class="col-lg-9 col-xl-6">
                        <h3 class="kt-section__title kt-section__title-sm">
                            {__d('admin', 'thong_tin_ca_nhan')}:
                        </h3>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'anh_dai_dien')}:
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        {assign var = bg_avatar value = ''}
                        {if !empty($user.image_avatar)}
                            {assign var = bg_avatar value = "background-image: url('{CDN_URL}{$user.image_avatar}');background-size: contain;background-position: 50% 50%;"}
                        {/if}

                        {assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_avatar"}

                        <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_avatar)}kt-avatar--changed{/if}">
                            <div class="kt-avatar__holder" style="{$bg_avatar}"></div>
                            <label data-src="{$url_select_avatar}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                <i class="fa fa-pen"></i>
                            </label>
                            <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>

                            <input id="image_avatar" name="image_avatar" value="{if !empty($user.image_avatar)}{htmlentities($user.image_avatar)}{/if}" type="hidden" />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'ho_va_ten')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="full_name" class="form-control" type="text" value="{if !empty($user.full_name)}{$user.full_name}{/if}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'so_dien_thoai')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="phone" class="form-control" type="text" value="{if !empty($user.phone)}{$user.phone}{/if}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'dia_chi')}
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="address" class="form-control" type="text" value="{if !empty($user.address)}{$user.address}{/if}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">
                        {__d('admin', 'ngay_sinh')}
                    </label>
                    <div class="col-lg-9 col-xl-6">
                        <input name="birthday" class="form-control" type="text" value="{if !empty($user.birthday)}{$this->UtilitiesAdmin->convertIntgerToDateString($user.birthday)}{/if}">
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>
        <div class="kt-form__actions">
            <div class="row">
                <div class="col-xl-3"></div>
                <div class="col-lg-9 col-xl-6">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'cap_nhat')}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
