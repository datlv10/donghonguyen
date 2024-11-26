
{assign var = debt value = 0}
{if !empty($order.debt)}
    {assign var = debt value = (float)$order.debt}
{/if}

{assign var = status value = null}
{if !empty($order.status)}
    {assign var = status value = $order.status}
{/if}

{if !empty($can_payment)}
    <span class="btn btn-sm btn-secondary btn-confirm-payment">
        {__d('admin', 'xac_nhan_thanh_toan')}
    </span>
{/if}

{if $order_status == {NEW_ORDER}}
    <button type="button" class="btn btn-sm btn-brand btn-confirm-shipping">
        {__d('admin', 'giao_hang')}
    </button>

    <button data-update="1" data-link="{ADMIN_PATH}/order/detail" data-status="{CONFIRM}" type="button" class="btn btn-sm btn-brand btn-save">
        {__d('admin', 'duyet_don')}
    </button>
{/if}

{if $order_status == {CONFIRM}}
    <button type="button" class="btn btn-sm btn-brand btn-confirm-shipping">
        {__d('admin', 'giao_hang')}
    </button>
{/if}

{assign var = last_shipping value = []}
{if !empty($shippings)}
    {assign var = last_shipping value = $shippings|@end}
{/if}

{if $order_status == {PACKAGE}}
    <span data-status="{DELIVERY}" data-shipping-id="{if !empty($last_shipping.id)}{$last_shipping.id}{/if}" class="btn btn-sm btn-brand float-right btn-shipping-status">
        {__d('admin', 'xuat_kho')}
    </span>
{/if}

{if $order_status == {EXPORT} && !$shipped}
    <span data-status="{DELIVERED}" data-shipping-id="{if !empty($last_shipping.id)}{$last_shipping.id}{/if}" class="btn btn-sm btn-brand float-right btn-shipping-status">
        {__d('admin', 'da_giao_hang')}
    </span>
{/if}

{if $order_status == {DONE}}
{/if}


{if $order_status == {DRAFT}}
    <button data-update="1" data-link="{ADMIN_PATH}/order/detail" data-status="{CONFIRM}" type="button" class="btn btn-sm btn-brand btn-save">
        {__d('admin', 'duyet_don')}
    </button>

    <button type="button" class="btn btn-sm btn-secondary btn-cancel-order">
        {__d('admin', 'huy_don')}
    </button>
{/if}

{if empty($order_status)}
    <div class="btn-group">
        <button id="btn-save" data-update="1" data-link="{ADMIN_PATH}/order/detail" data-status="{NEW_ORDER}" class="btn btn-sm btn-brand btn-save" type="button" shortcut="112">
            <i class="la la-plus"></i>
            {__d('admin', 'tao_don_moi')} (F1)
        </button>
        
        <button type="button" class="btn btn-sm btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
        <div class="dropdown-menu dropdown-menu-right">
            <ul class="kt-nav p-0">
                <li class="kt-nav__item">
                    <span data-update="1" data-link="{ADMIN_PATH}/order/detail" data-status="{CONFIRM}" class="kt-nav__link btn-save">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'tao_don_va_duyet')}
                        </span>
                    </span>
                </li>
            </ul>
        </div>
    </div>
{/if}

{if !empty($order.id) && !$order_status|in_array:[CANCEL, DRAFT]}
    <div class="dropdown">
        <button class="btn btn-sm btn-brand" type="button" data-toggle="dropdown">
            <i class="la la-angle-down m-0"></i>
        </button>
        <div class="dropdown-menu">
            <span class="dropdown-item cursor-p btn-cancel-order">
                {__d('admin', 'huy_don')}
            </span>

            {if $order_status|in_array:[NEW_ORDER, CONFIRM]}
                <a href="{ADMIN_PATH}/order/update/{$order.id}" class="dropdown-item">
                    {__d('admin', 'sua_thong_tin')}
                </a>
            {/if}
        </div>
    </div>
{/if}