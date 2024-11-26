{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="row justify-content-center">
				<div class="col-12 col-lg-6">
					<div class="h-100 bg-white p-4">
						<div class="modal-title h2 text-uppercase text-center font-weight-bold mb-4">
						    {__d('template', 'thay_doi_email')}
						</div>

						<form nh-form="change-email" action="/member/ajax-change-email" method="post" autocomplete="off">
							{$this->element('../Member/element_change_verify')}

						    <div class="form-group">
						        <label for="new_email" class="font-weight-normal color-main">
						            {__d('template', 'email_moi')}: 
						            <span class="required">*</span>
						        </label>
						        <input name="new_email" type="text" class="form-control" placeholder="{__d('template', 'nhap_email')}">
						    </div>
						    <input type="hidden" name="type" value="email">
						    
						    <div class="form-group">
						        <span nh-btn-action="submit" class="btn btn-submit w-100">
						            {__d('template', 'xac_nhan')}
						        </span>
						    </div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>