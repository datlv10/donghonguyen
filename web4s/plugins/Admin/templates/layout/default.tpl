<!DOCTYPE html>
<html lang="{$lang}">
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>
			{if !empty($title_for_layout)}
				{$title_for_layout}
			{else}
				Control Panel
			{/if} 
			| Admin
		</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link href="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.min.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/style.bundle.min.css" rel="stylesheet" type="text/css" />


		<link href="{ADMIN_PATH}/assets/css/skins/header/base/dark.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/skins/header/menu/dark.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/skins/brand/dark.css" rel="stylesheet" type="text/css" />

		{if !empty($css_page)}
	        {foreach from = $css_page item = css_file}
	        	<link href="{ADMIN_PATH}{$css_file}" rel="stylesheet" type="text/css" />
	        {/foreach}
	    {/if}
		<link href="{ADMIN_PATH}/assets/css/custom.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />

		<link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />
	</head>
	
	<body path-menu="{if !empty($path_menu)}{$path_menu}{/if}" class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-page--loading">

		{$this->element('layout/header_mobile')}

		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
				<div id="kt_wrapper" class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper">

					{$this->element('layout/header')}

					<div id="kt_content" class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
						<div class="kt-container-layout kt-grid__item kt-grid__item--fluid kt-grid__item kt-grid__item--fluid {if !empty($full_screen)}kt-container--fluid{/if}">
							{$this->fetch('content')}
						</div>
					</div>
				</div>
			</div>
		</div>

		{$this->element('Admin.layout/notifications')}

		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>
		

		<ul class="kt-sticky-toolbar">
			<li class="kt-sticky-toolbar__item kt-sticky-toolbar__item--brand" data-toggle="kt-tooltip" title="" data-placement="left" data-original-title="{__d('admin', 'gui_yeu_cau')}">
				<a href="{ADMIN_PATH}/feedback">
					<i class="flaticon2-telegram-logo"></i>
				</a>
			</li>
			<li class="kt-sticky-toolbar__item kt-sticky-toolbar__item--danger" id="kt_sticky_toolbar_chat_toggler" data-toggle="kt-tooltip" title="" data-placement="left" data-original-title="{__d('admin', 'hotline_ho_tro')}: 1900 6680">
				<a href="tel:19006680">
					<i class="flaticon2-phone"></i>
				</a>
			</li>
		</ul>

		<script type="text/javascript">
			var adminPath = '{ADMIN_PATH}';	
			var cdnUrl = '{CDN_URL}';
			var paginationLimitAdmin = '{PAGINATION_LIMIT_ADMIN}';
			var templatePath = '{URL_TEMPLATE}';
			var csrfToken = '{$this->getRequest()->getAttribute('csrfToken')}';
			var accessKeyUpload = "{$access_key_upload}";
			var useMultipleLanguage = Boolean("{$use_multiple_language}");
			var listLanguage = JSON.parse('{$list_languages|@json_encode}');
			var languageAdmin = "{LANGUAGE_ADMIN}";
		</script>
		
	
		<script src="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/scripts.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>		
		<script src="{ADMIN_PATH}/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.vi.min.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/nh_notification.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		
		

		{if !empty($js_page)}
	        {foreach from = $js_page item = js_file}
	            <script src="{ADMIN_PATH}{$js_file}?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
	        {/foreach}
	    {/if}

	</body>

</html>