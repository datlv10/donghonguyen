<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/tag/add" class="btn btn-sm btn-brand">
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
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                <div class="kt-input-icon kt-input-icon--left">
                                    <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}..." autocomplete="off">
                                    <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                        <span><i class="la la-search"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                <div class="kt-form__group">
                                    <div class="kt-form__control">
                                        {$this->Form->select('lang', $list_languages, ['id' => 'nh-language', 'empty' => "{__d('admin', 'ngon_ngu')}", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                    </div>
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
                                    <div class="dropdown mr-10">
                                        <button type="button" class="btn btn-sm btn-label-primary dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                            {__d('admin', 'thay_doi_trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            {foreach from = $this->ListConstantAdmin->listStatus() key = k_status item = status}
                                                <a class="dropdown-item nh-change-status-all" data-status="{$k_status}" href="javascript:;">
                                                    {$status}
                                                </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                  
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