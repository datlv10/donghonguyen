{if !empty($comment)}
    {foreach from = $comment item = item}
        <div class="kt-todo__item align-items-center" data-id="{if !empty($item.id)}{$item.id}{/if}">
            <div class="kt-todo__info">
                <div class="kt-todo__actions">
                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                        <input type="checkbox" value={if !empty($item.id)}{$item.id}{/if}>
                        <span></span>
                    </label>
                </div>
            </div>
            <div class="kt-todo__details" data-toggle="view">
                <div class="kt-todo__message">
                    {if (!empty($item.is_admin))}
                        <span class="kt-badge kt-badge--warning kt-badge--inline">{__d('admin', 'quan_tri')}</span>
                    {/if}
                    <span class="kt-todo__subject">
                        {if !empty($item.parent_id)}
                            <i class="flaticon-reply"></i>
                        {/if}
                        {if !empty($item.content)}
                            {$item.content|truncate:150:" ..."}
                        {/if}
                        {if !empty($item.images)}
                            <i class="la la-image"></i>
                        {/if}
                    </span>
                    {if !empty($item.time)}
                        <small>{$item.time}</small>
                    {/if}
                </div>
            </div>
            {if !empty($item.type_comment eq 'rating')}
                <div class="star-rating">
                    <span style="width:{math equation="x * y" x=$item.rating|intval y=20}%"></span>
                </div>
            {/if}
            <div class="kt-todo__info kt-margin-l-10">
                {if isset($item.status) && $item.status eq 0}
                    <span class="kt-font-danger mr-10">
                        <i class="flaticon2-cross"></i>
                    </span>
                {/if}
                {if isset($item.status) && $item.status eq 1}
                    <span class="kt-font-success mr-10">
                        <i class="flaticon2-check-mark"></i>
                    </span>
                {/if}
                {if isset($item.status) && $item.status eq 2}
                    <span class="kt-font-warning mr-10">
                        <i class="flaticon2-warning"></i>
                    </span>
                {/if}
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