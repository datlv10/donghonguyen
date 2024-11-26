{assign var = page value = 1}
{assign var = pages value = 1}
{assign var = perpage value = 1}
{assign var = show_max_page value = 5}
{assign var = start value = 1}
{assign var = end value = $show_max_page}


{if !empty($pagination.page)}
    {assign var = page value = $pagination.page}
{/if}

{if !empty($pagination.pages)}
    {assign var = pages value = $pagination.pages}
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

{if !empty($pages) && $pages > 1}
<div class="kt-pagination  kt-pagination--brand">
    <ul class="kt-pagination__links">
        {if !empty($show_first)}
            <li class="kt-pagination__link--first">
                <a class="pages-link" data-page="1" href="javascript:;" title="{__d('template', 'trang_dau_tien')}">
                    <i class="fa fa-angle-double-left kt-font-brand"></i>
                </a>
            </li>
        {/if}

        {if $pages > $show_max_page && $page > 1}
            <li class="kt-pagination__link--next">
                <a class="pages-link" data-page="{$page - 1}" href="javascript:;" title="{__d('template', 'trang_truoc')}">
                    <i class="fa fa-angle-left kt-font-brand"></i>
                </a>
            </li>
        {/if}

        {if !empty($show_first)}
            <li class="disabled">
                <a class="pages-link"> ... </a>
            </li>
        {/if}

        {for $number = $start to $end}
            {if $page == $number}
                <li class="kt-pagination__link--active disabled">
                    <a class="pages-link" data-page="{$number}">
                        {$number}
                    </a>
                </li>
            {else}
                <li class="">
                    <a class="pages-link" data-page="{$number}" href="javascript:;">
                        {$number}
                    </a>
                </li>
            {/if}
        {/for}

        {if !empty($show_last) && $end < $pages}
            <li class=" disabled">
                <a class="pages-link"> ... </a>
            </li>
        {/if}

        {if $pages > $show_max_page && $page < $pages}
            <li class="kt-pagination__link--prev">
                <a class="pages-link" data-page="{$page + 1}" href="javascript:;" title="{__d('template', 'trang_tiep')}">
                    <i class="fa fa-angle-right kt-font-brand"></i>
                </a>
            </li>
        {/if}

        {if !empty($show_last)}
            <li class="kt-pagination__link--last">
                <a class="pages-link" data-page="{$pages}" href="javascript:;" title="{__d('template', 'trang_cuoi')}">
                    <i class="fa fa-angle-double-right kt-font-brand"></i>
                </a>
            </li>
        {/if}
    </ul>
</div>
{/if}