<div class="row">
    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'so_binh_luan_hien_thi')}
            </label>            
            <input name="config[{NUMBER_RECORD}]" value="{if !empty($config[{NUMBER_RECORD}])}{$config[{NUMBER_RECORD}]}{else}20{/if}" class="form-control form-control-sm" type="number" max="200">
            <span class="form-text text-muted">
                {__d('admin', 'so_luong_binh_luan_hien_thi_trong_1_trang')}
            </span>
        </div>
    </div>

    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'sap_xep_theo')}
            </label>
            {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfComment(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
        </div>
    </div>

    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'yeu_cau_dang_nhap')}
            </label>
            
            <div class="kt-radio-inline mt-5">
                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                    <input type="radio" name="config[login_required]" value="0" {if empty($config.login_required) || !isset($config.login_required)}checked{/if}> 
                        {__d('admin', 'khong')}
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="config[login_required]" value="1" {if !empty($config.login_required)}checked{/if}> 
                        {__d('admin', 'co')}
                    <span></span>
                </label>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'cho_quan_tri_phe_duyet')}
            </label>
            
            <div class="kt-radio-inline mt-5">
                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                    <input type="radio" name="config[awaiting_approval]" value="0" {if empty($config.awaiting_approval)}checked{/if}> 
                        {__d('admin', 'khong')}
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="config[awaiting_approval]" value="1" {if !empty($config.awaiting_approval) || !isset($config.awaiting_approval)}checked{/if}> 
                        {__d('admin', 'co')}
                    <span></span>
                </label>
            </div>
        </div>
    </div>
</div>
