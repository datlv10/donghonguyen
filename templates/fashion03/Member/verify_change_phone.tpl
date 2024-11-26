{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
{assign var = phone value = $this->Utilities->getParamsByKey('phone')}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6 col-12">
			<div class="bg-white shadow p-4">
				<div class="h4 font-weight-bold mb-5">
			       {__d('template', 'thay_doi_so_dien_thoai')}
			    </div>
				<form nh-form="vertify-change-phone" action="/member/ajax-vertify-change-phone" method="post" autocomplete="off">
				    <div class="form-group {if !empty($phone)}d-none{/if}">
				        <label for="phone">
				            {__d('template', 'so_dien_thoai')}: 
				            <span class="required">*</span>
				        </label>

				        <input name="phone" type="text" class="form-control" value="{if !empty($phone)}{$phone}{/if}">
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
	                    <label for="new_phone">
	                        {__d('template', 'so_dien_thoai_moi')}
	                        <span class="required">*</span>
	                    </label>
	                    <input id="new_phone" name="new_phone" type="text" class="form-control"> 
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