{assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=image_avatar&lang={LANGUAGE_ADMIN}"}

<div class="row">
    <div class="col-sm-4 col-12">
        <div class="form-group">
            <label>
                Họ và tên
                <span class="kt-font-danger">*</span>
            </label>
            <input name="full_name" value="{if !empty($genealogy.full_name)}{$genealogy.full_name|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>

    <div class="col-sm-4 col-12">
        <div class="form-group">
            <label label-self-name>
                {if !empty($genealogy.relationship) && $genealogy.relationship == 2}
                    Tên hiệu
                {else}
                    Tên tự
                {/if}
            </label>
            <input name="self_name" value="{if !empty($genealogy.self_name)}{$genealogy.self_name|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>

    <div class="col-sm-4 col-12">
        <div class="form-group">
            <label>
                Trình độ học vấn
            </label>
            {$this->Form->select('education_level', $education_level, ['id' => 'education_level', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($genealogy.education_level)}{$genealogy.education_level}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-placeholder' => "Trình độ học vấn"])}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Tình trạng
            </label>
            <div class="kt-radio-inline">
                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="status" value="0" {if empty($genealogy.status)}checked{/if}> Đã mất
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="status" value="1" {if !empty($genealogy.status)}checked{/if}> Còn sống
                    <span></span>
                </label>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>Giới tính</label>
            <div class="kt-radio-inline">
                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="sex" value="male" {if !empty($genealogy.sex) && $genealogy.sex == 'male'}checked{/if}> Nam
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="sex" value="female" {if !empty($genealogy.sex) && $genealogy.sex == 'female'}checked{/if}> Nữ
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="sex" value="other" {if !empty($genealogy.sex) && $genealogy.sex == 'other'}checked{/if}> Khác
                    <span></span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>Quan hệ</label>
            <div class="kt-radio-inline">
                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="relationship" value="0" {if empty($genealogy.relationship)}checked{/if}> Không
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="relationship" value="1" data-title="Chồng" {if !empty($genealogy.relationship) && $genealogy.relationship == 1}checked{/if}> Chồng
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="relationship" value="2" data-title="Vợ" {if !empty($genealogy.relationship) && $genealogy.relationship == 2}checked{/if}> Vợ
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="relationship" value="3" data-title="Con" {if !empty($genealogy.relationship) && $genealogy.relationship == 3}checked{/if}> Con
                    <span></span>
                </label>
            </div>
        </div>
    </div>  

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Thuộc phả đồ
            </label>
            <div class="kt-radio-inline">
                <label class="kt-radio kt-radio--tick kt-radio--success">
                    <input type="radio" name="genealogical" value="0" {if isset($genealogy.genealogical) && $genealogy.genealogical == 0}checked{/if}> Không
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="genealogical" value="1" {if (isset($genealogy.genealogical) && $genealogy.genealogical == 1) || empty($genealogy)}checked{/if}> Có
                    <span></span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row {if empty($genealogy.relationship)}d-none{/if}" wrap-relationship>
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label label-info>
               Thông tin quan hệ
            </label>
            {$this->Form->select('relationship_info', $genealogy_select, ['id' => 'relationship_info', 'empty' => "-- Thông tin quan hệ --", 'default' => "{if !empty($genealogy.relationship_info)}{$genealogy.relationship_info}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '10', 'data-live-search' => true])}
        </div>
    </div>

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label label-position>
               Thứ
            </label>
            <input name="relationship_position" value="{if !empty($genealogy.relationship_position)}{$genealogy.relationship_position}{/if}" class="form-control form-control-sm number-input" type="text" maxlength="10">
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        Nguồn gốc
    </label>
    <input name="description" value="{if !empty($genealogy.description)}{$genealogy.description|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
</div>

<div class="row">
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Năm sinh
            </label>
            <input name="birthday" value="{if !empty($genealogy.birthday)}{$this->Utilities->convertIntgerToDateString($genealogy.birthday)}{/if}" class="form-control form-control-sm datepicker" type="text" maxlength="255">
        </div>
    </div>

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Năm mất
            </label>
            <input name="nam_mat" value="{if !empty($genealogy.nam_mat)}{$this->Utilities->convertIntgerToDateString($genealogy.nam_mat)}{/if}" class="form-control form-control-sm datepicker" type="text" maxlength="255">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Năm sinh chi tiết
            </label>
            <input name="year_of_birth" value="{if !empty($genealogy.year_of_birth)}{$genealogy.year_of_birth|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                Năm mất chi tiết
            </label>
            <input name="year_of_death" value="{if !empty($genealogy.year_of_death)}{$genealogy.year_of_death|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        Nơi an táng
    </label>
    <input name="burial" value="{if !empty($genealogy.burial)}{$genealogy.burial|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
</div>

<div class="row">
    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'tinh_thanh_thanh_pho')}
            </label>
            {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => "{if !empty($genealogy.city_id)}{$genealogy.city_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5'])}
        </div>
    </div>

    <div class="col-sm-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'quan_huyen')}
            </label>
            {$this->Form->select('district_id', [], ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => "{if !empty($genealogy.district_id)}{$genealogy.district_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5'])}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-sm-4 col-12">
        <div class="form-group">
            <label>
                Ảnh đại diện
            </label>

            <div class="clearfix">
                {assign var = bg_avatar value = ''}
                {if !empty($genealogy.image_avatar)}
                    {assign var = bg_avatar value = "background-image: url('{CDN_URL}{$genealogy.image_avatar}');background-size: contain;background-position: 50% 50%;"}
                {/if}

                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_avatar)}kt-avatar--changed{/if}">
                    <a {if !empty($genealogy.image_avatar)}href="{CDN_URL}{$genealogy.image_avatar}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_avatar}"></a>
                    <label class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-src="{$url_select_avatar}" data-type="iframe">
                        <i class="fa fa-pen"></i>
                    </label>
                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                        <i class="fa fa-times"></i>
                    </span>

                    <input id="image_avatar" name="image_avatar" value="{if !empty($genealogy.image_avatar)}{htmlentities($genealogy.image_avatar)}{/if}" type="hidden" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-sm-8 col-12">
        <div class="form-group">
            <label>
                Tiểu sử, Sự nghiệp, Ghi chú
            </label>
            <div class="clearfix">
                <textarea name="content" id="content" class="mce-editor-simple">{if !empty($genealogy.content)}{$genealogy.content}{/if}</textarea>
            </div>
        </div>
    </div>
</div>

<input type="hidden" note-id-old value="">
<input type="hidden" note-id-new value="">