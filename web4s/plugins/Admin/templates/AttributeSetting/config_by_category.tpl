<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <div class="btn-group">
                <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'luu_cau_hinh')} (F1)
                </span>
            </div>
            {$this->element('Admin.page/language')}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-grid  kt-wizard-v2 kt-wizard-v2--white" id="kt_wizard" data-ktwizard-state="step-first">
                {$this->element('../AttributeSetting/header_tab')}

                <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                    <div class="kt-form p-20">
                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            <div class="kt-todo__body">
                                {$this->element('../AttributeSetting/element_config_attribute_product')}
                            </div>
                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {$this->element('../AttributeSetting/element_config_attribute_article')}

                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {$this->element('../AttributeSetting/element_config_attribute_item')}

                        </div>

                        <div class="kt-wizard-v2__content border-bottom-0" data-ktwizard-type="step-content">
                            {$this->element('../AttributeSetting/element_config_brand')}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>