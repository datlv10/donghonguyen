{* Thư viện load ở tất cả các trang *}
{$this->Minify->compress([
	"/templates/{CODE_TEMPLATE}/assets/lib/jquery/jquery-3.6.0.min.js",	
	"/templates/{CODE_TEMPLATE}/assets/lib/jquery-lazy/jquery.lazy.min.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/jquery-lazy/jquery.lazy.plugins.min.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/jquery/jquery.validate.min.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/jquery/jquery.cookie.min.js",
    "/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/popper.min.js",
    "/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/util.js",
    "/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/dropdown.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/collapse.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/modal.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/toast.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap/tab.js",
	"/templates/{CODE_TEMPLATE}/assets/lib/swiper/swiper-bundle.min.js",
	"/templates/{CODE_TEMPLATE}/assets/js/constants.js",
    "/templates/{CODE_TEMPLATE}/assets/js/locales/{LANGUAGE}.js",
    "/templates/{CODE_TEMPLATE}/assets/lib/lightgallery-all.min.js",
    "/templates/{CODE_TEMPLATE}/assets/js/lazy.js"
], 'js')}

{* Load thư viện khác không dùng ở trang chủ *}
{if PAGE_TYPE != 'home'}
	{$this->Minify->compress([
	    "/templates/{CODE_TEMPLATE}/assets/lib/inputmask/jquery.inputmask.min.js",
	    "/templates/{CODE_TEMPLATE}/assets/lib/jstree/jstree.bundle.js",
	    
		"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap-select/bootstrap-select.min.js",	    
		"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap-datepicker/bootstrap-datepicker.min.js",
		"/templates/{CODE_TEMPLATE}/assets/lib/bootstrap-datepicker/locales/{LANGUAGE}.js",
		"/templates/{CODE_TEMPLATE}/assets/lib/tocbot/tocbot.min.js"
	], 'js')}
{/if}

{* My js *}
{$this->Minify->compress([	
    "/templates/{CODE_TEMPLATE}/assets/js/main.js",
	"/templates/{CODE_TEMPLATE}/assets/js/menu.js",
	"/templates/{CODE_TEMPLATE}/assets/js/search.js",
	"/templates/{CODE_TEMPLATE}/assets/js/product.js",
	"/templates/{CODE_TEMPLATE}/assets/js/order.js",
	"/templates/{CODE_TEMPLATE}/assets/js/wishlist.js",
	"/templates/{CODE_TEMPLATE}/assets/js/compare.js",
	"/templates/{CODE_TEMPLATE}/assets/js/member.js",
	"/templates/{CODE_TEMPLATE}/assets/js/contact.js",
	"/templates/{CODE_TEMPLATE}/assets/js/custom.js"
], 'js')}

{if PAGE_TYPE != 'home'}
	{$this->Minify->compress([
        "/templates/{CODE_TEMPLATE}/assets/js/genealogy.js",
        "/templates/{CODE_TEMPLATE}/assets/js/catalogue.js",
	    "/templates/{CODE_TEMPLATE}/assets/js/comment.js"
	], 'js')}
{/if}