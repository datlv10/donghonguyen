{$this->Minify->compress([
	"/templates/{CODE_TEMPLATE}/assets/css/bootstrap.css",
	"/templates/{CODE_TEMPLATE}/assets/css/swiper.css",
	"/templates/{CODE_TEMPLATE}/assets/lib/fontawesome/css/fontawesome.css",
	"/templates/{CODE_TEMPLATE}/assets/lib/fontawesome/css/light.css",
	"/templates/{CODE_TEMPLATE}/assets/lib/fontawesome/css/solid.css",
	"/templates/{CODE_TEMPLATE}/assets/lib/fontawesome/css/brands.css",
	"/templates/{CODE_TEMPLATE}/assets/css/lightgallery.css"
])}

{* Load thư viện khác không dùng ở trang chủ *}
{if PAGE_TYPE != 'home'}
	{$this->Minify->compress([
		"/templates/{CODE_TEMPLATE}/assets/lib/jstree/jstree.bundle.css",
		"/templates/{CODE_TEMPLATE}/assets/css/variable.css",
		"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap-datepicker/bootstrap-datepicker.min.css",
		"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap-select/bootstrap-select.css",
		"/templates/{CODE_TEMPLATE}/assets/lib/jquery-ui/jquery-ui.min.css",
		"/templates/{CODE_TEMPLATE}/assets/css/catalogue.css",
		"/templates/{CODE_TEMPLATE}/assets/css/comment-rating.css",
		"/templates/{CODE_TEMPLATE}/assets/css/member.css",
		"/templates/{CODE_TEMPLATE}/assets/css/order.css"
	])}
{/if}

{* my css *}
{$this->Minify->compress([
	"/templates/{CODE_TEMPLATE}/assets/css/variable.css",
	"/templates/{CODE_TEMPLATE}/assets/css/compare.css",
	"/templates/{CODE_TEMPLATE}/assets/css/page.css",
	"/templates/{CODE_TEMPLATE}/assets/css/utilities.css",
	"/templates/{CODE_TEMPLATE}/assets/css/custom.css"
])}