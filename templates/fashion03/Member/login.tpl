{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-xl-5 col-lg-6 col-md-8 col-12">
			<div class="rounded shadow bg-white mt-5">
				{$this->element('../Member/element_login_form')}	
			</div>
		</div>
	</div>	
</div>