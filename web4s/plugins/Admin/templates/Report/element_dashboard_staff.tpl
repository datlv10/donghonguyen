<div class="kt-portlet kt-portlet--bordered-semi kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'nhan_vien_suat_sac')}
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body wrp-scrollbar" style="max-height: 360px;">
        {assign var = report value = $report['item_report']}
        {if !empty($report)}
            {foreach from = $report item = item}
                {if !empty($item['image_avatar'])}
                    {assign var = image_avatar value = "{CDN_URL}{$this->Utilities->getThumbs($item['image_avatar'], 150)}"}
                {else}
                    {assign var = image_avatar value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                {/if}
                
                <div class="kt-widget4">
                    <div class="kt-widget4__item">
                        <div class="kt-widget4__pic kt-widget4__pic--logo">
                            <img src="{$image_avatar}" alt="{if !empty($item.staff_name)}{$item.staff_name}{/if}">
                        </div>
                        <div class="kt-widget4__info">
                            <a target="_blank" href="{ADMIN_PATH}/user/detail/{if !empty($item['staff_id'])}{$item['staff_id']}{/if}" class="kt-widget4__title">
                                {if !empty($item.staff_name)}
                                    {$item.staff_name}
                                {/if}
                            </a>
                            <p class="kt-widget4__text">
                                {if !empty($item.role)}
                                    {$item.role}
                                {/if}
                            </p>
                        </div>
                        <span class="kt-widget4__number kt-font-brand">
                            {if !empty($item.total)}
                                {$item.total|number_format:0:".":","}
                            {/if}
                        </span>
                    </div>
                </div>
            {/foreach}
        {else}
            {__d('admin', 'chua_co_thong_tin')}
        {/if}
    </div>
</div>