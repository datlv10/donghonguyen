<div nh-wrap="shipping-method" class="card bg-white mb-3 py-3 px-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="d-flex align-items-center" >
            	<i class="fa-light fa-truck-fast color-highlight"></i>
            	<div class="pl-2 mb-0 font-weight-bold">
                    {__d('template', 'phuong_thuc_van_chuyen')}
                </div>
            </span>
        </div>
    </div>
    {if !empty($shipping_methods)}
		{foreach from = $shipping_methods key = method_id item = method}
			{assign var = selected_method value = false}
			{if !empty($order_info.shipping_method_id) && $order_info.shipping_method_id == $method_id}
				{assign var = selected_method value = true}
			{/if}

			<div class="swich-change bg-white {if !$method@last}mb-3{/if}">
				<input class="form-check-input" name="shipping_method_id" value="{$method_id}" {if $selected_method}checked="true"{/if} nh-shipping-method="{$method_id}" id="shipping-method-{$method_id}" type="radio">
				<label class="d-flex align-items-center border rounded p-3" for="shipping-method-{$method_id}">
					<div>
						{if !empty($method.name)}
							{$method.name}
						{/if}
						
						<div class="font-weight-normal">
							{__d('template', 'phi_van_chuyen')}: 

							{if !empty($method.fee)}
								{$method.fee|number_format:0:".":","}
								<span class="currency-symbol fs-12">
			                        {CURRENCY_UNIT_DEFAULT}
			                    </span>
							{else}
								0 {CURRENCY_UNIT_DEFAULT}
							{/if}
						</div>

						{if !empty($method.description)}
							<div class="font-weight-normal">
								{$method.description}
							</div>
						{/if}
					</div>

					<div class="ml-auto checked">
						<i class="fa-light fa-check fa-2x"></i>
	                </div>
				</label>
			</div>
	    {/foreach}
	{else}
		<i class="p-15 fs-12">
            {__d('template', 'khong_co_phuong_thuc_nao_duoc_ap_dung')}
        </i>
    {/if}
</div>