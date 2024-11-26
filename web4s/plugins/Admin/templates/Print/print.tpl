<!DOCTYPE html>
<html>
    <head>
        <base href="">
        <meta charset="utf-8" />
        <title>
            {if !empty($title_for_layout)}
                {$title_for_layout}
            {else}
                {__d('admin', 'mau_in')}
            {/if} 
        </title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />

        <link href="{ADMIN_PATH}/assets/css/style.bundle.min.css" rel="stylesheet" type="text/css" />
        <link href="{URL_TEMPLATE}/assets/css/print-page.css" rel="stylesheet" type="text/css" />
    </head>
    
    <body>
        <div id="nh-print-page" nh-view="{if !empty($view)}{$view}{/if}" nh-id-record="{if !empty($id_record)}{$id_record}{/if}" nh-name="{if !empty($name)}{$name}{/if}" class="print-page">
        </div>
    </body>

    <script type="text/javascript">
        var adminPath = '{ADMIN_PATH}'; 
        var cdnUrl = '{CDN_URL}';
        var templatePath = '{URL_TEMPLATE}';
        var csrfToken = '{$this->getRequest()->getAttribute('csrfToken')}';
    </script>

    <script src="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/plugins/global/scripts.bundle.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/plugins/print/print-this.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/assets/js/print-page.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
</html>