<!DOCTYPE html>
<html lang="{LANGUAGE}" csrf-token="{$this->getRequest()->getAttribute('csrfToken')}">
<head>
    {assign var = title value = "{if !empty($title_for_layout)}{$title_for_layout}{else}404{/if}"}
    {if !empty($seo_info.title)}
        {assign var = title value = "{$seo_info.title}"}
    {/if}

    <title>{$title}</title>

    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta name="description" content="{if !empty($seo_info.description)}{$seo_info.description}{/if}" />
    <meta name="keywords" content="{if !empty($seo_info.keywords)}{$seo_info.keywords}{/if}" />
    <link rel="canonical" href="{$this->Utilities->getUrlWebsite()}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="{if !empty($seo_info.site_name)}{$seo_info.site_name}{/if}">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{if !empty($seo_info.description)}{$seo_info.description}{/if}">
    <meta name="twitter:image" content="{if !empty($seo_info.image)}{CDN_URL}{$seo_info.image}{/if}">

    <!-- Open Graph data -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{if !empty($seo_info.site_name)}{$seo_info.site_name}{/if}">
    <meta property="og:title" content="{$title}">
    <meta property="og:url" content="{$this->Utilities->getUrlCurrent()}">
    <meta property="og:image" content="{if !empty($seo_info.image)}{CDN_URL}{$seo_info.image}{/if}">
    <meta property="og:description" content="{if !empty($seo_info.description)}{$seo_info.description}{/if}">
    
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <link rel="dns-prefetch" href="{CDN_URL}">


    {$this->element('layout/css', [], $this->Setting->getConfigCacheView('css', {LAYOUT}))}

</head>

<body>
    {$this->element('../Page/404', [], $this->Setting->getConfigCacheView({PAGE}, {LAYOUT}))}
</body>
</html>
