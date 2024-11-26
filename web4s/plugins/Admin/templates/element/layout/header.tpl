<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">
	<!-- 
		Uncomment this to display the close button of the panel
		<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
	-->
	<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
		<div class="kt-header-logo">
			<a href="{ADMIN_PATH}">
				<img alt="Web4s" src="{ADMIN_PATH}/assets/media/logos/logo4s-02.svg" style="width:100px;" />
			</a>
			<i class="fs-11 text-muted pt-5 pl-5">
				Version {ADMIN_VERSION_UPDATE}
			</i>
		</div>

		{$this->element('layout/header_menu')}
	</div>

	{$this->element('layout/header_topbar')}
</div>