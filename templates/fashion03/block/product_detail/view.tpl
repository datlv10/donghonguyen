{assign var = product value = []}
{if !empty($data_block.data)}
	{assign var = product value = $data_block.data}
{/if}

{if !empty($product)}
	{strip}
	<ol data-toc="div.product-detail-footer" data-toc-headings="h2,h3,h4" class="mb-0"></ol>
	<div class="product-detail-head">
		<div class="row">
			<div class="col-lg-6">
				{$this->element("../block/product_detail/element_product_image", [
	                'product' => $product
	            ])}
			</div>

			<div class="col-lg-6">
				{$this->element("../block/product_detail/element_product_info", [
	                'product' => $product
	            ])}
			</div>
		</div>
	</div>
	
	{$this->element("../block/product_detail/element_product_content", [
        'product' => $product
    ])}
{else}
	<p class="text-center font-danger my-4">{__d('template', 'thong_tin_san_pham_khong_ton_tai')}</p>
{/if}