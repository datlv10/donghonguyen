<table nh-table="choose-row" class="table responsive-table">
	<thead>
		<tr>
			<th scope="col">
				{__d('template', 'ten_dia_chi')}
			</th>

			<th scope="col" class="text-left">
				{__d('template', 'dia_chi')}
			</th>

			<th scope="col">
				{__d('template', 'so_dien_thoai')}
			</th>
		</tr>
	</thead>
	<tbody>
		{if !empty($member_info.addresses)}
			{foreach from = $member_info.addresses item = item}
				<tr data-address-id="{$item.id}" class="cursor-pointer">
			        <th scope="row">
			        	{if !empty($item.address_name)}
			        		{$item.address_name}
			        	{else}
			        		{__d('template', 'dia_chi_mac_dinh')}
			        	{/if}
			        </th>

			        <td data-title="{__d('template', 'dia_chi')}: " class="text-left">
			        	{if !empty($item.full_address)}
			        		{$item.full_address}
			        	{/if}
			    	</td>

			        <td data-title="{__d('template', 'so_dien_thoai')}: ">
			        	{if !empty($item.phone)}
			        		{$item.phone}
			        	{/if}
			        </td>
			    </tr>
		    {/foreach}
		{else}
			<tr>
				<td colspan="5">
					{__d('template', 'hien_chua_co_dia_chi_nao')}
				</td>
			</tr>
	    {/if}
	</tbody>
</table>