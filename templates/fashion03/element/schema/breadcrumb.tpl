{if !empty($breadcrumb)}
	{assign url_website value = $this->Utilities->getUrlWebsite()}
	{assign website_info value = $this->Setting->getWebsiteInfo()}
	{assign website_name value = ''}

	{if !empty($website_info.website_name)}
		{assign website_name value = $website_info.website_name}
	{/if}

	{assign list_item_breadcrumb value = []}
	{$list_item_breadcrumb[0] = [
		'@type' => 'ListItem',
		'position' => 1,
		'item' => [
			'@id' => "{$url_website}",
			'name' => $website_name
		]
	]}

	{foreach from = $breadcrumb item = item key = key_breadcrumb}
		{$list_item_breadcrumb[$key_breadcrumb + 1] = [
			'@type' => 'ListItem',
			'position' => $key_breadcrumb + 2,
			'item' => [
				'@id' => "{$url_website}/{$item.url}",
				'name' => $item.name
			]
		]}
	{/foreach}

	{assign schema_breadcrumb  value = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => $list_item_breadcrumb
	]}

	<script type="application/ld+json">
		{$schema_breadcrumb|@json_encode}
	</script>
{/if}