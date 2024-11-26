<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'doi_tac_moi')}
        </h3>
    </div>
</div>
<div class="kt-portlet__body">
    <div class="kt-widget4">
        {if !empty($new_partner)}
            {foreach from=$new_partner key=key item=partner}
                {assign var = url_detail value = "#"}
                {if !empty($partner.Customers.id)}
                    {assign var = url_detail value = "{ADMIN_PATH}/customer/affiliate/detail/{$partner.Customers.id}"}
                {/if}

                <div class="kt-widget4__item">
                    <div class="kt-widget4__info">
                        <a href="{$url_detail}" class="kt-widget4__username">
                            {if !empty($partner.Customers.full_name)}
                                {$partner.Customers.full_name}
                            {/if}
                        </a>
                        <p class="kt-widget4__text">
                            {if !empty($partner.Customers.email)}
                                {$partner.Customers.email}
                            {/if}
                        </p>
                    </div>
                    <a href="{$url_detail}" class="btn btn-sm btn-label-brand btn-bold">
                        {__d('admin', 'chi_tiet')}
                    </a>
                </div>
            {/foreach}
        {else}
            <p class="text-center">
                {__d('admin', 'khong_co_du_lieu')}
            </p>
        {/if}
    </div>
</div>