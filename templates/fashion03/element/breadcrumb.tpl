{strip}
{assign var = last_url value = []}
{if !empty($list_url)}
    {assign var = last_url value = $list_url|@end}
{/if}
<div class="p-3 bg-white mb-3">
    <div class="container">
        <nav class="breadcrumbs-section">
            <a href="/">{__d('template', 'trang_chu')}</a>
            {if !empty($list_url)}
                {foreach from = $list_url item = item name = url_each}
                    {if $smarty.foreach.url_each.last}
                        <h1>
                            <span>  
                                {if !empty($item.title)}
                                    {$item.title}
                                {/if}
                            </span>
                        </h1>
                    {else}
                        <a href="/{if !empty($item.url)}{$item.url}{/if}">
                            {if !empty($item.title)}
                                {$item.title}
                            {/if}
                        </a>
                    {/if}
                {/foreach}
            {/if}
        </nav>
    </div>
</div>
{/strip}