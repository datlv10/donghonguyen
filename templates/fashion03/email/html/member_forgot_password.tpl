{assign var = customer_info value = $this->Member->getDetailCustomer($id_record, [
    'get_account' => true
])}

{if !empty($customer_info)}
    <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
        {__d('template', 'xac_nhan_thay_doi_mat_khau')}
    </div>

    {if !empty($token)}
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            <p>{__d('template', 'ma_xac_nhan')}</p>
            <h2>
                <strong>
                    {$token}
                </strong>
            </h2>
        </div>
    {/if}

    <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
        {__d('template', 'de_xac_nhan_viec_thay_doi_mat_khau_nay_la_hop_le_ban_vui_long_truy_cap_vao')}
        <a href="{$this->Utilities->getUrlWebsite()}/member/verify-forgot-password{if !empty($customer_info.email)}?email={$customer_info.email}{/if}" target="_blank">
            <strong>{__d('template', 'duong_dan')}</strong>
        </a>
    </div>

    {if !empty($customer_info.username)}
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            {__d('template', 'neu_nhu_ban_khong_thay_doi_mat_khau_voi_tai_khoan')} {$customer_info.username} {__d('template', 'vui_long_bo_qua_va_xoa_email_nay')}.
        </div>
        <div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
            {__d('template', 'xin_cam_on')}!
        </div>
    {/if}
{/if}