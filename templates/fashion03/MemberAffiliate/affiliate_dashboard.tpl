{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
{strip}
<script src="{URL_TEMPLATE}assets/lib/chartjs/chart.js"></script>
<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>

		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="h-100 bg-white p-4">
				<div class="row">
					<div class="col-6">
						<div class="font-weight-bold text-info h5 mb-4">
							{__d('template', 'don_hang')}
						</div>

						<div class="row">
							<div class="col-12 col-lg-6">
								<div class="bg-light p-4 rounded-lg mb-5">
									<img src="{URL_TEMPLATE}assets/media/affilate/3dcube.svg" alt="{__d('template', 'don_hang_hoan_thanh')}" class="img-fluid image-48x48">
									<div class="mt-4">
										{__d('template', 'don_hang_hoan_thanh')}
									</div>

									<div>
										<span class="h2 font-weight-bold pr-1">
											{if !empty($statistical.all_total_order_success)}
												{$statistical.all_total_order_success|number_format:0:".":","} 
											{else}
												0
											{/if}
										</span>
										<span>vnd</span>
									</div>
								</div>
							</div>

							<div class="col-12 col-lg-6">
								<div class="bg-light p-4 rounded-lg mb-5">
									<img src="{URL_TEMPLATE}assets/media/affilate/clipboard-close.svg" alt="{__d('template', 'don_hang_that_bai')}" class="img-fluid image-48x48">
									<div class="mt-4">
										{__d('template', 'don_hang_that_bai')}
									</div>

									<div>
										<span class="h2 font-weight-bold pr-1">
											{if !empty($statistical.all_total_order_failed)}
												{$statistical.all_total_order_failed|number_format:0:".":","} 
											{else}
												0
											{/if}
										</span>
										<span>vnd</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-6">
						<div class="font-weight-bold text-info h5 mb-4">
							{__d('template', 'thu_hang')}
						</div>
						
						<div class="row">
							<div class="col-12 col-lg-6">
								<div class="bg-light p-4 rounded-lg mb-5">
									{assign var = rank_info value = []}
									{if !empty($statistical.rank)}
										{$rank_info = $statistical.rank}
									{/if}
									
									{assign var = image_rank value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
									{if !empty($rank_info.image)}
										{assign var = image_rank value = "{CDN_URL}{$rank_info.image}"}
									{/if}

									<img src="{$image_rank}" alt="{__d('template', 'thu_hang')}" class="img-fluid image-48x48">

					                <div class="mt-4">
										{__d('template', 'hien_tai_cua_ban')}:
										{if !empty($rank_info.name)}
											{$rank_info.name}
										{/if}
					                </div>
					                
									<div>
										<span class="h2 font-weight-bold pr-1">
											{if !empty($rank_info.profit)}
												{$rank_info.profit}
											{/if}
										</span>%
									</div>
								</div>
							</div>

							<div class="col-12 col-lg-6">
								<div class="bg-light p-4 rounded-lg mb-5">
									<img src="{URL_TEMPLATE}assets/media/affilate/dollar-square.svg" alt="{__d('template', 'tong_hoa_hong_dat')}" class="img-fluid">

									<div class="mt-4">
										{__d('template', 'tong_hoa_hong_dat')}
					                </div>

									<div>
										<span class="h2 font-weight-bold pr-1">
											{if !empty($statistical.all_profit_point)}
												{$statistical.all_profit_point|number_format:0:".":","}
											{else}
												0
											{/if}
										</span>
										<span>
											{__d('template', 'diem')}
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="d-flex justify-content-between align-items-center mb-4">
					<div class="font-weight-bold text-warning h5">
						{__d('template', 'du_lieu_thang')}
					</div>

					{if !empty($list_month)}
						<div class="dropdown">
							{assign var = this_month value = $smarty.now|date_format:"%m"}
							<a class="btn btn-sm btn-secondary dropdown-toggle rounded" href="javascript:;" role="button" data-toggle="dropdown">
								{if !empty($list_month[$this_month])}
									{$list_month[$this_month]}
								{else}
									{__d('template', 'thang_1')}
								{/if}
							</a>

							<div class="dropdown-menu dropdown-menu-right">
								{foreach from = $list_month key = key item = month}
									<a filter-month="{$key}" class="dropdown-item" href="javascript:;">
										{$month}
									</a>
								{/foreach}
							</div>
						</div>
					{/if}
				</div>

				<div id="wrap-dashboard-statistic-element">
					{$this->element('../MemberAffiliate/load_statistic_month', ['statistical' => $statistical])}
				</div>

				
				<div class="mb-4">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<div class="font-weight-bold text-primary h5">
							{__d('template', 'bieu_do_thang')}
						</div>

						{if !empty($list_month)}
							<div id="dropdown-month-chart-profit" class="dropdown">
								{assign var = this_month value = $smarty.now|date_format:"%m"}
								<a class="btn btn-sm btn-secondary dropdown-toggle rounded" href="javascript:;" role="button" data-toggle="dropdown">
									{if !empty($list_month[$this_month])}
										{$list_month[$this_month]}
									{else}
										{__d('template', 'thang_1')}
									{/if}
								</a>

								<div class="dropdown-menu dropdown-menu-right">
									{foreach from = $list_month key = key item = month}
										<a chart-month="{$key}" class="dropdown-item" href="javascript:;">
											{$month}
										</a>
									{/foreach}
								</div>
							</div>
						{/if}
					</div>

					<div id="wrap-load-chart-profit">
						{$this->element('../MemberAffiliate/chart_profit', ['chart_data' => $chart_data])}
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
{/strip}