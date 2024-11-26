{if !empty($categories)}
    {foreach from = $categories item = item}
        <div class="kt-todo__item align-items-center" data-id="{if !empty($item.id)}{$item.id}{/if}">
            <div class="kt-todo__info">
                <div class="kt-todo__actions">
                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                        <input type="radio" name="category_id" value={if !empty($item.id)}{$item.id}{/if}>
                        <span></span>
                    </label>
                </div>
            </div>
            <div class="kt-todo__details" data-toggle="view">
                <div class="kt-todo__message">
                    <span class="kt-todo__subject">
                        {if (!empty($item.CategoriesContent.name))}
                            {$item.CategoriesContent.name}
                        {/if}
                    </span>
                </div>
            </div>
        </div>
    {/foreach}
    <div class="kt-todo__foot">
        <div class="kt-todo__toolbar">
            <div class="kt-todo__controls">
                {$this->element('Admin.page/pagination')}
            </div>
        </div>
    </div>
{/if}