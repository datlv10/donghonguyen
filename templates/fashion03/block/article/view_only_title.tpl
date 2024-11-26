{assign var = is_slider value = false}
{if !empty($data_extend['slider'])}
    {assign var = is_slider value = true}
{/if}

{assign var = element value = "item_only_title"}
{if !empty($data_extend['element'])}
    {assign var = element value = {$data_extend['element']}}
{/if}

{assign var = col value = ""}
{if !empty($data_extend['col'])}
    {assign var = col value = $data_extend['col']}
{/if}

{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}

{strip}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
<div class="border-bottom">
    <span class="title_tinmoi2">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </span>
</div>
{/if}

{if !empty($data_block.data)}
    <div class="aksks mt-4">
        {foreach from = $data_block.data item = article}
            {if empty($article.url)}{continue}{/if}
            {$this->element("../block/{$block_type}/{$element}", [
                'article' => $article,
                'is_slider' => $is_slider,
                'col' => $col,
                'ignore_lazy' => $ignore_lazy
            ])}
        {/foreach}
    </div>
{else}
    <div class="mb-4">
        {__d('template', 'khong_co_du_lieu')}
    </div>
{/if}

{if !empty($block_config.has_pagination) && !empty($data_block[{PAGINATION}])}
    {$this->element('pagination_ajax', ['pagination' => $data_block[{PAGINATION}]])}
{/if}
{/strip}