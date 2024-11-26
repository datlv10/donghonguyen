<!DOCTYPE html>
<html lang="{$lang}">

	<!-- begin::Head -->
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

		<link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />

		<link href="{ADMIN_PATH}/assets/template-v2/css/main.css" rel="stylesheet" type="text/css" />
		
	</head>
	
	<!-- end::Head -->

	<!-- begin::Body -->
	<body>

		{$this->fetch('content')}

		<script type="text/javascript">
			var adminPath = '{ADMIN_PATH}';	
			var cdnUrl = '{CDN_URL}';
			var templatePath = '{URL_TEMPLATE}';
			var csrfToken = '{$this->getRequest()->getAttribute("csrfToken")}';
			var accessKeyUpload = '';
			var useMultipleLanguage = Boolean("{$use_multiple_language}");
			var listLanguage = JSON.parse('{$list_languages|@json_encode}');
		</script>

	    <script src="{ADMIN_PATH}/assets/template-v2/lib/jquery/jquery-3.5.1.min.js"></script>
	    <script src="{ADMIN_PATH}/assets/template-v2/js/constants.js"></script>
	    <script src="{ADMIN_PATH}/assets/template-v2/js/config.js"></script>
	    <script src="{ADMIN_PATH}/assets/template-v2/js/main.js"></script>
	    <script src="{ADMIN_PATH}/assets/template-v2/js/layout.js"></script>

	</body>

	<!-- end::Body -->
</html>