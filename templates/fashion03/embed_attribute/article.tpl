{if !empty($value)}
	{assign var = article_id value = $value|json_decode:true}
	{assign var = data_article value = $this->Article->getArticles([
        'get_categories' => true,
        {FILTER} => [
            'ids' => $article_id
        ],
        {SORT} => [
            {FIELD} => 'name',
            {SORT} => ASC
        ]
    ], {LANGUAGE})}

    {if !empty($data_article)}
    	<div class="my-3">
		    <div class="row">
		        {foreach from = $data_article item = article}
		            {if empty($article.url)}{continue}{/if}
		            {$this->element("../block/article/item", [
		                'article' => $article,
		                'col' => 'col-12 col-md-4 col-lg-4'
		            ])}
		        {/foreach} 
		    </div>
	    </div>
	{else}
	    {__d('template', 'khong_co_du_lieu')}
	{/if}
{/if}