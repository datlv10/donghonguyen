{if !empty($contact)}
    {assign var = url_list value = "{ADMIN_PATH}/contact/list"}

    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-sm btn-default">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-wizard-v4">
            <div class="kt-portlet">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid">
                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v4__wrapper">
                            <div class="kt-form" style="width: 95%">
                                <div class="kt-wizard-v4__content">
                                    <div class="kt-heading kt-heading--md">
                                        {__d('admin', 'thong_tin_form')}:
                                        {if !empty($contact.form.name)}
                                            <a href="{ADMIN_PATH}/contact/form/update/{$contact.form.id}" target="_blank">
                                                {$contact.form.name}
                                            </a>                                            
                                        {/if}
                                    </div>

                                    <div class="kt-form__section kt-form__section--first">
                                        <div class="kt-wizard-v4__review entire-detail">
                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'noi_dung_lien_he')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">    
                                                    {if !empty($contact.value)}  
                                                        {foreach from = $contact.value item = value key = code}       
                                                            <p class="mb-5">
                                                                {if !empty($fields[$code])}
                                                                    {$fields[$code]}:
                                                                {else}
                                                                    {$code}:
                                                                {/if}    
                                                                <span class="kt-font-bolder">
                                                                    {$value}
                                                                </span>
                                                            </p>
                                                        {/foreach}
                                                    {/if} 
                                                    <p class="mb-5">
                                                        {__d('admin', 'ngay_nhan')}:  
                                                        <span class="kt-font-bolder">
                                                            {if !empty($contact.created)}
                                                                <i>{$contact.created}</i>
                                                            {/if}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}</span>
{/if}

