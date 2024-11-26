<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar align-self-center">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <div class="dropdown dropdown-inline">
                        <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> {__d('admin', 'xuat_excel')}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 250px;">
                            <ul class="kt-nav">
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="current">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_trang_hien_tai')}</span>
                                    </a>
                                </li>
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="all">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_toan_bo_cac_trang')}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">

        <div class="kt-portlet__body">
            <div class="nh-search-advanced">
                <div class="kt-form">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')} ..." autocomplete="off">
                                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                            <span><i class="la la-search"></i></span>
                                        </span>
                                    </div>
                                </div>  

                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-form__group">
                                        <select name="status" id="nh_status" class="form-control form-control-sm kt-selectpicker">
                                            <option value="" selected="selected">
                                                -- {__d('admin', 'trang_thai')} --
                                            </option>
                                            <option value="1">{__d('admin', 'da_doc')}</option>
                                            <option value="2">{__d('admin', 'chua_doc')}</option>
                                        </select>
                                    </div>
                                </div>  

                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    {assign var = list_form value = $this->ContactFormAdmin->getListForm()}
                                    {$this->Form->select('form_id', $list_form, ['id'=>'form_id', 'empty' => "-- {__d('admin', 'ten_form')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div> 

                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-form__group">
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
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
    </div>
</div>

{$this->element('Admin.page/popover_quick_change')}