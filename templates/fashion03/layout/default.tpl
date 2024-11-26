<!DOCTYPE html>
<html lang="{LANGUAGE}" csrf-token="{$this->getRequest()->getAttribute('csrfToken')}">
<head>
    {assign var = title value = ""}
    {if !empty($seo_info.title)}
        {assign var = title value = "{$seo_info.title}"}
    {/if}
    {if !empty($title_for_layout)}
        {assign var = title value = "{$title_for_layout}"}
    {/if}

    <title>{$title}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta name="description" content="{if !empty($seo_info.description)}{$seo_info.description}{/if}" />
    <meta name="keywords" content="{if !empty($seo_info.keywords)}{$seo_info.keywords}{/if}" />
    
    <link rel="canonical" href="{$this->Utilities->getUrlPath()}">
    <link rel="alternate" hreflang="{LANGUAGE}" href="{$this->Utilities->getUrlCurrent()}" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet">
    {assign website_info value = $this->Setting->getWebsiteInfo()}
    <link href="{if !empty($website_info.favicon)}{CDN_URL}{$website_info.favicon}{else}/favicon.ico{/if}" rel="icon" type="image/x-icon"/>
      
    {assign var = css_cache_key value = 'css'}
    {if PAGE_TYPE == 'home'}
        {$css_cache_key = 'css_home'}
    {/if}
    {$this->element('layout/css', [], $this->Setting->getConfigCacheView($css_cache_key, {LAYOUT}))}
    {$this->element('fonts')}

    {assign var = embed_code value = []}
    {if !empty($data_init.embed_code)}
        {assign var = embed_code value = $data_init.embed_code}
    {/if}

    {if !empty($embed_code.head) && empty($embed_code.time_delay)}
        {$embed_code.head}
    {/if}
    
</head>

<body class="{if !empty(DEVICE)}is-mobile{/if} {if !empty(PAGE_TYPE)}{PAGE_TYPE}{/if}">
    {if !empty($embed_code.top_body) && empty($embed_code.time_delay)}
        {$embed_code.top_body}
    {/if}

    {if !empty($page_code) && !empty($structure)}
        {assign var = page_cache_options value = []}
        {if !empty($cache_page)}
            {assign var = page_cache_options value = $this->Setting->getConfigCacheView($page_code, {PAGE})}
        {/if}

        {$this->element('layout/page', [
            'structure' => $structure
        ], $page_cache_options)}
    {/if}



    {$this->element('layout/modal', [], $this->Setting->getConfigCacheView('modal', {LAYOUT}))}
    <input id="nh-data-init" type="hidden" value="{if !empty($data_init)}{htmlentities($data_init|@json_encode)}{/if}">



    {$this->element('schema/company', [], $this->Setting->getConfigCacheView('schema_company', {LAYOUT}))}
    {$this->element('schema/website', [], $this->Setting->getConfigCacheView('schema_website', {LAYOUT}))}

    {if !empty(PAGE_TYPE) && PAGE_TYPE != HOME}
        {$this->element('schema/breadcrumb')}
    {/if}

    {if !empty(PAGE_TYPE) && PAGE_TYPE == PRODUCT_DETAIL}
        {$this->element('schema/product_detail')}
    {/if}

    {if !empty(PAGE_TYPE) && PAGE_TYPE == ARTICLE_DETAIL}
        {$this->element('schema/article_detail')}
    {/if}
    

    {assign var = js_cache_key value = 'js'}
    {if PAGE_TYPE == 'home'}
        {$js_cache_key = 'js_home'}
    {/if}
    {$this->element('layout/js', [], $this->Setting->getConfigCacheView($js_cache_key, {LAYOUT}))}


    {if !empty($embed_code.bottom_body) && empty($embed_code.time_delay)}
        {$embed_code.bottom_body}
    {/if}
    
    {$this->element('../Notification/bell')}

    {if !empty($nh_admin_bar)}
        {$nh_admin_bar}
    {/if}
</body>
</html>
