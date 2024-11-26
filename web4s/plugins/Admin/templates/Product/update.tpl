{$this->element('../Product/element_subheader')}

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/product/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="d-none">
            <input type="hidden" name="draft" value="">
            <input type="hidden" name="seo_score" value="" id="seo-score">
            <input type="hidden" name="keyword_score" value="" id="keyword-score">
        </div>

        {$this->element('../Product/element_basic_info')}

        {$this->element('../Product/element_price')}

        {$this->element('../Product/element_description')}
        
        <div id="attributes-product">
            {$this->element('../Product/element_attributes')}
        </div>

        {$this->element('../Product/element_another_info')}

        {$this->element('../Product/element_seo')}

        <input type="hidden" name="product_id" value="{if !empty($id)}{$id}{/if}">
    </form>
</div>

{$this->element('../Product/popover_price_special')}