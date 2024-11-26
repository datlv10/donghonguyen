<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>

		<div class="col-12 col-md-9 col-lg-9 px-2">
			{if !empty($point_history)}
				<div class="row justify-content-center">
					<div class="col-xl-6 col-lg-8 col-md-10 col-12">
						<div class="notification text-center my-5">
							<div class="icon">
								<img src="{URL_TEMPLATE}/assets/img/member/check.png" alt="{__d('template', 'thanh_cong')}" width="64" height="64" />
								<p class="mb-0 mt-3">{__d('template', 'nap_diem_thanh_cong')}</p>
								<p>
									{__d('template', 'ban_da_nap_thanh_cong')}
									<span class="text-success font-weight-bold">
										{if !empty($point_history.point)}
											+{$point_history.point|number_format:0:".":","}
										{/if}
									</span>
									{__d('template', 'diem_vao_vi')}
								</p>
							</div>
							<div class="info text-left">
								<ul class="list-unstyled">
									<li class="d-flex justify-content-between mb-2 pb-2 border-bottom">
										<span>{__d('template', 'ma_giao_dich')}</span>
										<span class="text-primary">
											{if !empty($info_payment.code)}
												{$info_payment.code}
											{/if}
										</span>
									</li>
									<li class="d-flex justify-content-between">
										<span>{__d('template', 'thanh_toan')}</span>
										<span class="text-success">
											{if !empty($info_payment.amount)}
												{$info_payment.amount|number_format:0:".":","}{CURRENCY_UNIT}
											{/if}
										</span>
									</li>
								</ul>
							</div>
							<div class="action pt-5">
								<a href="/member/wallet" class="btn btn-submit mr-3">
									{__d('template', 'vi_cua_ban')}
								</a>
								<a href="/member/wallet/buy-point" class="btn btn-submit-1">
									{__d('template', 'giao_dich_moi')}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>	
</div>