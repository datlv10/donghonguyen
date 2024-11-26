<div class="kt-portlet nh-portlet nh-active-hover position-relative">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'gia_va_phien_ban_san_pham')}
            </h3>
        </div>
    </div>
    
    <div class="kt-portlet__body">

        <div id="wrap-change-item-attribute">
            {$this->element('../Product/element_change_attribute_item')}                
        </div>

        <div id="products-item-wrap" class="clearfix">
            {$this->element('../Product/items')}
        </div>

        <input id="nh-item-product" name="items" type="hidden" value="" >
    </div>
</div>