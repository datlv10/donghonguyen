{assign var = contact value = $this->Contact->getDetailContact($id_record)}
{assign var = contact_info value = []}
{if !empty($contact.value)}
    {assign var = contact_info value = $contact.value}    
{/if}

<div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
    {__d('template', 'khach_hang_de_lai_thong_tin_lien_he')}
</div>
{if !empty($contact_info)}
    <table style="border-collapse:collapse;border: 1px solid #e5e5e5;" align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
	    <thead>
	        <tr>
	            <th style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;padding:10px 15px;background-color: #36414b;color:#ffffff; text-align: left;"  width="30%">
	                <b>{__d('template', 'thong_tin_lien_he')}: </b>
	            </th>
	            <th style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;padding:10px 15px;background-color: #36414b;color:#ffffff; text-align: left;" width="70%"></th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:5px 10px;">
	                {__d('template', 'ho_va_ten')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:5px 10px;">
	            	{if !empty($contact_info.full_name)}
                        <strong>{$contact_info.full_name}</strong>
                    {/if}
	            </td>
	        </tr>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:5px 10px;">
	                {__d('template', 'so_dien_thoai')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:5px 10px;">
	            	{if !empty($contact_info.phone)}
                        <strong>{$contact_info.phone}</strong>
                    {/if}
	            </td>
	        </tr>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:5px 10px;">
	                {__d('template', 'tieu_de')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:5px 10px;">
	            	{if !empty($contact_info.title)}
                        <strong>{$contact_info.title}</strong>
                    {/if}
	            </td>
	        </tr>
	        <tr>
	            <td style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050; padding:5px 10px;">
	                {__d('template', 'noi_dung')}
	            </td>
	            <td  style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;text-align:left;color: #505050;padding:5px 10px;">
	            	{if !empty($contact_info.content)}
                        <strong>{$contact_info.content}</strong>
                    {/if}
	            </td>
	        </tr>
	    </tbody>
	</table>
{/if}