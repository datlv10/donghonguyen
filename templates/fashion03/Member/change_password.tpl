{$this->element('breadcrumb', [
	'list_url' => [
		['title' =>  $title_for_layout]
	]
])}

<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="h-100 bg-white p-4">
				<form nh-form="change-password" action="/member/ajax-change-password" method="post" autocomplete="off">
					<div class="row">
						<div class="col-md-6 col-12">
							<div class="form-group">
						        <label for="old_password">
						            {__d('template', 'mat_khau')} 
						            <span class="required">*</span>
						        </label>
						        <input id="old_password" name="old_password" type="password" class="form-control">
						    </div>
						</div>
					</div>
					
				    <div class="row">
						<div class="col-md-6 col-12">
							<div class="form-group">
						        <label for="new_password">
						            {__d('template', 'mat_khau_moi')} 
						            <span class="required">*</span>
						        </label>
						        <input id="new_password" name="new_password" type="password" class="form-control">
						    </div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-12">
							<div class="form-group">
						        <label for="re_password">
						            {__d('template', 'nhap_lai_mat_khau_moi')} 
						            <span class="required">*</span>
						        </label>
						        <input id="re_password" name="re_password" type="password" class="form-control">
						    </div>
						</div>
					</div>

				    <div class="form-group mt-3">
				        <span nh-btn-action="submit" class="btn btn-submit">
				            {__d('template', 'cap_nhat')}
				        </span>
				    </div>
				</form>
			</div>
		</div>
	</div>	
</div>