{strip}
{$this->element('breadcrumb', [ 
    'list_url' => [ 
        [
            'title' => {__d('template', 'gio_hang')}
        ] 
    ] 
])}

{if !empty($cart_info['items'])} 
    {assign var = items value = $cart_info['items']}
{/if}

<div class="container">
    {if !empty($items)}
        <div nh-cart-info>
            <div class="bg-white p-4 mb-3">
                <div class="h4 font-weight-bold mb-4">
                    {__d('template', 'thong_tin_san_pham')}
                    <small>
                        {if !empty($items)} ({count($items)} {__d('template', 'san_pham')}) {/if}
                    </small>
                </div>

                <table class="table cart-info-section responsive-table mb-0">
                    <thead>
                        <tr>
                            <th>{__d('template', 'san_pham')}</th>
                            <th>{__d('template', 'gia')}</th>
                            <th>{__d('template', 'so_luong')}</th>
                            <th class="text-right">{__d('template', 'tong_tien')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from = $items item = item key = product_item_id}
                            <tr nh-cart-item="{$product_item_id}" nh-cart-item-quantity="{if !empty($item.quantity)}{$item.quantity}{/if}" class="cart-item {if !$item@last}border-bottom{/if}">
                                <th scope="row">
                                    {if !empty($item['images'][0])} {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item['images'][0], 150)}"} {else} {assign var = url_img value =
                                    "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="} {/if}

                                    <div class="row mx-n2">
                                        <div class="col-lg-2 col-3 px-2">
                                            <div class="rounded overflow-hidden ratio-1-1">
                                                <a href="{$this->Utilities->checkInternalUrl($item.url)}">
                                                    <img class="img-fluid" src="{$url_img}" alt="{if !empty($item.name_extend)}{$item.name_extend}{/if}" />
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-9 px-2">
                                            <div class="flex-column d-flex">
                                                <a href="{$this->Utilities->checkInternalUrl($item.url)}">
                                                    <div class="name-cart">
                                                        {if !empty($item.name_extend)} {$item.name_extend} {/if}
                                                    </div>
                                                </a>
                                                <div class="remove-cart mt-2">
                                                    <a href="javascript:;" nh-remove-item-cart="{if !empty($item.product_item_id)}{$item.product_item_id}{/if}" class="font-weight-normal color-highlight">
                                                        {__d('template', 'xoa')}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <td data-title="{__d('template', 'gia')}">
                                    <span nh-cart-price="{if isset($item.price)}{$item.price}{else}0{/if}">
                                        {if isset($item.price)}
                                            {$item.price|number_format:0:".":","}
                                        {else}
                                            0
                                        {/if} 
                                    </span>
                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>

                                    {if !empty($item.default_price)}
                                        <span class="text-muted"> ( {$item.default_price|number_format:0:".":","} <span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span> ) </span>
                                    {/if}
                                </td>

                                <td data-title="{__d('template', 'so_luong')}" class="item-quantity">
                                    {$this->element('input_quantity', ['quantity' => "{if !empty($item.quantity)}{$item.quantity}{/if}"])}
                                </td>

                                <td class="text-right" data-title="{__d('template', 'tien')}">
                                    {if isset($item.total_item)} 
                                        <span nh-cart-total-item="{if isset($item.total_item)}{$item.total_item}{/if}">
                                            {$item.total_item|number_format:0:".":","}
                                        </span>
                                        <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                    {/if} 
                                    {if !empty($item.default_total_item)}
                                        <span class="text-muted"> ( {$item.default_total_item|number_format:0:".":","} <span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span> ) </span>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-lg-6 ml-auto">
                        <div class="entire-cart-totals mt-4">
                            <table class="cart-totals">
                                <tbody>
                                    <tr class="order-total">
                                        <th>
                                            {__d('template', 'tong_tien')}
                                        </th>
                                        <td>
                                            <b>
                                                <span class="price-amount">
                                                    <span nh-cart-total>
                                                        {if !empty($cart_info.total)} 
                                                            {$cart_info.total|number_format:0:".":","} 
                                                        {else}
                                                            0 
                                                        {/if}
                                                    </span>
                                                    <span class="currency-symbol">{CURRENCY_UNIT}</span>
                                                </span>
                                            </b>
                                            {if !empty($cart_info.total_default)}
                                            <span class="form-text"> ( {$cart_info.total_default|number_format:0:".":","} <span class="currency-symbol fs-12">{CURRENCY_UNIT_DEFAULT}</span> ) </span>
                                            {/if}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="proceed-to-checkout">
                                <a nh-cart-action="cart-confirm" href="javascript:;" class="btn btn-submit w-100">
                                    {__d('template', 'xac_nhan_gio_hang')}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}
        <div class="empty text-center my-5">
            <i class="fa-light fa-bag-shopping"></i>
            <div class="empty-cart mb-4">
                {__d('template', 'ban_can_them_mot_so_san_pham_vao_gio_hang_cua_minh')}.<br />
                {__d('template', 'vui_long_them_mot_so_san_pham_vao_gio_hang_cua_minh_vui_long_truy_cap_trang_chu_va_tim_san_pham_cua_ban')}.
            </div>
            <a class="btn btn-submit" href="/">
                {__d('template', 'trang_chu')}
            </a>
        </div>
    {/if}
</div>
{/strip}