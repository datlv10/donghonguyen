{assign url_website value = $this->Utilities->getUrlWebsite()}
{assign website_info value = $this->Setting->getWebsiteInfo()}

{assign website_name value = ''}
{assign company_name value = ''}

{if !empty($website_info.website_name)}
	{assign website_name value = $website_info.website_name}
{/if}

{if !empty($website_info.company_name)}
	{assign company_name value = $website_info.company_name}
{/if}


{if !empty($website_name) || !empty($company_name)}
	{assign schema_company  value = [
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'name' => "{if !empty($website_name)}{$website_name}{else}{$company_name}{/if}",
		'legalName' => $company_name,
		'url' => "{$url_website}/",
		'logo' => "{if !empty($website_info.company_logo)}{CDN_URL}{$website_info.company_logo}{/if}"
	]}

	<script type="application/ld+json">
		{$schema_company|@json_encode}
	</script>
{/if}