{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {__d('template', 'thanh_toan')}]
	]
])}
{assign var = message value = $this->Utilities->getParamsByKey('message')}
<div class="container">
	<div class="payment-section mb-5">
		<div class="row mx-n2">
			<div class="col-md-7 px-2">
				<div class="bg-white p-4 mb-3">
					{if !empty($message)}
						<div class="alert alert-danger" role="alert">
						  	{$message}
						</div>
					{/if}

					{$this->element('../Order/element_product_info')}
				</div>
			    <div class="order-review bg-white p-4 mb-3">
					<div class="entry-order-review mb-0 ">
						<div class="h4 font-weight-bold mb-4">
							{__d('template', 'thong_tin_khach_hang')}
						</div>
						{$this->element('../Order/element_contact')}
					</div>			
				</div>

				<div class="payment-method mb-4 bg-white p-4">
					<form id="form-checkout" action="" method="post">
						<div class="d-flex align-content-stretch flex-wrap ">
							{if !empty($payment_gateway)}
								<ul class="nav w-100 mb-4" role="tablist">
									{foreach from = $payment_gateway item = $gateway key = code name = each_nav}
									    <li class="nav-item clearfix mb-3">
									        <a nh-gateway-item="{$code}" href="#{$code}" class="nav-link color-black d-flex align-items-center border p-3  {if $smarty.foreach.each_nav.first}active{/if}" data-toggle="tab" role="tab">
									        	<div class="inner-icon position-relative mr-3 ">
									        		<img class="img-fluid object-contain" src="{URL_TEMPLATE}assets/img/payment/{$code}.png" alt="{$code}" /> 
									        	</div>
									        	<div class="inner-label text-left">
									        		{if !empty($gateway.name)}
									        			{$gateway.name|truncate:50:" ..."}
									        		{/if}
									        		
									        		{if !empty($gateway.content)}
	    										        <div class="content-payment fs-14 font-weight-normal">
	    								        			{$gateway.content|truncate:100:" ..."}
	    								        		</div>
	    							        		{/if}
									        	</div>
									        	
									    	</a>
									    </li>
								    {/foreach}
								</ul>

								<div class="tab-content w-100">
									{foreach from = $payment_gateway item = $gateway key = code name = each_tab}
									    <div id="{$code}" class="tab-pane {if $smarty.foreach.each_tab.first}active{/if}" role="tabpanel">
									    	{if $code == {BANK}}
									    		{assign var = list_bank value = []}
									    		{if !empty($gateway.config)}
									    			{assign var = list_bank value = $gateway.config}
									    		{/if}

									    		{if !empty($list_bank)}
												    <div class="h4 font-weight-bold mb-4">
												    	{__d('template', 'tai_khoan_ngan_hang')}
												    </div>

											    	<div class="entry-bank mb-30">
											    		{foreach from = $list_bank item = bank key = key name = each_bank}
													        <table class="table w-100 mb-15">
															    <tbody>
															        <tr>
															            <td>{__d('template', 'ten_ngan_hang')}</td>
															            <td>
															            	{if !empty($bank.bank_name)}
															            		<b>{$bank.bank_name}</b>
															            	{/if}
															            </td>
															        </tr>

															        {if !empty($bank.bank_branch)}
																        <tr>
																            <td>{__d('template', 'chi_nhanh')}</td>
																            <td>
																            	<b>{$bank.bank_branch}</b>
																            </td>
																        </tr>
															        {/if}

															        <tr>
															            <td>{__d('template', 'chu_tai_khoan')}</td>
															            <td>
															            	{if !empty($bank.account_holder)}
															            		<b>{$bank.account_holder}</b>
															            	{/if}
															            </td>
															        </tr>

															        <tr>
															            <td>{__d('template', 'so_tai_khoan')}</td>
															            <td>
															            	{if !empty($bank.account_number)}
															            		<b>{$bank.account_number}</b>
															            	{/if}
															            </td>
															        </tr>
															    </tbody>
															</table>
														{/foreach}
											    	</div>
										    	{/if}

										    	{if !empty($order_info.code)}
										    		<div class="h4 font-weight-bold mb-4">
												    	{__d('template', 'ma_giao_dich')}: <span class="text-danger">{$order_info.code}</span>
												    </div>
										    	{/if}
									    	{/if}
	                                        
							        		<div class="checkout-payment text-lg-left text-center pt-15 p-lg-0 ">
												<span nh-btn-action="checkout" class="btn btn-submit w-100">
							                        {__d('template', 'thanh_toan_ngay')}
							                    </span>
											</div>
									    </div>
								    {/foreach}
								</div>
							{/if}
						</div>
						<input name="payment_gateway" value="" type="hidden">
						<input name="code" value="{if !empty($order_info.code)}{$order_info.code}{/if}" type="hidden">
					</form>
				</div>
			</div>

			<div class="col-md-5 px-2">
				<div class="order-review mb-0">
					<div class="entry-order-review ">
						{$this->element('../Order/element_items')}
					</div>
				</div>	
			</div>
		</div>
	</div>	
</div>