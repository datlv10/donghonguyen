{assign var = point_to_money value = 1}
{if !empty($config_point.point_to_money)}
	{assign var = point_to_money value = $config_point.point_to_money}
{/if}

{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

{assign var = message value = $this->Utilities->getParamsByKey('message')}
{assign var = list_amout value = [
    50000,
    100000,
    200000,
    500000,
    1000000,
    5000000
]}

<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>

		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="h-100 bg-white p-4">
				{if !empty($message)}
					<div class="alert alert-danger" role="alert">
					  	{$message}
					</div>
				{/if}
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
				</div>

				<div class="money-send">
					<form id="buy-point" method="post" autocomplete="off">
						<label class="font-weight-bold mb-3">
				            {__d('template', 'so_tien_nap')}
				            <span class="required">*</span>
				            <small class="text-muted">
				            	{__d('template', 'quy_doi')}: 1 {__d('template', 'diem')} = {$point_to_money|number_format:0:".":","} {CURRENCY_UNIT}
							</small>
				        </label>
				        {if !empty($list_amout)}
							<div class="row mx-n2">
								{foreach from = $list_amout key = key item = amount}
									{math assign = buy_point equation = "x/y" x = $amount y = $point_to_money}
									<div class="col-lg-3 col-6 px-2">
										<div class="form-group">
											<div class="inner-buy-point position-relative mb-20">

					                            <input name="point" id="{$key}" {if $amount@first}checked="true"{/if} value="{$buy_point}" class="form-check-input" type="radio" >
					                            <label class="form-check-label" for="{$key}">
					                                {$amount|number_format:0:".":","}
					                                <span>
					                                    {CURRENCY_UNIT}
					                                </span>
					                                <div>&#8776; {$buy_point|number_format:0:".":","} {__d('template', 'diem')}</div>
					                            </label>
					                        </div>
				                        </div>
									</div>
								{/foreach} 
							</div>
						{/if}
					    <div class="form-group">
					        <label class="font-weight-bold mb-3">
					            {__d('template', 'cong_thanh_toan')}
					            <span class="required">*</span>
					        </label>

						    <div class="d-flex align-content-stretch flex-wrap payment-method">
								{if !empty($payment_gateway)}
									<ul class="nav w-100 payment_gateways" role="tablist">
										{foreach from = $payment_gateway item = $gateway key = code name = each_nav}
										    <li class="nav-item clearfix mb-3">
										        <a href="javascript:;" choose-payments class="nav-link cursor-pointer color-black d-flex align-items-center border px-4" code="{$code}">
										        	<div class="inner-icon position-relative mr-4">
										        		<img class="img-fluid object-contain" src="{URL_TEMPLATE}assets/img/payment/{$code}.png" alt="{$code}" /> 
										        	</div>

										        	<div class="inner-label text-left">
										        		{if !empty($gateway.name)}
										        			{$gateway.name|truncate:50:" ..."}
										        		{/if}
										        	</div>
										    	</a>
										    </li>
									    {/foreach}
									</ul>
								{else}
									<i>
										{__d('template', 'chua_co_cong_thanh_toan_nao_duoc_cau_hinh')}
									</i>
								{/if}
							</div>
					    </div>

					    <input name="payment_gateway" value="" type="hidden">

					    <span nh-btn-action="submit" class="btn text-uppercase btn-submit btn-user w-100 mb-10" >
					        {__d('template', 'xac_nhan')}
					    </span>
					</form>
				</div>
			</div>
		</div>
	</div>	
</div>