{assign var = plugins value = $this->Setting->getListPlugins()}
{if !empty($plugins.notification)}
    <link href="{URL_TEMPLATE}assets/css/notification.css" rel="stylesheet" type="text/css" />

    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
    <script src="/firebase-init.js"></script>
    <script>
        const messaging = firebase.messaging();
    </script>
    <script src="{URL_TEMPLATE}assets/js/notification.js" type="text/javascript"></script>

    <div nh-element-push="wrap" class="push-bell d-none">
        <div class="push-icon-container">
            <div class="push-icon"></div>
        </div>

        <div class="push-paragraph push-collapsed"></div>

        <div class="push-dialog push-collapsed">
            <div class="push-dialog-title">
                {__d('template', 'quan_ly_thong_bao')}
            </div>

            <div class="push-notification">
                <div class="push-notification-icon"></div>
                <div class="push-notification-paragraph-large"></div>
                <div class="push-notification-paragraph-medium"></div>
                <div class="push-notification-paragraph-large"></div>
                <div class="push-notification-paragraph-small"></div>
            </div>

            <div class="push-dialog-button-container">
                <div nh-action-push="subscribe" class="push-dialog-button"></div>
            </div>
        </div>

        <div class="push-help push-collapsed" style="background-image: url('{URL_TEMPLATE}assets/img/notification/allow-notifications.png');"></div>
    </div>
{/if}