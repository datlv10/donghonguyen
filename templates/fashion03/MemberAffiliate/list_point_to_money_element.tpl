{if !empty($point_tomoney)}
	{assign var = list_status value = [
		0 => {__d("template", "huy_xet_duyet")},
		1 => {__d("template", "da_duyet")},
		2 => {__d("template", "cho_xet_duyet")}
	]}

	{assign var = list_type value = [
		0 => {__d("template", "rut_tien_theo_ky_han")},
		1 => {__d("template", "rut_tien_truoc_ky_han")}
	]}

	<table class="table responsive-table mb-5">
		<thead>
			<tr>
				<th class="w-25">{__d('template', 'ngay_tao_yeu_cau')}</th>
				<th class="w-15">{__d('template', 'so_diem_rut')}</th>
				<th class="w-30">{__d('template', 'ngan_hang')}</th>
				<th class="w-30">{__d('template', 'ghi_chu')}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from = $point_tomoney item = item}
				<tr>
			        <td scope="row">			        	
		        	    {if isset($item.status)}
			        	    <p class="font-weight-bold m-0">
			        	    	{$list_status[$item.status]}
			        	    </p>
			        	{/if}
			        	{if isset($item.type)}
			        		<p class="font-weight-bold m-0">
		        	    		{$list_type[$item.type]}
		        	    	</p>
		        	    {/if}

			        	{if !empty($item.created)}
				        	{assign created value = "{$this->Utilities->convertIntgerToDateTimeString($item.created, 'H:i d-m-Y')}"}
			        	    <i>
			        	    	{$created}
			        	    </i>
		        	    {/if}
			        </td>
                   
			        <td data-title="{__d('template', 'so_tien_rut')}">
			        	{if !empty($item.point)}
			        	    <b>{$item.point|number_format:0:".":","} {__d('template', 'diem')}</b>
			        	{/if}
			        </td>

			        <td data-title="{__d('template', 'ngan_hang')}">
			        	{if !empty($item.bank_name)}
			        	    {$item.bank_name}
			        	    {if !empty($item.account_number)}
			        	    	- {$item.account_number}
			        	    {/if}
			        	{/if}
			        </td>

			    	<td data-title="{__d('template', 'ghi_chu')}">
			    		<div>
				    		{if !empty($item.note)}
				    			<b>{__d('template', 'doi_tac')}:</b> {$item.note}
				    		{/if}
				    		{if !empty($item.note_admin)}
				    			<br/>
				    			<b>{__d('template', 'quan_tri')}:</b> {$item.note_admin}
				    		{/if}
			    		</div>
			    	</td>
			    </tr>
			{/foreach}
		</tbody>
	</table>
	{$this->element('../Member/pagination', ['pagination' => $pagination])}
{else}
	{__d('template', 'chua_co_yeu_cau_rut_tien')}
{/if}