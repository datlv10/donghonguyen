<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/notification/add" class="btn btn-sm btn-brand">
                <i class="la la-plus"></i>
                {__d('admin', 'them_moi')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body">
            {$this->element('../Notification/search_advanced')}

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


<div id="sent-notification-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'gui_thong_bao')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h5 class="mb-30">
                    <span class="kt-font-bolder">
                        {__d('admin', 'thong_bao')}:
                    </span>

                    <span id="label-title" class="kt-font-bolder"></span>
                </h5>

                <div class="row">
                    <div class="col-lg-6 col-xs-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'nen_tang')}
                            </label>
                            {$this->Form->select('platform', $this->NotificationAdmin->listPlatform(), ['id' => 'platform' ,'empty' => null, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div id="wrap-token" class="form-group d-none">
                    <label>
                        Token
                    </label>
                    <input id="token" value="" class="form-control form-control-sm" type="text" autocomplete="off">
                </div>

                <input id="notification-id" value="" type="hidden">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-send-notification" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'gui_thong_bao')}
                </button>
            </div>
        </div>
    </div>
</div>