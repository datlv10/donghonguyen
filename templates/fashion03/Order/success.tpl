{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {__d('template', 'don_hang_thanh_cong')}]
	]
])}

<div class="container">
	<div class="order-section mb-5">
		<div class="alert alert-success text-center mb-3" role="alert">
		  	{__d('template', 'chuc_mung_quy_khach_da_dat_hang_thanh_cong')}. 
		  	{if !empty($order_info.code)}
		  		<strong>{__d('template', 'ma_don_hang')}: {$order_info.code}</strong>
		  	{/if}
		</div>

		{assign var = contact value = []}
		{if !empty($order_info.contact)}
			{assign var = contact value = $order_info.contact}
		{/if}

		<div class="order-info mb-3 bg-white p-4">
			<div class="order-item">
				<div class="h4 font-weight-bold mb-4">
					{__d('template', 'dia_chi_giao_hang')}
				</div>

				{if !empty($contact.full_name)}
					<div class="mb-2">
						<b>{__d('template', 'ho_va_ten')}: </b>{$contact.full_name}
					</div>
				{/if}

				{if !empty($contact.phone)}
					<div class="mb-2">
						<b>{__d('template', 'so_dien_thoai')}: </b>{$contact.phone}
					</div>
				{/if}

				{if !empty($contact.email)}
					<div class="mb-2">
						<b>{__d('template', 'email')}: </b>{$contact.email}
					</div>
				{/if}

				{if !empty($contact.full_address)}
					<div class="mb-2">
						<b>{__d('template', 'dia_chi')}: </b>{$contact.full_address}
					</div>
				{/if}
				{if !empty($order_info.note)}
					<div class="mb-2">
						<b>{__d('template', 'ghi_chu')}: </b>{$order_info.note}
					</div>
				{/if}
			</div>
		</div>

		<div class="row mx-n2">
			<div class="col-lg-7 col-md-6 px-2">
				<div class="bg-white p-4 mb-3">
					{$this->element('../Order/element_product_info')}
				</div>
			</div>
			<div class="col-lg-5 col-md-6 px-2">
				{$this->element('../Order/element_items')}
			</div>
		</div>
	</div>
</div>