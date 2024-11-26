{if !empty($schema_data[{PRODUCT_DETAIL}])}    
    {assign url_website value = $this->Utilities->getUrlWebsite()}
    {assign data_product_detail value = $schema_data[{PRODUCT_DETAIL}]}
    {assign var = first_item value = []}
    {if !empty($data_product_detail.items[0])}
        {assign var = first_item value = $data_product_detail.items[0]}
    {/if}

    {assign image_avatar value = ''}
    {if !empty($data_product_detail.all_images[0])}
        {$image_avatar = "{CDN_URL}{$data_product_detail.all_images[0]}"}
    {/if}

    {assign var = review value = []}
    {if !empty($data_product_detail.ratings)}
        {foreach from = $data_product_detail.ratings key = k_rating item = rating}
            {$review[$k_rating] = [
                '@type' => 'Review',
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => "{if !empty($rating.rating)}{$rating.rating}{/if}",
                    'bestRating' => '5'
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => "{if !empty($rating.full_name)}{$rating.full_name}{/if}"
                ]
            ]}
        {/foreach}
    {/if}

    {assign var = rating value = [
        '@type' => 'AggregateRating',
        'ratingValue' => "{if !empty($data_product_detail.rating)}{$data_product_detail.rating}{else}5{/if}",
        'reviewCount' => "{if !empty($data_product_detail.rating_number)}{$data_product_detail.rating_number}{else}1{/if}"
    ]}

    {assign var = offer value = [
        '@type' => 'Offer',
        'url' => "{$url_website}/{if !empty($data_product_detail.url)}{$data_product_detail.url}{/if}",
        'priceCurrency' => "{CURRENCY_UNIT}",
        'price' => "{if !empty($first_item.price)}{$first_item.price}{else}0{/if}",
        'priceValidUntil' => $this->Utilities->getCurrentDate(),
        'itemCondition' => 'https://schema.org/UsedCondition',
        'availability' => 'https://schema.org/InStock'
    ]}

    {if !empty($first_item.apply_special)}
        {assign var = offer value = [
            '@type' => 'AggregateOffer',
            'offerCount' => "{if isset($first_item.quantity_available) && !empty($data_init.product.check_quantity)}{$first_item.quantity_available}{else}0{/if}",
            'lowPrice' => "{if !empty($first_item.price_special)}{$first_item.price_special}{else}0{/if}",
            'highPrice' => "{if !empty($first_item.price)}{$first_item.price}{else}0{/if}",
            'priceCurrency' => "{CURRENCY_UNIT}"
        ]}
    {/if}

    {assign schema_product_detail  value = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => "{if !empty($data_product_detail.name)}{$data_product_detail.name}{/if}",
        'image' => $image_avatar,
        'description' => "{if !empty($data_product_detail.description)}{$data_product_detail.description|strip_tags}{/if}",
        'sku' => "{if !empty($first_item.code)}{$first_item.code}{/if}",
        'mpn' => "{if !empty($first_item.code)}{$first_item.code}{/if}",
        'brand' => [
            '@type' => 'Brand',
            'name' => "{if !empty($data_product_detail.brand_name)}{$data_product_detail.brand_name}{/if}"
        ],
        'offers' => $offer,
        'review' => $review,
        'aggregateRating' => $rating
    ]}

    <script type="application/ld+json">
        {$schema_product_detail|@json_encode}
    </script>
{/if}