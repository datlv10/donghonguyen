<form id="onepay-domestic-form" action="{ADMIN_PATH}/setting/payment-gateway/{ONEPAY_INSTALLMENT}" method="POST" autocomplete="off">
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
                        Merchant ID
                    </label>

                    <input name="config[merchant_id]" value="{if !empty($item.config.merchant_id)}{$item.config.merchant_id}{/if}" type="text" class="form-control" >
                </div>

                <div class="col-md-6 col-12">
                    <label>
                        Access Key
                    </label>

                    <input name="config[access_key]" value="{if !empty($item.config.access_key)}{$item.config.access_key}{/if}" type="text" class="form-control">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        Secret Key
                    </label>

                    <input name="config[secret_key]" value="{if !empty($item.config.secret_key)}{$item.config.secret_key}{/if}" type="text" class="form-control" >
                </div>
            </div>

            <div class="form-group row form-group-xs">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'server_thanh_toan')}
                    </label>

                    {$this->Form->select('config[transaction_server]', $this->ListConstantAdmin->listStatusTransaction(), ['empty' => '', 'default' => "{if !empty($item.config.transaction_server)}{$item.config.transaction_server}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'mo_ta_cong_thanh_toan')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            {if !empty($list_language)}
                {foreach from = $list_language item = language key = k_lang}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'mo_ta')}
                            ({$language})
                        </label>
                        
                        <textarea id="content-onepay-installment-{$k_lang}" name="content[{$k_lang}]" class="mce-editor-simple check-required">{if !empty($item['content'][$k_lang])}{$item['content'][$k_lang]['content']}{/if}</textarea>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>
    <div class="form-group">
        <span class="mr-1">
            {__d('admin', 'link_webhook')}:
        </span>
        <span>
            {$this->Utilities->getUrlWebsite()}/payment/webhooks/onepay_installment
        </span>
    </div>
</form>