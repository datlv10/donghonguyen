{strip}
{assign var = ignore_lazy value = false}
{if !empty($data_extend.ignore_lazy)}
    {assign var = ignore_lazy value = $data_extend.ignore_lazy}
{/if}
{if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
    <div class="title_tinmoi">
        {$this->Block->getLocale('tieu_de', $data_extend)}
    </div>
{/if}
<div class="view-small">
	{if !empty($data_block.data)}
		{foreach from = $data_block.data item = article key = k_article}
			{$this->element("../block/{$block_type}/item_small", [
				'article' => $article,
				'ignore_lazy' => $ignore_lazy
			])}
		{/foreach}
    {else}
        <div class="mb-4">
	        {__d('template', 'khong_co_du_lieu')}
	    </div>
	{/if}
</div>
{/strip}