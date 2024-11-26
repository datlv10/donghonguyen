{assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
    {TYPE} => $type, 
    {LANG} => $lang
])}

<div class="form-group">
    <label>
        {__d('admin', 'danh_muc')}
    </label>
    {$this->Form->select('', $list_categories, ['data-name' => 'category_id', 'empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($category_id)}{$category_id}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
</div>

