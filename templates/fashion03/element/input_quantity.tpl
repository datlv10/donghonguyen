<div nh-quantity-product="wrap" class="product-quantity {if (isset($first_item.quantity_available) && $first_item.quantity_available <= 0  || empty($first_item.quantity_available)) && !empty($data_init.product.check_quantity) && !empty($check_quantity)}d-none{/if}">
    <span nh-quantity-product="subtract" class="btn-quantity quantity-subtract">
        <i class="fa-light fa-minus"></i>
    </span>

    <input nh-quantity-product="quantity" value="{if !empty($quantity)}{$quantity}{else}1{/if}" class="text-center number-input" type="text" maxlength="3" />

    <span nh-quantity-product="add" class="btn-quantity quantity-add">
        <i class="fa-light fa-plus"></i>
    </span>
</div>