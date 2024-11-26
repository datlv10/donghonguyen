{strip}
<a href="javascript:;" class="quickview-close effect-rotate icon-close display-1" data-dismiss="modal">
    <i class="fa-light fa-xmark"></i>
</a>
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
{/strip}