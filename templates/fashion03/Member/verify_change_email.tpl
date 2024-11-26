{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
{assign var = email value = $this->Utilities->getParamsByKey('email')}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6 col-12">
			<div class="bg-white shadow p-4">
				<div class="h4 font-weight-bold mb-5">
			       {__d('template', 'thay_doi_email')}
			    </div>
				<form nh-form="vertify-change-email" action="/member/ajax-vertify-change-email" method="post" autocomplete="off">
				    <div class="form-group {if !empty($email)}d-none{/if}">
				        <label for="email">
				            {__d('template', 'email')}: 
				            <span class="required">*</span>
				        </label>
				        <input name="email" type="text" class="form-control" value="{if !empty($email)}{$email}{/if}">
				    </div>

                    <div class="form-group position-relative">
					    <label>
					        {__d('template', 'ma_xac_nhan')}: 
					        <span class="required">*</span>
					    </label>
					    <div class="input-opt d-flex align-items-center justify-content-between">
					        <input nh-otp="input" type="text" maxlength="1" />
					        <input nh-otp="input" type="text" maxlength="1" />
					        <input nh-otp="input" type="text" maxlength="1" />
					        <input nh-otp="input" type="text" maxlength="1" />
					        <input nh-otp="input" type="text" maxlength="1" />
					    </div>
					    <input nh-otp="verification" name="code" type="hidden"/>
					</div>

	                <div class="form-group">
	                    <label for="new_email">
	                        {__d('template', 'email_moi')}
	                        <span class="required">*</span>
	                    </label>
	                    <input id="new_email" name="new_email" type="text" class="form-control">
	                </div>
				    
				    <div class="form-group">
				        <span nh-btn-action="submit" class="btn btn-submit w-100">
				            {__d('template', 'xac_nhan')}
				        </span>
				    </div>
				    <div nh-btn-action="resend-verify" class="btn btn-submit-1 w-100">
				        {__d('template', 'gui_lai_ma_xac_nhan')}?
				        <span class="ml-1" nh-countdown></span>
				    </div>
				</form>
			</div>
		</div>
	</div>	
</div>