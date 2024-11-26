{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {__d('template', 'quan_ly_don_hang')}]
	]
])}
 
<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			{$this->element('../Member/element_promotion')}
		</div>
	</div>	
</div>