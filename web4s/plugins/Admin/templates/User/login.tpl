<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>Web4s | {__d('admin', 'dang_nhap')}</title>

		<meta name="description" content="{__d('admin', 'dang_nhap')}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="shortcut icon" href="{ADMIN_PATH}/favicon.ico" />

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">

		<link href="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/pages/login/login-4.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />
		<link href="{ADMIN_PATH}/assets/css/login.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />
		
	</head>

	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

		<div class="kt-grid kt-grid--ver kt-grid--root">
			<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v4 kt-login--signin" id="kt_login">
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" style="background-color: #1b1b28;">
					<div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
						<div class="kt-login__container">
							<div class="kt-login__logo" style="margin-bottom: 30px;">
								<img alt="Web4s" src="{ADMIN_PATH}/assets/media/logos/logo4s-01.svg" style="width: 250px;">
							</div>
							<div class="kt-login__signin">
								<div class="kt-login__head" style="margin: 0 0 20px 0;">
									<h3 class="kt-login__title">
										{__d('admin', 'dang_nhap_quan_tri')}
									</h3>
								</div>

								<form id="form-login" class="kt-form" action="{ADMIN_PATH}/ajax-login" method="post">
									<div class="input-group">
										<input name="username" class="form-control" type="text" placeholder="{__d('admin', 'tai_khoan')}" autocomplete="off">
									</div>

									<div class="input-group">
										<input name="password" class="form-control" type="password" placeholder="{__d('admin', 'mat_khau')}" >
									</div>									

									<div nh-show-error class="text-error"></div>

									<div class="kt-login__account">
										<label class="kt-checkbox kt-checkbox--tick kt-checkbox--success kt-font-bold">
											<input name="token" type="checkbox" value="{if !empty($token)}{$token}{/if}"> 
											I'm not a robot
											<span></span>
										</label>
									</div>

									<input type="hidden" name="redirect" value="{if !empty($redirect)}{$redirect}{/if}">

									<div class="kt-login__actions" style="margin-top: 10px;">
										<span id="btn-login" class="btn btn-dark btn-pill kt-login__btn-primary">
											{__d('admin', 'dang_nhap')}
										</span>
									</div>

									<div class="kt-login__account">
										<i class="text-muted" style="font-size: 12px;">
											© {$smarty.now|date_format:'%Y'} Web4s. 
											Version {ADMIN_VERSION_UPDATE}
										</i>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var adminPath = "{ADMIN_PATH}";
			var csrfToken = "{$this->getRequest()->getAttribute('csrfToken')}";
		</script>

		<script src="{ADMIN_PATH}/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/plugins/global/scripts.bundle.js" type="text/javascript"></script>

		<script src="{ADMIN_PATH}/assets/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
		<script src="{ADMIN_PATH}/assets/js/pages/login.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
	
	</body>

</html>