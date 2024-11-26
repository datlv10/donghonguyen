{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6 col-12">
			<div class="rounded shadow bg-white mt-5">
				<form nh-form="resend-token" action="/member/ajax-resend-token" method="post" autocomplete="off">
				    <div class="form-group">
				        <label for="email">
				            {__d('template', 'email')}: 
				            <span class="required">*</span>
				        </label>
				        <input name="email" type="text" class="form-control required">
				    </div>
				    
				    <div class="form-group">
				        <button type="submit" nh-btn-action="submit" class="btn btn-submit w-100">
				            {__d('template', 'xac_nhan')}
				        </button>
				    </div>
				</form>
			</div>
		</div>
	</div>	
</div>