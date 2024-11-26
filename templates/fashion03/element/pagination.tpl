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
    <ul class="pagination">
        {if !empty($show_first)}
            <li class="page-item">
                <a class="page-link" href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($this->Url->build(), ['page' => 1])}" title="{__d('template', 'trang_dau_tien')}">
                    <i class="fa-light fa-angles-left"></i>
                </a>
            </li>
        {/if}

        {if $pages > $show_max_page && $page > 1}
            <li class="page-item">
                <a class="page-link" href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($this->Url->build(), ['page' => $page -1])}" title="{__d('template', 'trang_truoc')}">
                    <i class="fa-light fa-angle-left"></i>
                </a>
            </li>
        {/if}

        {if !empty($show_first)}
            <li class="page-item disabled">
                <a class="page-link"> ... </a>
            </li>
        {/if}

        {for $number = $start to $end}
            {if $page == $number}
                <li class="page-item active">
                    <span class="page-link">
                        {$number}
                    </span>
                </li>
            {else}
                <li class="page-item">
                    <a class="page-link" href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($this->Url->build(), ['page' => $number])}">
                        {$number}
                    </a>
                </li>
            {/if}
        {/for}

        {if !empty($show_last) && $end < $pages}
            <li class="page-item disabled">
                <a class="page-link"> ... </a>
            </li>
        {/if}

        {if $pages > $show_max_page && $page < $pages}
            <li class="page-item">
                <a class="page-link" href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($this->Url->build(), ['page' => $page + 1])}" title="{__d('template', 'trang_tiep')}">
                    <i class="fa-light fa-angle-right"></i>
                </a>
            </li>
        {/if}

        {if !empty($show_last)}
            <li class="page-item">
                <a class="page-link" href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($this->Url->build(), ['page' => $pages])}" title="{__d('template', 'trang_cuoi')}">
                    <i class="fa-light fa-angles-right"></i>
                </a>
            </li>
        {/if}
    </ul>
{/if}