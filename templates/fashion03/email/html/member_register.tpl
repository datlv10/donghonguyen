{assign var = customer_info value = $this->Member->getDetailCustomer($id_record, [
	'get_account' => true
])}

{if !empty($customer_info)}

    <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
        {__d('template', 'chuc_mung_ban_da_dang_ky_thanh_cong_tai_khoan_tai')} <a href="{$this->Utilities->getUrlWebsite()}" target="_blank"><strong>website</strong></a>
    </div>

    <table style="border-collapse:collapse;border: 1px solid #e5e5e5;" align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
	    <thead>
	        <tr>
	            <th style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;padding:8px 10px 15px;background-color: #36414b;color:#ffffff; text-align: left;"  width="30%">
	                <b>{__d('template', 'thong_tin_dang_ky')}: </b>
	            </th>
	            <th style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;padding:8px 10px 15px;background-color: #36414b;color:#ffffff; text-align: left;" width="70%"></th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:8px 10px;">
	                {__d('template', 'ho_va_ten')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
	            	{if !empty($customer_info.full_name)}
                        <strong>
                        	{$customer_info.full_name}
                    	</strong>
                    {/if}
	            </td>
	        </tr>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:8px 10px;">
	                {__d('template', 'ten_truy_cap')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
	            	{if !empty($customer_info.username)}
                        <strong>
                        	{$customer_info.username}
                        </strong>
                    {/if}
	            </td>
	        </tr>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:8px 10px;">
	                {__d('template', 'email_dang_ky')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
	            	{if !empty($customer_info.email)}
                        <strong>
                        	<a href="mailto:{$customer_info.email}" target="_blank">
                        		{$customer_info.email}
                        	</a>
                    	</strong>
                    {/if}
	            </td>
	        </tr>

	        {if !empty($token)} 
		        <tr>
		        	<td></td>
		            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
		            	<p>{__d('template', 'ma_xac_nhan')}</p>
		            	<h2>
		            		<strong>
			            		{$token}
			            	</strong>
		            	</h2>
		            </td>
		        </tr>
		        <tr>
		            <td colspan="2" style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
		            	{__d('template', 'de_xac_nhan_viec_dang_ky_nay_la_hop_le_ban_vui_long_truy_cap_vao')}
		            	<a href="{$this->Utilities->getUrlWebsite()}/member/verify-email{if !empty($customer_info.email)}?email={$customer_info.email}{/if}" target="_blank">
		                    <strong>{__d('template', 'duong_dan')}</strong>
		                </a>
		            </td>
		        </tr>
		        {if !empty($customer_info.username)}
			        <tr>
			            <td colspan="2" style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
			            	{__d('template', 'neu_nhu_ban_khong_dang_ky_thong_tin_voi_tai_khoan')} {$customer_info.username} {__d('template', 'vui_long_bo_qua_va_xoa_email_nay')}.
			            </td>
			        </tr>
			        <tr>
			            <td colspan="2" style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:8px 10px;">
			            	{__d('template', 'xin_cam_on')}!
			            </td>
			        </tr>
		        {/if}
	        {/if}

	    </tbody>
	</table>
{/if}