<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
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
                                {$this->Form->select('status', $this->ListConstantAdmin->listStatusShipping(), ['id'=>'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('shipping_method', $this->ShippingAdmin->getListShippingMethod(), ['id'=>'shipping_method', 'empty' => "-- {__d('admin', 'phuong_thuc_van_chuyen')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
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
                                    {__d('admin', 'tien_thu_ho')}
                                </label>
                                
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__group">
                                        <div class="input-group">
                                            <input id="cod_money_from" type="text" class="form-control number-input" name="cod_money_from" placeholder="{__d('admin', 'tu')}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fa fa-long-arrow-alt-right"></i></span>
                                            </div>
                                            <input id="cod_money_to" type="text" class="form-control number-input" name="cod_money_to" placeholder="{__d('admin', 'den')}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'phi_van_chuyen')}
                                </label>
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__group">
                                        <div class="input-group">
                                            <input id="shipping_fee_from" type="text" class="form-control number-input" name="shipping_fee_from" placeholder="{__d('admin', 'tu')}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fa fa-long-arrow-alt-right"></i></span>
                                            </div>
                                            <input id="shipping_fee_to" type="text" class="form-control number-input" name="shipping_fee_to" placeholder="{__d('admin', 'den')}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ngay_tao')}
                                </label>
                                <div class="input-daterange input-group">
                                    <input id="create_from" type="text" class="form-control kt_datepicker" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-ellipsis-h"></i>
                                        </span>
                                    </div>
                                    <input id="create_to" type="text" class="form-control kt_datepicker" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
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
                                        <button type="button" class="btn btn-brand btn-sm dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                            {__d('admin', 'trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            {foreach from = $this->ListConstantAdmin->listStatus() key = k_status item = status}
                                                <a class="dropdown-item nh-change-status-all" data-status="{$k_status}" href="javascript:;">
                                                    {$status}
                                                </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                  
                                    <button class="btn btn-sm btn-danger nh-delete-all mobile-mb-5" type="button">
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