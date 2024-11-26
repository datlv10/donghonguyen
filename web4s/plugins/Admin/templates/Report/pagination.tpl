{assign var = page value = 1}
{assign var = pages value = 1}
{assign var = perpage value = 10}
{assign var = show_max_page value = 5}
{assign var = start value = 1}
{assign var = end value = $show_max_page}

{if !empty($pagination.page)}
    {assign var = page value = $pagination.page}
{/if}

{if !empty($pagination.pages)}
    {assign var = pages value = $pagination.pages}
{/if}    

{if !empty($pagination.perpage)}
    {assign var = perpage value = $pagination.perpage}
{/if}      

{if $page >= $show_max_page}
    {assign var = end value = $page + 1}
{/if}

{if $end > $pages}
    {assign var = end value = $pages}
{/if}

{assign var = start value = $end - $show_max_page + 1}
{if $start < 1}
    {assign var = start value = 1}
{/if}

{if $pages > $show_max_page && $page >= $show_max_page}
    {assign var = show_first value = true}
{/if}

{if $pages > $show_max_page && $pages > $page}
    {assign var = show_last value = true}
{/if}

<div class="kt-pagination  kt-pagination--brand">
    <ul class="kt-pagination__links">
        {if !empty($pages) && $pages > 1}
            {if !empty($show_first)}
                <li class="kt-pagination__link--first">
                    <a href="javascript:;" nh-page-redirect="1">
                        <i class="fa fa-angle-double-left kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if $pages > $show_max_page && $page > 1}
                <li class="kt-pagination__link--next">
                    <a href="javascript:;" nh-page-redirect="{$page - 1}">
                        <i class="fa fa-angle-left kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if !empty($show_first)}
                <li class="kt-datatable__pager-link--disabled" disabled="disabled">
                    <a> ... </a>
                </li>
            {/if}

            {for $number = $start to $end}
                {if $page == $number}
                    <li class="kt-pagination__link--active">
                        <a>{$number}</a>
                    </li>
                {else}
                    <li>
                        <a href="javascript:;" nh-page-redirect="{$number}">
                            {$number}
                        </a>
                    </li>
                {/if}
            {/for}

            {if !empty($show_last) && $end < $pages}
                <li class="kt-datatable__pager-link--disabled" disabled="disabled">
                    <a> ... </a>
                </li>
            {/if}

            {if $pages > $show_max_page && $page < $pages}
                <li class="kt-pagination__link--prev">
                    <a href="javascript:;" nh-page-redirect="{$page + 1}">
                        <i class="fa fa-angle-right kt-font-brand"></i>
                    </a>
                </li>
            {/if}

            {if !empty($show_last)}
                <li class="kt-pagination__link--last">
                    <a href="javascript:;" nh-page-redirect="{$pages}">
                        <i class="fa fa-angle-double-right kt-font-brand"></i>
                    </a>
                </li>
            {/if}
        {/if}
    </ul>
    <div class="kt-pagination__toolbar">
        <select id="number_record" class="form-control kt-font-brand" style="width: 60px;">
            <option value="50" {if $perpage eq '50'}selected{/if}>50</option>
            <option value="100" {if $perpage eq '100'}selected{/if}>100</option>
        </select>
    </div>
</div>