<div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
    {__d('template', 'xac_nhan_thay_doi_thong_tin_tai')} <a href="{$this->Utilities->getUrlWebsite()}" target="_blank"><strong>website</strong></a>
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
    {__d('template', 'neu_nhu_ban_khong_yeu_cau_gui_lai_ma_xac_nhan_vui_long_bo_qua_va_xoa_email_nay')}.
</div>
<div style="border-collapse:collapse;font-family:Helvetica,Arial;font-size:12px;line-height:150%;margin-bottom:10px">
    {__d('template', 'xin_cam_on')}!
</div>