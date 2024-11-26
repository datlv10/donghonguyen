<div nh-order-info>
	{$this->element('breadcrumb', [
		'list_url' => [
			['title' => {__d('template', 'thong_tin_don_hang')}]
		]
	])}
	<div class="container">
		<div class="checkout-section mb-5">
			<form id="order-info" method="post">
				<div class="row mx-n2">
					<div id="order-info-left" class="col-lg-7 col-md-6 px-2">
						{$this->element('../Order/element_order_info_left')}
					</div>

					<div id="order-info-right" class="col-lg-5 col-md-6 px-2">
						{$this->element('../Order/element_order_info_right')}
					</div>
				</div>
			</form>
		</div>
	</div>

	{$this->element('../Order/list_coupon_modal')}
</div>

{if !empty($member_info)}
	{$this->element('../Order/update_address_modal')}
{/if}