<div class="kt-subheader kt-grid__item" id="kt_subheader">
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

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__body">
            <ul class="nav nav-tabs  nav-tabs-line" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#config-tab" role="tab">
                        <i class="fa fa-cogs"></i>
                        {__d('admin', 'cau_hinh_email')}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#template-tab" role="tab">
                        <i class="fa fa-envelope-open-text"></i>
                        {__d('admin', 'cau_hinh_mau_email')}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#edit-template-tab" role="tab">
                        <i class="fa fa-code"></i>
                        {__d('admin', 'chinh_sua_mau_email')}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#send-try-tab" role="tab">
                        <i class="fab fa-telegram-plane"></i>
                        {__d('admin', 'gui_thu')}
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="config-tab" class="tab-pane active" role="tabpanel">
                    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'email_ung_dung')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input name="email" value="{if !empty($config.email)}{$config.email}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'mat_khau_ung_dung')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-unlock-alt"></i>
                                            </span>
                                        </div>
                                        <input name="application_password" value="{if !empty($config.application_password)}{$config.application_password}{/if}" class="form-control form-control-sm" type="text">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        Email Host
                                    </label>

                                    {assign var = list_host value = [
                                        'gmail' => __d('admin', 'gmail'),
                                        'umail' => 'UMail',
                                        'other' => __d('admin', 'khac')
                                    ]}
                                    {$this->Form->select('smtp_host', $list_host, ['empty' => null, 'default' => "{if !empty($config.smtp_host)}{$config.smtp_host}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'id' => 'smtp_host'])}
                                </div>

                                <div id="wrap-another-config" class="{if empty($config.smtp_host) || $config.smtp_host != 'other'}d-none{/if}">
                                    <div class="form-group">
                                        <label>
                                            SMTP Server
                                        </label>
                                        <div class="input-group">
                                            <input id="email-smtp" name="smtp" value="{if !empty($config.smtp)}{$config.smtp}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            Port
                                        </label>

                                        <div class="input-group">
                                            <input id="email-port" name="port" value="{if !empty($config.port)}{$config.port}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            SSL
                                        </label>

                                        <div class="input-group">
                                            <input id="email-ssl" name="ssl" value="{if !empty($config.ssl)}{$config.ssl}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                        </div>
                                    </div>
                                </div>                                
                            </div>

                            <div class="col-lg-6 col-xl-6">
                                <div class="form-group d-none">
                                    <label>
                                        {__d('admin', 'quan_tri_nhan_thong_bao')}
                                    </label>

                                    <div class="kt-radio-inline h-35">
                                        <label class="kt-radio kt-radio--tick kt-radio--success">
                                            <input type="radio" name="admin_receive_notification" value="1" {if !empty($config.admin_receive_notification)}checked{/if}> {__d('admin', 'co')}
                                            <span></span>
                                        </label>

                                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                                            <input type="radio" name="admin_receive_notification" value="0" {if empty($config.admin_receive_notification)}checked{/if}> {__d('admin', 'khong')}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'email_quan_tri')}
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope-open"></i>
                                            </span>
                                        </div>
                                        <input id="email-administrator" name="email_administrator" value="{if !empty($config.email_administrator)}{$config.email_administrator}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-form__actions">
                            <button id="btn-save-config" type="button" class="btn btn-brand btn-sm">
                                {__d('admin', 'luu_cau_hinh')}
                            </button>
                        </div>
                    </form>
                </div>

                <div id="template-tab" class="tab-pane" role="tabpanel">
                    <form id="update-template-form" action="{ADMIN_PATH}/setting/email-template/save" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-lg-8 col-xl-8">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ten_email')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    {assign var = templates value = $this->EmailTemplateAdmin->getListEmailTemplates()}
                                    {$this->Form->select('template_code', $templates, ['id' => 'email-template', 'empty' => "{__d('admin', 'chon')}", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>

                                <div id="wrap-form-template"></div>
                            </div>
                        </div>                        
                    </form>
                </div>

                <div id="edit-template-tab" class="tab-pane" role="tabpanel">
                    <form id="edit-template-form" action="{ADMIN_PATH}/setting/email-template/edit-view" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'mau_email')}
                                <span class="kt-font-danger">*</span>
                            </label>

                            <div class="row">
                                <div class="col-lg-6 col-xl-4">
                                    {assign var = files value = $this->EmailTemplateAdmin->getListFileViewEmail()}
                                    {$this->Form->select('view_template', $files, ['id' => 'view-template', 'empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($template_info.template)}{$template_info.template}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div id="wrap-content-template" class="col-12"></div>
                        </div>
                    </form>

                    <div class="kt-form__actions mt-10">
                        <button id="btn-edit-view" type="button" class="btn btn-brand btn-sm">
                            {__d('admin', 'cap_nhat')}
                        </button>
                    </div>
                </div>

                <div id="send-try-tab" class="tab-pane" role="tabpanel">
                    <form id="send-try-form" action="{ADMIN_PATH}/setting/email-send-try" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-lg-6 col-xl-6">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'email_nhan')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input name="email_send_try" value="" class="form-control form-control-sm" type="text" maxlength="255">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'chon_loai')}
                                    </label>

                                    <div class="kt-radio-inline">
                                        <label class="kt-radio kt-radio--tick kt-radio--success">
                                            <input type="radio" name="type_send_try" value="content" checked="true"> 
                                            {__d('admin', 'gui_thu_noi_dung')}
                                            <span></span>
                                        </label>

                                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                                            <input type="radio" name="type_send_try" value="template"> 
                                            {__d('admin', 'gui_thu_mau_email')}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div id="wrap-type-content">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'noi_dung_gui_thu')}
                                            <span class="kt-font-danger">*</span>
                                        </label>
                                        <input name="content_send_try" value="" class="form-control form-control-sm" type="text" maxlength="255">
                                    </div>
                                </div>
                                
                                <div id="wrap-type-template" class="d-none">
                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'mau_email')}
                                            <span class="kt-font-danger">*</span>
                                        </label>

                                        {assign var = templates value = $this->EmailTemplateAdmin->getListEmailTemplates()}
                                        {$this->Form->select('template_code', $templates, ['empty' => "{__d('admin', 'chon')}", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            {__d('admin', 'ID')}
                                            <span class="kt-font-danger">*</span>
                                        </label>

                                        <input name="id_record" value="" class="form-control form-control-sm" type="number" maxlength="6">
                                        <span class="form-text text-muted">
                                            {__d('admin', 'nhap_id_cua_ban_ghi_can_gui_thu')} 
                                            ({__d('admin', 'don_hang')}, {__d('admin', 'san_pham')}, {__d('admin', 'khach_hang')} ...)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="kt-form__actions">
                            <button id="btn-send-try" type="button" class="btn btn-brand btn-sm">
                                {__d('admin', 'gui_email')}
                            </button>
                        </div>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
</div>