{if $load_form}
    <div class="form-group">
        <label>
            {__d('admin', 'tieu_de_email_gui')}
            <span class="kt-font-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-list-ol"></i>
                </span>
            </div>
            <input name="title_email" value="{if !empty($template_info.title_email)}{$template_info.title_email}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>

    <div class="form-group">
        <label>
            {__d('admin', 'email_gui_kem_cc')}
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-envelope"></i>
                </span>
            </div>
            <input name="cc_email" value="{if !empty($template_info.cc_email)}{$template_info.cc_email}{/if}" type="text" class="form-control form-control-sm tagify-input">
        </div>
    </div>

    <div class="form-group">
        <label>
            {__d('admin', 'email_gui_kem_an_danh_bcc')}
        </label>
        <div class="input-group">
            <input name="bcc_email" value="{if !empty($template_info.bcc_email)}{$template_info.bcc_email}{/if}" type="text" class="form-control form-control-sm tagify-input">
        </div>
    </div>

    <div class="form-group">
        <label>
            {__d('admin', 'mau_email')}
            <span class="kt-font-danger">*</span>
        </label>
        <div class="row">
            <div class="col-6">
                {assign var = files value = $this->EmailTemplateAdmin->getListFileViewEmail()}
                {$this->Form->select('template', $files, ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($template_info.template)}{$template_info.template}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
            </div>
        </div>
    </div>

    <div class="kt-form__actions">
        <button id="btn-update-template" type="button" class="btn btn-brand btn-sm">
            {__d('admin', 'luu_mau_email')}
        </button>
    </div>
{/if}
