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
						    {__d('template', 'thay_doi_so_dien_thoai')}
						</div>
					
					    <form nh-form="change-phone" action="/member/ajax-change-phone" method="post" autocomplete="off">
					    	{$this->element('../Member/element_change_verify')}

			                <div class="form-group">
			                    <label for="new_phone">
			                        {__d('template', 'so_dien_thoai_moi')}
			                        <span class="required">*</span>
			                    </label>
			                    <input id="new_phone" name="new_phone" type="text" class="form-control" placeholder="{__d('template', 'nhap_so_dien_thoai')}">
			                </div>
			                <input type="hidden" name="type" value="phone">
						    
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