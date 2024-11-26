<div class="form-group">
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
    
    {assign var = sms_usage value = $this->Setting->checkSmsBrandUsage()}
	{if !empty($member.phone) && $sms_usage}
		<div class="entry-choose-verify mb-3">
			<input name="type_verify" id="verify-phone" {if empty($member.email)}checked="checked"{/if} value="phone" class="form-check-input" type="radio" >
            <label class="inner-verify" for="verify-phone">
                <i class="fa-lg fa-light fa-phone-rotary icon-left"></i>
                <div>
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
	<div class="form-group">
        <span nh-btn-action="get-verify" class="btn btn-submit w-100">
            {__d('template', 'nhan_ma')}
            <small class="ml-2" nh-countdown></small>
        </span>
    </div>
</div>

<div class="form-group position-relative ">
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