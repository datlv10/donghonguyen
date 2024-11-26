
{if !empty($value)}
	{assign var = article_id value = $value|json_decode:true}
	{assign var = data_article value = $this->Article->getArticles([
        'get_categories' => true,
        {FILTER} => [
            'ids' => $article_id
        ],
        {SORT} => [
            {FIELD} => 'name',
            {SORT} => ASC
        ]
    ], {LANGUAGE})}

    {assign var = config_slider value = [
		'slidesPerView' => 1,
		'spaceBetween' => 10,
		'lazy' => false,
		'autoplay' => [
			'delay' => 5000,
			'disableOnInteraction' => true,
			'pauseOnMouseEnter' => true
		],
		'pagination' => [
			'enabled' => false,
			'el' => '.swiper-pagination',
			'clickable' => true
		],
		'navigation' => [
			'nextEl' => '.swiper-button-next',
			'prevEl' => '.swiper-button-prev'
		],
		'breakpoints' => [
			'640' => [
				'slidesPerView' => 2,
				'spaceBetween' => 10
			],
			'768' => [
				'slidesPerView' => 3,
				'spaceBetween' => 20
			],
			'1024' => [
				'slidesPerView' => 3,
				'spaceBetween' => 30
			]
		]
	]}

	{if !empty($data_article)}
		<div class="my-3">
		    <div class="swiper" nh-swiper="{htmlentities($config_slider|@json_encode)}">
		        <div class="swiper-wrapper">
		            {foreach from = $data_article item = article}
		            	{if empty($article.url)}{continue}{/if}
			            {$this->element("../block/article/item", [
			                'article' => $article,
			                'is_slider' => true
			            ])}
		            {/foreach}
		        </div>
		        {if !empty($config_slider.pagination)}
		            <!-- If we need pagination -->
		            <div class="swiper-pagination"></div>
		        {/if}
		        {if !empty($config_slider.navigation)}
		            <div class="swiper-button-next">
		                <i class="fa-light fa-angle-right h1"></i>
		            </div>
		            <div class="swiper-button-prev">
		                <i class="fa-light fa-angle-left h1"></i>
		            </div>
		        {/if}
		    </div>
	    </div>
	{else}
	    {__d('template', 'khong_co_du_lieu')}
	{/if}
{/if}