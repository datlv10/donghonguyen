<form id="bank-tranfer-form" action="{ADMIN_PATH}/setting/payment-gateway{if !empty($item.code)}/{$item.code}{/if}" method="POST" autocomplete="off">
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
                        <input type="radio" name="status" value="1" {if isset($item.status) && $item.status == 1}checked{/if}> 
                            {__d('admin', 'dang_hoat_dong')}
                        <span></span>
                    </label>

                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                        <input type="radio" name="status" value="0" {if isset($item.status) && $item.status == 0}checked{/if}> 
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
        {__d('admin', 'cau_hinh_tai_khoan_ngan_hang')}
    </div>

    <div id="kt_repeater">
        <div class="row" data-repeater-list="config">
            {if !empty($item.config)}
                {foreach from = $item.config key = key item = data}
                    <div class="col-lg-6" data-repeater-item>
                        {$this->Form->select('bank', $list_banks, ['name'=>'bank', 'default' => "{if !empty($data.bank)}{$data.bank}{/if}", 'empty' => "{__d('admin', 'ten_ngan_hang')}", 'data-live-search' => true, 'class' => 'form-control form-control-sm kt-selectpicker mb-20'])}

                        {*
                        <div class="form-group">
                            <input name="bank_branch" value="{if !empty($data.bank_branch)}{$data.bank_branch}{/if}" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'chi_nhanh')}">
                        </div>
                        *}

                        <div class="form-group mb-0">
                            <input name="bank_name" value="{if !empty($data.bank_name)}{$data.bank_name}{/if}" bank-name type="hidden" class="form-control form-control-sm">
                        </div>

                        <div class="form-group">
                            <input name="account_holder" value="{if !empty($data.account_holder)}{$data.account_holder}{/if}" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'chu_tai_khoan')}">
                        </div>

                        <div class="form-group">
                            <input name="account_number" value="{if !empty($data.account_number)}{$data.account_number}{/if}" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'so_tai_khoan')}">
                        </div>

                        <div class="form-group">
                            <div class="text-right">
                                <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                    <i class="la la-trash-o"></i>
                                    {__d('admin', 'xoa')}
                                </a>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {else}
                <div class="col-lg-6" data-repeater-item>
                    <div class="form-group">
                        <select data-live-search="true" name="bank_name" class="dropdown-select selectpicker form-control form-control-sm mb-20">
                            {foreach from = $list_banks key = key item = bank}
                                <option value="{$key}">{$bank}</option>
                            {/foreach}
                        </select>
                    </div>
                    
                    {*
                    <div class="form-group">
                        <input name="bank_branch" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'chi_nhanh')}">
                    </div>
                    *}

                    <div class="form-group mb-0">
                        <input name="bank_name" value="{if !empty($data.bank_name)}{$data.bank_name}{/if}" bank-name type="hidden" class="form-control form-control-sm">
                    </div>

                    <div class="form-group">
                        <input name="account_holder" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'chu_tai_khoan')}">
                    </div>

                    <div class="form-group">
                        <input name="account_number" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'so_tai_khoan')}">
                    </div>

                    <div class="form-group">
                        <div class="text-right">
                            <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                <i class="la la-trash-o"></i>
                                {__d('admin', 'xoa')}
                            </a>
                        </div>
                    </div>
                </div>

            {/if}
        </div>

        <div class="form-group form-group-last kt-margin-b-15 text-right">
            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi_tai_khoan')}
            </a>
            
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
                        <textarea id="content-tranfer-{$k_lang}" name="content[{$k_lang}]" class="mce-editor-simple check-required">{if !empty($item['content'][$k_lang])}{$item['content'][$k_lang]['content']}{/if}</textarea>
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
            {$this->Utilities->getUrlWebsite()}/payment/webhooks/bank
        </span>
    </div>
</form>