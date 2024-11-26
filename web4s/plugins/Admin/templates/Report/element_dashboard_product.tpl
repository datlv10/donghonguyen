<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <a href="{ADMIN_PATH}/report/product">
            <h3 class="kt-portlet__head-title">
                    {__d('admin', 'top_10_san_pham_ban_chay')}
            </h3>
        </a>
    </div>
</div>
<div class="kt-portlet__body wrp-scrollbar">
    {if !empty($report['item_report'])}
        {assign var = report_product value = $report['item_report']}
        <div class="kt-portlet__body p-0">
            <div class="kt-widget4">
                {foreach from = $report_product item = item}
                    <div class="kt-widget4__item">
                        <div class="kt-widget4__info">
                            <a target="_blank" href="{ADMIN_PATH}/product/detail/{if !empty($item['product_id'])}{$item['product_id']}{/if}" class="kt-widget4__title kt-widget4__title--light">
                                {if !empty($item['name_extend'])}
                                    {$item['name_extend']}
                                {/if}
                            </a>
                            <p class="kt-widget4__text kt-font-primary">
                                {__d('admin', 'doanh_thu')}: {if !empty($item['total'])}{$item['total']|number_format:0:".":","}{/if}
                            </p>
                        </div>
                        <span class="kt-widget4__number kt-font-info">
                            {if !empty($item['quantity'])}
                                {$item['quantity']} {__d('admin', 'san_pham')}
                            {/if}
                        </span>
                    </div>
                {/foreach}
            </div>
        </div>
    {else}
        {__d('admin', 'chua_co_thong_tin')}
    {/if}
</div>
