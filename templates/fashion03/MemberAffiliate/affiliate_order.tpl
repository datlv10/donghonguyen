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
				<form nh-form="list-order" action="/member/affiliate/order" method="POST" autocomplete="off" class="h-100">
					<div class="mb-3">
						<div class="d-flex justify-content-between form-inline">
							<div class="form-group">
								<div class="input-group">
									<input nh-date data-date-end-date="0d" type="text" placeholder="{__d('template', 'tu_ngay')}" class="form-control" name="create_from" value="{if !empty($create_from)}{$create_from}{/if}">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="fa-lg fa-light fa-grip-dots"></i>
										</span>
									</div>
									<input nh-date data-date-end-date="0d" type="text" placeholder="{__d('template', 'den_ngay')}" class="form-control" name="create_to" value="{if !empty($create_to)}{$create_to}{/if}" style="margin-left: -1px;">
								</div>
							</div>
						
							<div class="d-flex">
								{$this->Form->select('group_status', $this->Order->getListStatusGroupOrder(), ['id' => 'group_status', 'empty' => "-- {__d('template', 'trang_thai')} --", 'class' => 'form-control form-control-sm selectpicker mr-10'])}
				                <button nh-btn-action="order-search" type="submit" class="btn btn-dark d-flex align-items-center ml-2">
				                	<i class="fa-lg fa-light fa-magnifying-glass"></i>
				                </button>
			                </div>
						</div>
					</div>
					
					<div nh-form="table-order">
						{$this->element('../MemberAffiliate/list_affiliate_order_element')}					
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>