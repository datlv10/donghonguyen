{assign var = categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
    {TYPE} => $type_category, 
    {LANG} => $lang
])}

{if !isset($categories_selected)}
    {assign var = categories_selected value = []}
{/if}

{if !empty($categories)}    
    {foreach from = $categories key = category_id item = category_name}
        {assign var = active value = ''}

        <div class="kt-todo__item kt-separator kt-separator--dashed mb-0 mt-0 pb-10 pt-10 h-auto" data-id="{$category_id}">
            {assign var = active value = "{if !empty($categories_selected) && in_array($category_id, $categories_selected)}kt-font-info{/if}"}
            <div class="kt-todo__info">
                <div class="kt-todo__actions">
                    <label class="kt-radio kt-radio--tick kt-radio--brand mb-0 {$active}">
                        <input nh-category-select="attribute" type="radio" name="category_id" nh-type="{if (!empty($type_attribute))}{$type_attribute}{/if}" value="{$category_id}">
                        <span></span>

                        {$category_name}
                    </label>
                </div>
            </div>
        </div>
    {/foreach}
{/if}