{assign var = my_notifications value = $this->NhNotificationAdmin->getFirstPageNotifcation('my_notification')}
{assign var = general_notifications value = $this->NhNotificationAdmin->getFirstPageNotifcation('general')}

{assign var = more_page_my_notification value = $this->NhNotificationAdmin->existmorePageNotifcation('my_notification')}
{assign var = more_page_general value = $this->NhNotificationAdmin->existmorePageNotifcation('general')}

<div nh-notification="slidebar" id="kt_quick_panel" class="kt-quick-panel">
    <a href="#" class="kt-quick-panel__close" id="kt_quick_panel_close_btn">
        <i class="flaticon2-delete"></i>
    </a>
    <div class="kt-quick-panel__nav">
        <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x" role="tablist">
            <li class="nav-item active">
                <a class="nav-link active" data-toggle="tab" href="#my-notifications" role="tab">
                    {__d('admin', 'thong_bao_cua_toi')}
                    <span nh-notification="count-my-notification" class="kt-badge kt-badge--outline kt-badge--info ml-5 d-none"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#general-notifications" role="tab">
                    {__d('admin', 'thong_bao_chung')}
                    <span nh-notification="count-general-notification" class="kt-badge kt-badge--outline kt-badge--info ml-5 d-none"></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="kt-quick-panel__content">
        <div class="tab-content">
            <div id="my-notifications" class="tab-pane fade show kt-scroll active" role="tabpanel">
                <div nh-list-notification="my_notification" class="kt-notification">
                    {$this->element('Admin.layout/notifications_items', [
                        'notifications' => $my_notifications,
                        'more_page' => $more_page_my_notification, 
                        'page' => 1,
                        'init' => true
                    ])}
                </div>
            </div>

            <div id="general-notifications" class="tab-pane fade show kt-scroll" role="tabpanel">
                <div nh-list-notification="general" class="kt-notification">
                    {$this->element('Admin.layout/notifications_items', [
                        'notifications' => $general_notifications, 
                        'more_page' => $more_page_general, 
                        'page' => 1,
                        'init' => true
                    ])}
                </div>
            </div>
        </div>
    </div>
</div>