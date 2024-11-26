{if !empty($notifications)}
    {foreach from = $notifications item = notification}
        {assign var = link value = '#'}
        {if !empty($notification.link)}
            {$link = $notification.link}
        {/if}

        {assign var = type value = ''}
        {if !empty($notification.type)}
            {$type = $notification.type}
        {/if}

        <a nh-notification="item" data-time="{if !empty($notification.created)}{$notification.created}{/if}" href="{$link}" target="_blank" class="kt-notification__item">
            <div class="kt-notification__item-icon">
                {if $type == 'general'}
                    <i class="flaticon2-layers kt-font-success"></i>
                {/if}

                {if $type == 'upgrade'}
                    <i class="flaticon-upload kt-font-primary"></i>
                {/if}

                {if $type == 'news'}
                    <i class="flaticon-doc kt-font-warning"></i>
                {/if}

                {if $type == 'promotion'}
                    <i class="flaticon2-percentage kt-font-danger"></i>
                {/if}

                {if $type == 'order'}
                    <i class="flaticon-doc kt-font-primary"></i>
                {/if}

                {if $type == 'contact'}
                    <i class="flaticon-whatsapp kt-font-warning"></i>
                {/if}
            </div>
            <div class="kt-notification__item-details">
                <div class="kt-notification__item-title">
                    {if !empty($notification.title)}
                        {$notification.title}
                    {/if}
                </div>

                <div class="kt-notification__item-time text-lowercase">
                    {if !empty($notification.created)}
                        {assign var = created value = $this->NhNotificationAdmin->parseTimeNotification($notification.created)}
                        {assign var = time value = ''}
                        {assign var = full_time value = ''}

                        {if !empty($created.time)}
                            {$time = $created.time}
                        {/if}

                        {if !empty($created.full_time)}
                            {$full_time = $created.full_time}
                        {/if}

                        {if !empty($time)}
                            {$time}
                        {else}
                            {$full_time}
                        {/if}
                    {/if}
                </div>
            </div>
        </a>
    {/foreach}

    {if !empty($more_page) }
        <div nh-notification="more" data-page="{if !empty($page)}{$page}{else}1{/if}" class="kt-notification-load-more cursor-p text-center p-10">
            <span>
                {__d('admin', 'xem_them')}
            </span>
        </div>
    {/if}
{/if}

{if empty($notifications) && !empty($init)}
    <div class="text-center">
        <i>{__d('admin', 'chua_co_thong_bao_nao')}</i>
    </div>
{/if}
