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
			<div class="h-100 bg-white p-4">
				<div class="d-flex align-items-center justify-content-between mb-5">
					<div>
						<p class="mb-2">
						    {__d('template', 'diem_vi')}: 
							<span class="h5 font-weight-bold color-highlight">
						    	{if !empty($point_info.point)}
						    		{$point_info.point|number_format:0:".":","}
						    	{else}
						    		0
						    	{/if}
						    </span>
						</p>

						<p class="mb-2">
						    {__d('template', 'diem_thuong')}: 
							<span class="h5 font-weight-bold">
						    	{if !empty($point_info.point_promotion)}
						    		{$point_info.point_promotion|number_format:0:".":","}
						    	{else}
						    		0
						    	{/if}
						    </span>

						    {if !empty($point_info.expiration_time)}
								<small class="text-muted">
									({__d('template', 'thoi_han_su_dung')}: 
									<span class="color-highlight">
										{$this->Utilities->convertIntgerToDateString($point_info.expiration_time)})
									</span>
								</small>
							{/if}
						</p>
					</div>

					<div class="align-self-end">
						<a href="/member/wallet/buy-point" class="btn btn-sm btn-submit-1 px-3 m-1" nh-wallet>
							<i class="fa-lg fa-light fa-money-bill-wave pr-2"></i>
							{__d('template', 'nap_diem')}
						</a>
					</div>
				</div>

				<form nh-form="give-point" id="give-point" method="post" autocomplete="off">
				    <div class="form-group">
				        <label class="font-weight-bold" for="customer_code">
				            {__d('template', 'ma_nguoi_nhan')}
				            <span class="required">*</span>
				        </label>
					    <input name="customer_code" type="text" class="form-control" placeholder="{__d('template', 'vi_du')}: CUS00000001">
				    </div>

				    <div class="form-group">
				        <label class="font-weight-bold" for="point">
				            {__d('template', 'so_diem')}
				            <span class="required">*</span>
				        </label>
				        <input name="point" nh-point-max="{if !empty($point_info.point)}{$point_info.point}{/if}" type="text" class="form-control number-input" placeholder="{__d('template', 'so_diem')}" autocomplete="off">

				        <small class="text-muted">
	                        {__d('template', 'so_diem_phai_nho_hon_diem_trong_vi')}
	                    </small>
				    </div>

			    	<div class="row">
			    		<div class="col-12 col-md-6">
			    			{if !empty($member.email)}
								<div class="entry-choose-verify mb-3">
									<input name="type_verify" id="verify-email" checked="checked" value="email" class="form-check-input" type="radio" >
						            <label class="inner-verify" for="verify-email">
						                <i class="fa-lg fa-light fa-envelope icon-left"></i>
						                <div>
						                    <p class="mb-0">
						                        <b>{__d('template', 'lay_ma_xac_nhan_qua_email')}</b><br />
						                        <span class="color-highlight">
						                    		{__d('template', 'email')}: {$member.email}
						                    	</span>
						                    </p>
						                </div>
						                <i class="fa-lg fa-light fa-circle-check icon-right ml-auto"></i>
						            </label>
								</div>
							{/if}
			    		</div>
			    		<div class="col-12 col-md-6">
			    			{assign var = sms_usage value = $this->Setting->checkSmsBrandUsage()}
			    			{if !empty($member.phone) && $sms_usage}
								<div class="entry-choose-verify mb-3">
									<input name="type_verify" id="verify-phone" {if empty($member.email)}checked="checked"{/if} value="phone" class="form-check-input" type="radio" >
						            <label class="inner-verify" for="verify-phone">
						                <i class="fa-lg fa-light fa-phone-rotary icon-left"></i>
						                <div class="text-forgot">
						                    <p class="mb-0">
						                        <b>{__d('template', 'lay_ma_xac_nhan_qua_dien_thoai')}</b><br />
						                        <span class="color-highlight">
						                    		{__d('template', 'so_dien_thoai')}: {$member.phone}
						                    	</span>
						                    </p>
						                </div>
						                <i class="fa-lg fa-light fa-circle-check icon-right ml-auto"></i>
						            </label>
								</div>
							{/if}
			    		</div>
			    	</div>
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								<div class="input-group">
									<input nh-otp="verification" name="code" type="text" placeholder="{__d('template', 'vui_long_nhap_ma_xac_nhan')}" class="form-control" />
									<div class="input-group-append">
								        <span nh-btn-action="get-verify" class="input-group-text input-group-main cursor-pointer">
											{__d('template', 'nhan_ma')}
								            <small class="ml-2" nh-countdown></small>
										</span>
								  	</div>
								</div>
							</div>
							<span nh-btn-action="submit" class="btn btn-submit w-100" >
						        {__d('template', 'xac_nhan')}
						    </span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>