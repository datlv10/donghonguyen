{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {__d('template', 'quan_ly_don_hang')}]
	]
])}
{assign var = limit value = $this->Utilities->getParamsByKey('limit')}
{assign var = keyword value = $this->Utilities->getParamsByKey('keyword')}
{assign var = status value = $this->Utilities->getParamsByKey('status')}
{assign var = create_from value = $this->Utilities->getParamsByKey('create_from')}
{assign var = create_to value = $this->Utilities->getParamsByKey('create_to')}
 
<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="h-100 bg-white p-4">
				<form nh-form="list-order" action="/member/order" method="POST" autocomplete="off" class="h-100">
					<div class="row space-5">
						<div class="col-12 col-md-4">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa-lg fa-light fa-magnifying-glass"></i>
										</span>
									</div>
									<input name="keyword" value="{if !empty($keyword)}{$keyword}{/if}" type="text" placeholder="{__d('template', 'ma_don_hang')}" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-12 col-md-4">
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
						</div>
						<div class="col-12 col-md-4">
							<div class="form-group">
								<div class="d-flex">
									{$this->Form->select('group_status', $this->Order->getListStatusGroupOrder(), ['id' => 'group_status', 'empty' => "-- {__d('template', 'trang_thai')} --", 'class' => 'form-control form-control-sm selectpicker input-hover'])}
					                <button nh-btn-action="order-search" type="submit" class="btn btn-dark d-flex align-items-center ml-2">
					                	<i class="fa-lg fa-light fa-magnifying-glass"></i>
					                </button>
				                </div>
			                </div>
						</div>
					</div>
					<div nh-form="table-order" class="list-order-element h-100">
						{$this->element('../Member/list_order_element')}					
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>
{$this->element('../Member/cancel_order_modal')}