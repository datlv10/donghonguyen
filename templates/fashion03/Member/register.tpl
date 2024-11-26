{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-xl-6 col-lg-8 col-md-10 col-12">
			<div class="rounded shadow bg-white mt-5">
				{$this->element('../Member/element_register_form')}
			</div>	
		</div>
	</div>	
</div>