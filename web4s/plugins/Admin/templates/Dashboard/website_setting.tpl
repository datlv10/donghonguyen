<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'thiet_lap_website')}
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
                            {__d('admin', 'che_do')}
                        </span>
                        <a href="{ADMIN_PATH}/setting/change-mode" class="kt-widget12__value d-inline-block">
                            {if !empty($website_mode) && $website_mode == DEVELOP}
                                {__d('admin', 'phat_trien')} 
                            {/if}

                            {if !empty($website_mode) && $website_mode == LIVE}
                                {__d('admin', 'thuc_te')} 
                            {/if}
                        </a>
                    </div>
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'duong_dan_cdn')}
                        </span>
                        <div class="kt-widget12__value d-inline-block">
                            {if !empty($profile_info.cdn_url)}
                                {$profile_info.cdn_url}
                            {else}
                                {__d('admin', 'chua_xac_dinh')} 
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'giao_dien_duoc_chon')}
                        </span>

                        <a href="{ADMIN_PATH}/template/list" class="kt-widget12__value d-inline-block">
                            {CODE_TEMPLATE}
                        </a>
                    </div>
                </div>

                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'ngon_ngu')}
                        </span>

                        <span class="kt-widget12__value d-inline-block">
                            {if !empty($languages)}
                                <span class="form-control-plaintext kt-font-bolder">
                                    {foreach from = $languages item = language key = lang}
                                        <img width="17px" class="img-fluid mr-10" src="/admin/assets/media/flags/{$lang}.svg" title="{$language}" alt="{$language}">
                                    {/foreach}
                                </span>
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="kt-widget12__item">
                    <div class="kt-widget12__info">
                        <span class="kt-widget12__desc">
                            {__d('admin', 'cau_hinh_email')}
                        </span>

                        <a href="{ADMIN_PATH}/setting/email" class="kt-widget12__value d-inline-block">
                            <span class="form-control-plaintext">
                                {if !empty($email_setting.email) && !empty($email_setting.application_password)}
                                    <span class="kt-badge kt-badge--inline kt-badge--success kt-badge--bold">
                                        {__d('admin', 'da_thiet_lap')}
                                    </span>
                                {else}
                                    <span class="kt-badge kt-badge--inline kt-badge--danger kt-badge--bold">
                                        {__d('admin', 'chua_duoc_thiet_lap')}
                                    </span>
                                {/if}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>