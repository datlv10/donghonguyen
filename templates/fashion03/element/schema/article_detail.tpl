{if !empty($schema_data[{ARTICLE_DETAIL}])}
    {assign url_website value = $this->Utilities->getUrlWebsite()}
    {assign website_info value = $this->Setting->getWebsiteInfo()}
    {assign data_article_detail value = $schema_data[{ARTICLE_DETAIL}]}

    {assign image_avatar value = ''}
    {if !empty($data_article_detail.image_avatar)}
        {$image_avatar = "{CDN_URL}{$data_article_detail.image_avatar}"}
    {/if}

    {assign date_modified value = ''}
    {if !empty($data_article_detail.updated)}
        {$date_modified = $this->Utilities->convertIntgerToDateTimeString($data_article_detail.updated, 'Y-m-d H:i:s')}
    {/if}

    {assign date_published value = ''}
    {if !empty($data_article_detail.created)}
        {$date_published = $this->Utilities->convertIntgerToDateTimeString($data_article_detail.created, 'Y-m-d H:i:s')}
    {/if}
    
    {assign var = rating value = [
        '@type' => 'AggregateRating',
        'ratingValue' => "{if !empty($data_article_detail.rating)}{$data_article_detail.rating}{else}5{/if}",
        'reviewCount' => "{if !empty($data_article_detail.rating_number)}{$data_article_detail.rating_number}{else}1{/if}"
    ]}

    {assign schema_article_detail value = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => "{if !empty($data_article_detail.name)}{$data_article_detail.name}{/if}",
        'image' => $image_avatar,
        'dateModified' => $date_modified,
        'datePublished' => $date_published,
        'publisher' => [
            '@type' => 'Organization',
            'name' => "{if !empty($website_info.website_name)}{$website_info.website_name}{/if}",
            'logo' => [
                '@type' => 'ImageObject',
                'url' => "{if !empty($website_info.company_logo)}{CDN_URL}{$website_info.company_logo}{/if}"
            ]
        ],
        'author' => "{if !empty($website_info.website_name)}{$website_info.website_name}{/if}",
        'mainEntityOfPage' => $this->Utilities->getUrlCurrent(),
        'aggregateRating' => $rating
    ]}

    <script type="application/ld+json">
        {$schema_article_detail|@json_encode}
    </script>
{/if}