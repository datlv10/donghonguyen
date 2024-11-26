{assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
    {TYPE} => {$type_category}, 
    {LANG} => {$language}
])}

{if !empty($list_categories)}
    <div class="form-group row">
        <label class="col-xl-2 col-lg-3 col-form-label">
            {__d('admin', 'danh_muc_ap_dung')}
        </label>

        <div class="col-lg-8">            
            {$this->Form->select('category_id', $list_categories, ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($category_id)}{$category_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}

            <span class="form-text text-muted">
                {__d('admin', 'tat_ca_ban_ghi_hoac_danh_muc_thuoc_danh_muc_da_chon_se_ap_dung_cau_hinh_cua_trang_nay')}
            </span>
        </div>
    </div>
{/if}