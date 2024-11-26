<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'sap_xep_theo')}
            </label>
            <div class="row">
                <div class="col-6">
                    {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfCategory(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>

                <div class="col-6">
                    {assign var = sort_type value = ''}
                    {if !empty($config[{SORT_TYPE}])}
                        {assign var = sort_type value = $config[{SORT_TYPE}]}
                    {/if}
                    <select name="config[{SORT_TYPE}]" class="form-control form-control-sm kt-selectpicker">
                        <option value="{DESC}" {if $sort_type == {DESC}}selected="true"{/if}>
                            {__d('admin', 'giam_dan')}
                        </option>

                        <option value="{ASC}" {if $sort_type == {ASC}}selected="true"{/if}>
                            {__d('admin', 'tang_dan')}
                        </option>                        
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

{assign var = categories_selected value = []}
{if !empty($config.data_ids)}
    {assign var = categories_selected value = $config.data_ids}
{/if}

<div class="form-group">
    <label>
        {__d('admin', 'danh_muc_bai_viet')}
    </label>
    {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForCheckboxList([
        {TYPE} => {ARTICLE}, 
        {LANG} => $lang
    ])}

    {if !empty($list_categories)}
        {foreach from = $list_categories key = category_id item = category}
            {assign var = level value = {$category.level|intval}}
            <div class="clearfix mt-10">
                {if !empty($level)}
                    {for $i = 1 to $level - 1}
                        <label class="mr-20">&nbsp;</label>
                    {/for}
                {/if}                                            
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                    <input name="config[data_ids][]" value="{$category_id}" {if !empty($categories_selected) && $category_id|in_array:$categories_selected}checked{/if} type="checkbox"> 
                    {if !empty($category.name)}
                        {$category.name}
                    {/if}
                    <span></span>
                </label>
            </div> 
        {/foreach}
    {else}
        <i class="mb-10">{__d('admin', 'chua_co_danh_muc_duoc_kich_hoat')}</i>
    {/if}
</div>