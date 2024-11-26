{strip}
{if !empty($sitemap)}
	<?xml version="1.0" encoding="UTF-8"?>
	<?xml-stylesheet type="text/xsl" href="/templates/{CODE_TEMPLATE}/assets/css/sitemap.xsl"?>
	<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		{foreach from = $sitemap item = item}
		   	<sitemap>
		      	<loc>{if !empty($item.loc)}{$item.loc}{/if}</loc>
		      	<lastmod>{if !empty($item.lastmod)}{$item.lastmod}{/if}</lastmod>
		   	</sitemap>
	   	{/foreach}
   	</sitemapindex>
{/if}
{/strip}