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
                    <a class="nav-link active" data-toggle="tab" href="#template-tab" role="tab">
                        <i class="fa fa-cogs"></i>
                        {__d('admin', 'cau_hinh_mau_in')}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#edit-template-tab" role="tab">
                        <i class="fa fa-code"></i>
                        {__d('admin', 'chinh_sua_mau_in')}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#test-print-tab" role="tab">
                        <i class="fa fa-print"></i>
                        {__d('admin', 'in_thu')}
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                <div id="template-tab" class="tab-pane active" role="tabpanel">
                    <form id="update-template-form" action="{ADMIN_PATH}/setting/print-template/save" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-lg-7 col-xl-7">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'mau_in')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    {assign var = templates value = $this->PrintTemplateAdmin->getListPrintTemplates()}
                                    {$this->Form->select('template_code', $templates, ['id' => 'print-template', 'empty' => "{__d('admin', 'chon')}", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>

                                <div id="wrap-form-template"></div>
                            </div>
                        </div>                        
                    </form>
                </div>

                <div id="edit-template-tab" class="tab-pane" role="tabpanel">
                    <form id="edit-template-form" action="{ADMIN_PATH}/setting/print-template/edit-view" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'mau_in')}
                                <span class="kt-font-danger">*</span>
                            </label>

                            <div class="row">
                                <div class="col-lg-6 col-xl-4">
                                    {assign var = files value = $this->PrintTemplateAdmin->getListFileViewPrint()}
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
                <div id="test-print-tab" class="tab-pane" role="tabpanel">
                    <form id="test-print-form" action="{ADMIN_PATH}/setting/print-template/edit-view" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-lg-6 col-xl-6">

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'mau_in')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    {assign var = templates value = $this->PrintTemplateAdmin->getListPrintTemplates()}
                                    {$this->Form->select('template_code', $templates, ['empty' => "{__d('admin', 'chon')}", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ID')}
                                        <span class="kt-font-danger">*</span>
                                    </label>

                                    <input name="id_record" value="" class="form-control form-control-sm" type="number" maxlength="6">
                                </div>
                            </div>
                        </div>

                        <div class="kt-form__actions">
                            <button id="btn-test-print" type="button" class="btn btn-brand btn-sm">
                                {__d('admin', 'in_thu')}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>