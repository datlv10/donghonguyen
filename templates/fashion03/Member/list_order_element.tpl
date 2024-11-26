{if !empty($order)}
	<table class="table responsive-table mb-30">
		<thead>
			<tr>
				<th>{__d('template', 'ma_don_hang')}</th>
				<th>{__d('template', 'tien')}</th>
				<th>{__d('template', 'ngay_dat_hang')}</th>
				<th>{__d('template', 'trang_thai')}</th>
				<th style="width: 75px"></th>
			</tr>
		</thead>
		<tbody>
			{foreach from = $order item = item}
				<tr>
			        <th scope="row">
			        	<a href="/member/order/detail{if !empty($item.code)}/{$item.code}{/if}">
			        		{if !empty($item.code)}{$item.code}{/if}
			        	</a>
			        </th>
                   
			        <td data-title="{__d('template', 'tien')}">
			        	{if !empty($item.total)}
							{$item.total|number_format:0:".":","} {CURRENCY_UNIT}
						{/if}
			    	</td>

			        <td data-title="{__d('template', 'ngay_dat_hang')}">
			        	{if !empty($item.created)}
							{$this->Utilities->convertIntgerToDateTimeString($item.created)}
			        	
						{/if}
			        </td>

			        <td data-title="{__d('template', 'trang_thai')}">
			        	{if !empty($item.status)}
							{assign var = list_status_order value = $this->Order->getListStatusOrderTemplate()}
							<span {if !empty($list_status_order[$item.status]['class'])}
								class="{$list_status_order[$item.status]['class']}"
							{/if}>
							{if !empty($list_status_order[$item.status]['title'])}
								{$list_status_order[$item.status]['title']}
							{/if}
							</span>
						{/if}
			        </td>
			        <td class="text-left" data-title="{__d('template', 'thao_tac')}">
			        	&nbsp;
			        	<div class="d-inline-block" style="width: 24px">
							{if !empty($item.status) && $item.status == 'draft'}
								<a class="text-dark" data-toggle="tooltip" data-placement="top" data-original-title="{__d('template', 'xac_nhan')}" href="/order/checkout?code={$item.code}">
									<i class="fa-light fa-pen-to-square"></i>
								</a>
							{/if}
						</div>
						<div class="d-inline-block" style="width: 24px">
							{if  $item.status|in_array:['draft', 'new', 'package']}
								<a class="text-dark" data-toggle="tooltip" data-placement="top" data-original-title="{__d('template', 'huy_don')}" nh-order-btn="cancel" href="javascript:;" data-id="{if !empty($item.id)}{$item.id}{/if}">
									<i class="fa-light fa-xmark"></i>
								</a>
							{/if}
						</div>
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