{if $load_form}
    <div class="form-group">
        <label>
            {__d('admin', 'tieu_de_mau_in')}
            <span class="kt-font-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-list-ol"></i>
                </span>
            </div>
            <input name="title_print" value="{if !empty($template_info.title_print)}{$template_info.title_print}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
        </div>
    </div>

    <div class="form-group">
        <label>
            {__d('admin', 'Template')}
            <span class="kt-font-danger">*</span>
        </label>
        {assign var = files value = $this->PrintTemplateAdmin->getListFileViewPrint()}
        {$this->Form->select('template', $files, ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($template_info.template)}{$template_info.template}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
    </div>

    <div class="kt-form__actions">
        <button id="btn-update-template" type="button" class="btn btn-brand btn-sm">
            {__d('admin', 'luu_mau_in')}
        </button>
    </div>
{/if}
