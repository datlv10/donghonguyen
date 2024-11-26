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

						<a href="/member/wallet/give-point" class="btn btn-sm btn-submit-1 px-3 m-1" nh-money-send>
							<i class="fa-lg fa-light fa-money-bill-transfer pr-2"></i>
							{__d('template', 'chuyen_diem')}
						</a>
					</div>
				</div>

				<div class="history-point pb-5">
					<ul class="nav nav-tabs row mb-0 mx-0">
					  	<li class="col px-0">
					  		<a class="font-weight-bold d-block active" data-toggle="tab" nh-wallet-redirect href="javascript:;">{__d('template', 'tat_ca_lich_su')}</a>
					  	</li>
					  	<li class="col px-0">
					  		<a class="font-weight-bold d-block" data-toggle="tab" nh-wallet-redirect="1" href="javascript:;">{__d('template', 'da_nhan')}</a>
					  	</li>
					  	<li class="col px-0">
					  		<a class="font-weight-bold d-block" data-toggle="tab" nh-wallet-redirect="0" href="javascript:;">{__d('template', 'da_dung')}</a>
					  	</li>
					</ul>
					<div class="tab-content">
					  	<div id="transaction_history" class="tab-pane fade show active">
						    {$this->element('../MemberWallet/element_wallet',['history'=> $history])}
					  	</div>
					</div>
				</div>	
			</div>
		</div>
	</div>	
</div>