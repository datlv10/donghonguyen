{if !empty($value)}
	{assign var = products_id value = $value|json_decode:true}
	{assign var = data_product value = $this->Product->getProducts([
        'get_categories' => true,
        {FILTER} => [
            'ids' => $products_id
        ],
        {SORT} => [
            {FIELD} => 'name',
            {SORT} => ASC
        ]
    ], {LANGUAGE})}

    {if !empty($data_product)}
    	<div class="my-3">
		    <div class="row">
		        {foreach from = $data_product item = product}
		            {$this->element("../embed_attribute/element_product", [
		                'product' => $product,
		                'col' => 'col-12 col-md-4 col-lg-4'
		            ])}
		        {/foreach} 
		    </div>
	    </div>
	{else}
	    {__d('template', 'khong_co_du_lieu')}
	{/if}
{/if}