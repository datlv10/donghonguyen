<form id="momo-form" action="{ADMIN_PATH}/setting/payment-gateway/{AZPAY}" method="POST" autocomplete="off">
    <div class="kt-heading kt-heading--md">
        {__d('admin', 'thong_tin_co_ban')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">

            <div class="form-group">
                <label>
                    {__d('admin', 'trang_thai')}
                </label>
                <div class="kt-radio-inline mt-5">

                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                        <input type="radio" name="status" value="1" {if !empty($item.status)}checked{/if}> 
                            {__d('admin', 'dang_hoat_dong')}
                        <span></span>
                    </label>

                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                        <input type="radio" name="status" value="0" {if empty($item.status)}checked{/if}> 
                            {__d('admin', 'khong_hoat_dong')}
                        <span></span>
                    </label>
                </div>
            </div>

            {if !empty($list_language)}
                {foreach from = $list_language item = language key = k_lang}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_phuong_thuc')}
                            ({$language})
                            <span class="kt-font-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input name="name[{$k_lang}]" value="{if !empty($item['content'][$k_lang])}{$item['content'][$k_lang]['name']}{/if}" class="form-control form-control-sm check-required" type="text">

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <div class="list-flags">
                                        <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'thong_tin_cau_hinh')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        Partner Email
                    </label>

                    <input name="config[partner_email]" value="{if !empty($item.config.partner_email)}{$item.config.partner_email}{/if}" type="text" class="form-control" >
                </div>

                <div class="col-md-6 col-12">
                    <label>
                        Partner ID
                    </label>

                    <input name="config[partner_id]" value="{if !empty($item.config.partner_id)}{$item.config.partner_id}{/if}" type="text" class="form-control">
                </div>
            </div>

            <div class="form-group row ">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'server_thanh_toan')}
                    </label>

                    {$this->Form->select('config[transaction_server]', $this->ListConstantAdmin->listStatusTransaction(), ['empty' => '', 'default' => "{if !empty($item.config.transaction_server)}{$item.config.transaction_server}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <span class="mr-1">
            {__d('admin', 'link_webhook')}:
        </span>
        <span>
            {$this->Utilities->getUrlWebsite()}/payment/webhooks/azpay
        </span>
    </div>
</form>