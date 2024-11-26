<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard-attribute" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
            
            <a href="{ADMIN_PATH}/setting/attribute/add" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body">
            <div class="kt-form">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}..." autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('attribute_type', $this->AttributeAdmin->getListType(), ['id' => 'attribute_type', 'empty' => {__d('admin', 'loai_thuoc_tinh')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('input_type', $all_attribute, ['id' => 'input_type', 'empty' => {__d('admin', 'loai_input')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-icon collapse-search-advanced" data-toggle="collapse" data-target="#collapse-search-advanced">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>   
            </div>
            <div id="collapse-search-advanced" class="collapse collapse-search-advanced-content">
                <div class="kt-margin-t-20">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ap_dung_anh_dai_dien')}
                                </label>
                                <div class="kt-form__group">
                                    <div class="kt-form__control">
                                        <select name="has_image" id="has_image" class="form-control form-control-sm kt-selectpicker">
                                            <option value="" selected="selected">
                                                -- {__d('admin', 'chon')} --
                                            </option>

                                            <option value="0">
                                                {__d('admin', 'khong_ap_dung')}
                                            </option>

                                            <option value="1">
                                                {__d('admin', 'co_ap_dung')}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'bat_buoc')}
                                </label>
                                <div class="kt-form__control">
                                    <select name="required" id="required" class="form-control form-control-sm kt-selectpicker">
                                        <option value="" selected="selected">
                                            -- {__d('admin', 'chon')} --
                                        </option>

                                        <option value="0">
                                            {__d('admin', 'khong_bat_buoc')}
                                        </option>

                                        <option value="1">
                                            {__d('admin', 'co_bat_buoc')}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
            	<div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mt-0 mb-20"></div>
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__label kt-form__label-no-wrap">
                                <label class="kt-font-bold kt-font-danger-">
                                    {__d('admin', 'da_chon')}
                                    <span id="nh-selected-number">0</span> :
                                </label>
                            </div>

                            <div class="kt-form__control">
                                <div class="btn-toolbar">                            
                                    <button class="btn btn-sm btn-label-danger nh-delete-all mobile-mb-5" type="button">
                                        <i class="la la-trash-o"></i>
                                        {__d('admin', 'xoa_tat_ca')}
                                    </button>                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>

{$this->element('Admin.page/modal_detail')}
{$this->element('Admin.page/popover_quick_change')}