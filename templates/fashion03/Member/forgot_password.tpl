{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-xl-6 col-lg-8 col-md-10 col-12">
			<div class="rounded shadow bg-white mt-5">
			    <div class="modal-title h2 text-uppercase text-center font-weight-bold pt-5">
				    {__d('template', 'quen_mat_khau')}
				</div>
				<form nh-form="forgot-password" action="/member/ajax-forgot-password" method="post" autocomplete="off">
					<div class="p-5">
						<div class="form-group select-items d-flex align-items-center">
	                       	<i class="fa-lg fa-light fa-envelope select-items--icon"></i>
	                        <div>
	                            <div class="font-weight-bold">
	                                {__d('template', 'khoi_phuc_mat_khau_qua_email')}
	                            </div>
	                            <div>
	                            	{__d('template', 'ma_se_gui_qua_email_ban_dang_ky_de_thay_doi_mat_khau')}
	                            </div>
	                        </div>
	                        <i class="fa-lg fa-light fa-circle-check ml-auto"></i>
	                   	</div>
					    <div class="form-group mb-5">
					        <input name="email" type="text" class="form-control required" placeholder="{__d('template', 'email')} *">
					    </div>
					    
					    <div nh-btn-action="submit" class="btn btn-submit w-100">
					        {__d('template', 'xac_nhan')}
					    </div>
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>

