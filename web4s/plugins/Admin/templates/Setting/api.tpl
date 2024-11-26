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

            <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>

            <!--begin::Form-->
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-lg-12">
                                    {__d('admin', 'ma_bao_mat')}
                                </label>
                                <div class="input-group col-lg-10">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fab fa-battle-net"></i>
                                        </span>
                                    </div>
                                    <input id="secret_key" name="secret_key" value="{if !empty($api.secret_key)}{$api.secret_key}{/if}" class="form-control" type="text">
                                    <div class="input-group-append">
                                        <button data-toggle="kt-tooltip" data-original-title="{__d('admin', 'tao_ma_moi')}" class="btn btn-secondary nh-random" type="button"><i class="fa fa-dice"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Form-->
        </div>
    </form>
</div>