<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="slack-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Slack
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="slack[status]"  value="1" {if !empty($slack.status)}checked{/if}> 
                                {__d('admin', 'dang_hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="slack[status]" value="0" {if !isset($slack.status) || empty($slack.status)}checked{/if}> 
                                {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Webhook URL
                            </label>

                            <input name="slack[webhook]" value="{if !empty($slack.webhook)}{$slack.webhook}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div>
                    <a href="javascript:;" data-toggle="modal" data-target="#guide_slack">
                        <i class="fa fa-info-circle fs-16"></i>
                        {__d('admin', 'huong_dan_lay_webhook_url')}
                    </a>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <form id="telegram-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Telegram (Nicegram)
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="telegram[status]"  value="1" {if !empty($telegram.status)}checked{/if}> 
                                {__d('admin', 'dang_hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="telegram[status]" value="0" {if !isset($telegram.status) || empty($telegram.status)}checked{/if}> 
                                {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Token Bot
                            </label>

                            <input name="telegram[token]" value="{if !empty($telegram.token)}{$telegram.token}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Chat ID
                            </label>

                            <input name="telegram[chat_id]" value="{if !empty($telegram.chat_id)}{$telegram.chat_id}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div>
                    <a href="javascript:;" data-toggle="modal" data-target="#guide_nicegram">
                        <i class="fa fa-info-circle fs-16"></i>
                        {__d('admin', 'huong_dan_cau_hinh_telegram_nicegram')}
                    </a>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_gui_tin')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                 <div class="form-group">
                    <div class="kt-radio-inline mt-5">
                        <div class="clearfix mb-10">
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                <input type="checkbox" name="apply[order]" value="1" {if !empty($apply.order)}checked="true"{/if}>
                                {__d('admin', 'don_hang')}
                                <span></span>
                            </label>
                        </div>

                        <div class="clearfix mb-10">
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                <input type="checkbox" name="apply[contact]" value="1" {if !empty($apply.contact)}checked="true"{/if}>
                                {__d('admin', 'lien_he')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="guide_slack" tabindex="-1" role="dialog" aria-labelledby="guideSlack" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guideSlack">{__d('admin', 'huong_dan_lay_webhook_url')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="kt-scroll" data-scroll="true" data-height="600">
                    <p class="mb-10 fs-16">
                        <strong>{__d('admin', 'tao_app_tren_slack')}</strong>
                    </p>
                    <p class="text-center">
                        <img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_7.png">
                    </p>
                    <p class="mb-10 fs-16">
                        <strong>{__d('admin', 'trong_trang_dashboard_cua_slack_chon_them_ung_dung_va_tim_incoming_webhooks')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_1.png"></p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_2.png"></p>
                    <p class="mb-10 fs-16">
                        <strong>{__d('admin', 'lua_chon_channel_muon_gui_tin')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_3.png"></p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_5.png"></p>
                    <p class="mb-10 fs-16">
                        <strong>{__d('admin', 'neu_chua_co_thi_tao_moi_channel')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_4.png"></p>
                    <p class="mb-10 fs-16">
                        <strong>{__d('admin', 'thong_tin_webhook_url')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/Screenshot_6.png"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="guide_nicegram" tabindex="-1" role="dialog" aria-labelledby="guideNicegram" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guideNicegram">{__d('admin', 'huong_dan_cau_hinh_telegram_nicegram')}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="kt-scroll" data-scroll="true" data-height="600">
                    <p class="mb-5">
                        <strong>{__d('admin', 'tai_ung_dung_nicegram_tren_iphone')}</strong>
                    </p>
                    <p class="mb-10">
                        <strong>{__d('admin', 'tren_ung_dung_nicegram_tim_kiem_botfather')}</strong>
                    </p>
                    <p class="text-center">
                        <img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/img_5.png">
                    </p>
                    <p class="mb-10">
                        <strong>{__d('admin', 'khi_cua_so_tro_chuyen_voi_bot_duoc_mo_nhan_nut_start')}</strong>
                    </p>
                    <p class="text-center">
                        <img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/img_1.png">
                    </p>
                    <p class="mb-10">
                        <strong>{__d('admin', 'nhap_vao_newbot_va_nhap_ten_username_cho_bot')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/img_2.png"></p>
                    <p class="mb-10">
                        <strong>{__d('admin', 'thong_tin_token_cua_bot')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/img_3.png"></p>
                    <p class="mb-5">
                        <strong>{__d('admin', 'tao_nhom_va_them_bot_ban_vua_tao_vao_nhom_chat')}</strong>
                    </p>
                    <p class="mb-10">
                        <strong>{__d('admin', 'lay_thong_tin_id_nhom_chat')}</strong>
                    </p>
                    <p class="mb-10 text-center"><img class="img-fluid" src="{ADMIN_PATH}/assets/media/send_messege/img_4.jpeg"></p>
                </div>
            </div>
        </div>
    </div>
</div>