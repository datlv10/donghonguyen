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

{if !empty($pages) && $pages > 1 && $page < $pages}
    <ul nh-pagination="load_more" class="list-unstyled text-center mt-4">
        <span nh-page="{$page + 1}" class="page-item">
            <a class="page-link btn btn-outline-dark" href="javascript:;" title="{__d('template', 'xem_them')}">
                {__d('template', 'xem_them')}
            </a>
        </span>
    </ul>
{/if}