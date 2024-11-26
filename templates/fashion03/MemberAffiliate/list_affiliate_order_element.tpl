{if !empty($affiliate_order)}
	<table class="table responsive-table mb-5">
		<thead>
			<tr>
				<th class="text-center">{__d('template', 'don_hang')}</th>
				<th>{__d('template', 'tong_tien_don_hang')}</th>
				<th>{__d('template', '%_hoa_hong')}</th>
				<th>{__d('template', 'diem_hoa_hong')}</th>
				<th>{__d('template', 'trang_thai')}</th>
			</tr>
		</thead>
			<tbody>
				{foreach from = $affiliate_order item = item}
					<tr>
				        <td scope="row">
				        	{if !empty($item.code)}
				        		<b>
				        			<a href="/member/affiliate/order-info/{$item.code}">{$item.code}</a>
				        		</b>
				        	{/if}
				        	<br/>
				        	{if !empty($item.created)}
								<i class="fs-12">
									{$this->Utilities->convertIntgerToDateTimeString($item.created)}
								</i>
							{/if}
				        </td>

				        <td data-title="{__d('template', 'tong_tien_don_hang')}">
				        	{if !empty($item.total)}
								{$item.total|number_format:0:".":","} {CURRENCY_UNIT}
							{/if}
				        </td>

				        <td data-title="{__d('template', '%_hoa_hong')}">
				        	{if !empty($item.profit_value)}
					        	<b>
					        		{$item.profit_value}%
					        	</b>                                
                            {/if}
				        </td>

				        <td data-title="{__d('template', 'diem_hoa_hong')}">
				        	<b class="text-success">
				        		{if !empty($item.profit_point)}
									{$item.profit_point|number_format:0:".":","} {__d('template', 'diem')}
								{/if}

								{if !empty($item.profit_money)}
									<br>
									= {$item.profit_money|number_format:0:".":","} đ
								{/if}
				        	</b>
				        </td>

				        <td data-title="{__d('template', 'trang_thai')}">
				        	{if !empty($item.status)}
								{assign var = list_status_order value = $this->Order->getListStatusOrderTemplate()}
								<span {if !empty($list_status_order[$item.status]['class'])}
									class="{$list_status_order[$item.status]['class']} w-100"
								{/if}>
								{if !empty($list_status_order[$item.status]['title'])}
									{$list_status_order[$item.status]['title']}
								{/if}
								</span>
							{/if}
				        </td>
				    </tr>
				{/foreach}
			</tbody>
	</table>
	{$this->element('../Member/pagination', ['pagination' => $pagination])}
{else}
	<div class="position-relative" style="padding-top: 66.66%;">
		<div class="pos-center text-center">
			<img src="{URL_TEMPLATE}assets/img/icon/empty-order.svg" class="img-fluid" alt="{__d('template', 'hien_chua_co_don_hang')}">
			<p class="mb-0 mt-10">
			  	{__d('template', 'hien_chua_co_don_hang')}
			</p>
		</div>
	</div>
{/if}