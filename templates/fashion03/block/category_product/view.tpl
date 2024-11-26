{strip}
<div class="categories">
    {if !empty($data_extend['locale'][{LANGUAGE}]['tieu_de'])}
        <div class="border-bottom text-uppercase h5 font-weight-bold border-gray pb-4 mb-4">
        	{$this->Block->getLocale('tieu_de', $data_extend)}
        </div>
    {/if}
    
    {if !empty($data_block.data)}
        <div nh-menu="active">
            {$this->element('../block/category_product/item', [
            	'categories' => $data_block.data,
            	'parent_id' => null
            ])}
        </div>
    {/if}
</div>
{/strip}