<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'top_doi_tac')}
        </h3>
    </div>
</div>
<div class="kt-portlet__body">
    <div class="kt-widget5">
        {if !empty($top_partner)}
            {foreach from=$top_partner key=key item=partner}
                {assign var = url_detail value = "#"}
                {assign var = key value = $key + 1}
                {if !empty($partner.Customers.id)}
                    {assign var = url_detail value = "{ADMIN_PATH}/customer/affiliate/detail/{$partner.Customers.id}"}
                {/if}

                <div class="kt-widget5__item">
                    <div class="kt-widget5__content">
                        <div class="kt-widget5__pic">
                            <img class="kt-widget7__img" src="/admin/assets/media/affiliate/top-{$key}.png" alt="" height="64">
                        </div>
                        <div class="kt-widget5__section">
                            <a href="{$url_detail}" class="kt-widget5__title">
                                {if !empty($partner.Customers.full_name)}
                                    {$partner.Customers.full_name}
                                {/if}
                            </a>
                            <p class="kt-widget5__desc">
                                {if !empty($partner.Customers.email)}
                                    {$partner.Customers.email}
                                {/if}
                            </p>
                            <div class="kt-widget5__info">
                                {if !empty($partner.Customers.phone)}
                                    {$partner.Customers.phone}
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="kt-widget5__content">
                        <div class="kt-widget5__stats text-center">
                            <span class="kt-widget5__number">
                                {if !empty($partner.total_point)}
                                    {$partner.total_point|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                            <span class="kt-widget5__sales">
                                {__d('admin', 'hoa_hong')}
                            </span>
                        </div>
                        <div class="kt-widget5__stats text-center">
                            <span class="kt-widget5__number">
                                {if !empty($partner.number_referral)}
                                    {$partner.number_referral|number_format:0:".":","}
                                {else}
                                    0
                                {/if}
                            </span>
                            <span class="kt-widget5__votes">
                                {__d('admin', 'luot_gioi_thieu')}
                            </span>
                        </div>
                    </div>
                </div>
            {/foreach}
        {else}
            <p class="text-center">
                {__d('admin', 'khong_co_du_lieu')}
            </p>
        {/if}
    </div>
</div>