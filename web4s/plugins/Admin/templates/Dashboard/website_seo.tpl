<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'thiet_lap_seo')}
        </h3>
    </div>
</div>

<div class="kt-form kt-form--label-right">
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'phan_nhom_sitemap')}
                        </span>

                        <a href="{ADMIN_PATH}/seo-setting" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($sitemap.combine_sitemap)}
                                    {__d('admin', 'co_phan_nhom')}
                                {/if}
                                {if empty($sitemap.combine_sitemap)}
                                    {__d('admin', 'khong_phan_nhom')}
                                {/if}
                            </span>
                        </a>
                    </div>
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'chia_sitemap_theo_nam')}
                        </span>

                        <a href="{ADMIN_PATH}/seo-setting" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($sitemap.split_by_year)}
                                    {__d('admin', 'co_chia')}
                                {/if}
                                {if empty($sitemap.split_by_year)}
                                    {__d('admin', 'khong_chia')}
                                {/if}
                            </span>
                        </a>
                    </div>
                </div>
                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'chuyen_huong_301')}
                        </span>

                        <a href="{ADMIN_PATH}/seo-setting" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($redirect.redirect_301)}
                                    {__d('admin', 'co_bat_chuyen_huong')}
                                {/if}
                                {if empty($redirect.redirect_301)}
                                    {__d('admin', 'khong_bat_chuyen_huong')}
                                {/if}
                            </span>
                        </a>
                    </div>
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'chuyen_huong_https')}
                        </span>

                        <a href="{ADMIN_PATH}/seo-setting" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($redirect.redirect_https)}
                                    {__d('admin', 'co_bat_chuyen_huong')}
                                {/if}
                                {if empty($redirect.redirect_https)}
                                    {__d('admin', 'khong_bat_chuyen_huong')}
                                {/if}
                            </span>
                        </a>
                    </div>
                </div>
                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            ROBOTS.TXT
                        </span>

                        <a href="{ADMIN_PATH}/seo-setting" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext kt-font-bolder">
                                {if !empty($exist_robots_file)}
                                    {__d('admin', 'da_tai_len')}
                                {else}
                                    {__d('admin', 'chua_tai_len')}
                                {/if}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>