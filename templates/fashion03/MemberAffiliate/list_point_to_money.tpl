{assign var = plugins value = $this->Setting->getListPlugins()}

{if !empty($plugins.affiliate)}
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
					<form nh-form="list-order" action="/member/affiliate/list-point-tomoney" method="POST" autocomplete="off" class="h-100">
						<div class="d-flex justify-content-between align-items-center mb-4">
							<div class="h5 font-weight-bold">
								{__d("template", "lich_su_rut_tien")}
							</div>
							<div class="btn-add-member text-center">
			                    <a nh-affiliate="point-tomoney" href="javascript:;" class="btn btn-sm btn-submit-1 px-3 m-1">
			                    	<i class="fa-light fa-money-from-bracket mr-2"></i>
			                    	{__d('template', 'rut_tien')}
			                    </a>
			                </div>
						</div>
					
						<div nh-form="table-order" class="rounded bg-white mb-10">
						    {$this->element('../MemberAffiliate/list_point_to_money_element')}
						</div>
					</form>
				</div>
			</div>
		</div>	
	</div>
	{$this->element('../MemberAffiliate/point_tomoney_modal')}
{/if}
