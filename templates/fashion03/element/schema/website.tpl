{assign url_website value = $this->Utilities->getUrlWebsite()}
{assign path_search value = 'tim-kiem'}
{if LANGUAGE != 'vi'}
	{assign path_search value = 'search'}
{/if}

{assign schema_website  value = [
	'@context' => 'https://schema.org',
	'@type' => 'WebSite',
	'url' => $url_website,
	'potentialAction' => [
		'@type' => 'SearchAction',
		'target' => "{$url_website}/{$path_search}?keyword={'{query}'}",
		'query-input' => 'required name=query'
	]
]}

<script type="application/ld+json">
    {$schema_website|@json_encode}
</script>