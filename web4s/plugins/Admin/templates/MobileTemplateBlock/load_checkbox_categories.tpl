{assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForCheckboxList([
    {TYPE} => $type, 
    {LANG} => $lang
])}
<div class="form-group">
    <label>
        {__d('admin', 'chon_danh_muc')}
    </label>
    {if !empty($list_categories)}
        {foreach from = $list_categories key = category_id item = category}
            {assign var = level value = {$category.level|intval}}
            <div class="clearfix">
                {if !empty($level)}
                    {for $i = 1 to $level - 1}
                        <label class="mr-20">&nbsp;</label>
                    {/for}
                {/if}                                            
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                    <input name="" data-name="id_categories" data-mutiple="true" value="{$category_id}" {if !empty($categories_selected) && $category_id|in_array:$categories_selected}checked{/if} type="checkbox"> 
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