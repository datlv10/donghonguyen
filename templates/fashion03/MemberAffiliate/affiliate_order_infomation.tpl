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
			<div class="bg-white p-4 mb-4">
				<div class="font-weight-bold h4 mb-4">
					{__d('template', 'thong_tin_don_hang')}
				</div>
				<div class="row">
					<div class="col-12 col-sm-6">
						{if !empty($order.code)}
							<p class="mb-2">
								{__d('template', 'ma_don_hang')}: {$order.code}
							</p>
						{/if}

						{if !empty($order.created)}
							<p class="mb-2">
								{__d('template', 'thoi_gian_dat')}: 
								{$this->Utilities->convertIntgerToDateString($order.created)}
							</p>
						{/if}

						{if !empty($order.total)}
							<p class="mb-2">
								{__d('template', 'tong_tien_don_hang')}: 
								{$order.total|number_format:0:".":","}
	                            <span class="currency-symbol">{CURRENCY_UNIT}</span>
							</p>
						{/if}

						{if !empty($order.affiliate_order.profit_value)}
							<p class="mb-2">
								{__d('template', '%_hoa_hong')}: 
								{$order.affiliate_order.profit_value}%
							</p>
						{/if}

						{if !empty($order.affiliate_order.profit_value)}
							<p class="mb-2">
								{__d('template', 'diem_hoa_hong')}: 
								{$order.affiliate_order.profit_money|number_format:0:".":","}
								<span class="currency-symbol">{CURRENCY_UNIT}</span>
							</p>
						{/if}
					</div>		
				</div>
			</div>
			<div class="bg-white p-4 mb-4">
				<div class="font-weight-bold h4 mb-4">
					{__d('template', 'thong_tin_san_pham')}
				</div>
				<table class="table responsive-table mb-0">
					<thead>
				        <tr>
				            <th>{__d('template', 'san_pham')}</th>
				            <th>{__d('template', 'gia')}</th>
				            <th>{__d('template', 'so_luong')}</th>
				            <th class="text-right">{__d('template', 'tien')}</th>
				        </tr>
				    </thead>
					<tbody>
						{if !empty($order.items)}
							{foreach from = $order.items item = item}
								<tr class="cart_item">
						            <th scope="row">
						            	{if !empty($item['images'][0])}
							                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item['images'][0], 50)}"}
							            {else}
							                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
							            {/if}
						                <a href="{$this->Utilities->checkInternalUrl($item.url)}">
						                	<img class="img-fluid mr-10" src="{$url_img}" alt="{if !empty($item.name_extend)}{$item.name_extend}{/if}" />

						                	{if !empty($item.name_extend)}
						                		{$item.name_extend}
						                	{/if}
						                </a>
						            </th>

						            <td data-title="{__d('template', 'gia')}">
						            	<span class="price-amount">
						            		{if !empty($item.price)}
								                <span>
								                	{$item.price|number_format:0:".":","}
								                </span>
							                {/if}
						            		<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						            	</span>
						            </td>

						            <td data-title="{__d('template', 'so_luong')}">
						            	{if !empty($item.quantity)}
							                <span>
							                	{$item.quantity|number_format:0:".":","}
							                </span>
						                {/if}
						            </td>

						            <td data-title="{__d('template', 'tien')}" class="text-right">
						            	<span class="price-amount">
						            		{if !empty($item.total_item)}
								                <span>
								                	{$item.total_item|number_format:0:".":","}
								                </span>
							                {/if}
											<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						                </span>
						            </td>
						        </tr>
							{/foreach}
						{/if}
					</tbody>
					<tfoot>
						{if !empty($order.shipping_fee_customer)}
							<tr>
								<td colspan="3">
									<strong class="fs-14">
										{__d('template', 'phi_van_chuyen')}
									</strong>
								</td>
								<td class="text-right">
									<span class="price-amount">
					            		+ {$order.shipping_fee_customer|number_format:0:".":","}
						            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
					            	</span>
								</td>
							</tr>
						{/if}
						{if !empty($order.total_coupon)}
							<tr>
								<td colspan="3">
									<strong class="fs-14">
										{__d('template', 'phieu_giam_gia')}
									</strong>
								</td>
								<td class="text-right">
									<span class="price-amount">
					            		- {$order.total_coupon|number_format:0:".":","}
						            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
					            	</span>
								</td>
							</tr>
						{/if}
						{if !empty($order.total_affiliate)}
							<tr>
								<td colspan="3">
									<strong class="fs-14">
										{__d('template', 'ma_gioi_thieu')}
									</strong>
								</td>
								<td class="text-right">
									<span class="price-amount">
					            		- {$order.total_affiliate|number_format:0:".":","}
						            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
					            	</span>
								</td>
							</tr>
						{/if}

						{if !empty($order.total_vat)}
							<tr>
								<td colspan="3">
									<strong class="fs-14">
										VAT
									</strong>
								</td>
								<td class="text-right">
									<span class="price-amount">
					            		+ {$order.total_vat|number_format:0:".":","}
						            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
					            	</span>
								</td>
							</tr>
						{/if}
						<tr class="color-hover fs-14 bg-gray">
							<td colspan="3"><b>{__d('template', 'tong_tien')}</b></td>
							<td class="text-right">
								<b>
									<span class="price-amount">
					            		{if !empty($order.total)}
					            			{$order.total|number_format:0:".":","}
					            		{/if}
						            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
					            	</span>
								</b>
							</td>
						</tr>
						{if !empty($order.point_promotion_paid) || !empty($order.point_paid)}
							{if !empty($order.point_promotion_paid)}
								<tr>
									<td colspan="3">
										<strong class="fs-14">
											{__d('template', 'thanh_toan_bang_diem_khuyen_mai')}
										</strong>
									</td>
									<td class="text-right">
										<span class="price-amount">
						            		- {$order.point_promotion_paid|number_format:0:".":","}
							            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						            	</span>
									</td>
								</tr>
							{/if}
							{if !empty($order.point_paid)}
								<tr>
									<td colspan="3">
										<strong class="fs-14">
											{__d('template', 'thanh_toan_bang_diem_vi')}
										</strong>
									</td>
									<td class="text-right">
										<span class="price-amount">
						            		- {$order.point_paid|number_format:0:".":","}
							            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						            	</span>
									</td>
								</tr>
							{/if}
							
							{if !empty($order.debt)}
								<tr>
									<td colspan="3">
										<strong class="fs-14 color-hover">
											{__d('template', 'con_phai_thanh_toan')}
										</strong>
									</td>
									<td class="text-right">
										<span class="price-amount color-hover">
						            		{$order.debt|number_format:0:".":","}
							            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						            	</span>
									</td>
								</tr>
							{/if}

							{if !empty($order.paid)}
								<tr>
									<td colspan="3">
										<strong class="fs-14 text-success">
											{__d('template', 'da_thanh_toan')}
										</strong>
									</td>
									<td class="text-right">
										<span class="price-amount text-success">
						            		{$order.paid|number_format:0:".":","}
							            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
						            	</span>
									</td>
								</tr>
							{/if}
						{/if}
					</tfoot>
				</table>
			</div>
		</div>
	</div>	
</div>