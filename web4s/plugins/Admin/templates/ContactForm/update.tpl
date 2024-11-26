{assign var = url_list value = "{ADMIN_PATH}/contact/form"}
{assign var = url_add value = "{ADMIN_PATH}/contact/form/add"}
{assign var = url_edit value = "{ADMIN_PATH}/contact/form/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/contact/form/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_form')}
                                 <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($form.name)}{$form.name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ma_form')}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-qrcode"></i>
                                    </span>
                                </div>
                                <input name="code" value="{if !empty($form.code)}{$form.code}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'ma_form_se_duoc_su_dung_de_nhung_ngoai_website')}
                            </span>
                        </div>
                    </div>
                </div>                        

                <div class="form-group">
                    <label class="mb-10">
                        {__d('admin', 'gui_email')}
                    </label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--tick kt-radio--success">
                            <input type="radio" name="send_email" value="1" {if !empty($form.send_email)}checked{/if}> {__d('admin', 'co')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="send_email" value="0" {if empty($form.send_email)}checked{/if}> {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label class="mb-10">
                                {__d('admin', 'nhan_email_khi_lien_he')}
                            </label>

                            {assign var = list_email_templates value = $this->EmailTemplateAdmin->getListEmailTemplates()}
                            {$this->Form->select('template_email_code', $list_email_templates, ['id' => 'template_email_code', 'empty' => {__d('admin', 'chon')}, 'default' => "{if !empty($form.template_email_code)}{$form.template_email_code}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>                
            </div>
        </div>

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_cac_truong_thong_tin')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div id="wrap-list-field">
                    <div data-repeater-list="fields">
                        {if !empty($form.fields)}
                            {foreach from = $form.fields key = key item = field}
                                {$this->element('../ContactForm/item_field', ['field' => $field])}
                            {/foreach}
                        {else}
                            {$this->element('../ContactForm/item_field')}
                        {/if}
                    </div>

                    <div class="row">
                        <div class="col-xl-2 col-lg-2">
                            <span data-repeater-create class="btn btn-sm btn-brand">
                                <i class="la la-plus"></i>
                                {__d('admin', 'them_truong_moi')}
                            </span>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </form>
</div>
