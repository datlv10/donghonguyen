{assign var = url_list value = "{ADMIN_PATH}/customer/point"}
{assign var = url_add value = "{ADMIN_PATH}/customer/point-history/add"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {if !empty($url_add)}
                <div class="btn-group">
                    <button data-link="{$url_add}" id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                        <i class="la la-plus"></i>
                        {__d('admin', 'them_moi')} (F1)
                    </button>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/customer/point-history/save" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div nh-wrap-select="{CUSTOMER}">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'thong_tin_khach_hang')}
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="flaticon-search"></i>
                                        </span>
                                    </div>
                                    <input suggest-item="{CUSTOMER}" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_khach_hang')}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div nh-item-selected class="clearfix"></div>
                        <input type="text" class="kt-hidden" value="" name="customer_id">
                        <input type="text" class="kt-hidden" value="" name="point">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'loai_giao_dich')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="point_type" value="1"> {__d('admin', 'diem_mac_dinh')}
                                <span></span>
                            </label>
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="point_type" value="0" checked> {__d('admin', 'diem_thuong')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'hanh_dong')}
                    </label>
                    <div class="col-lg-8 col-xl-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select class="form-control form-control-sm kt-selectpicker" name="action">
                                    <option value="1">{__d('admin', 'cong_diem')}</option>
                                    <option value="0">{__d('admin', 'tru_diem')}</option>
                                </select>
                            </div>
                            <input name="point" value="" type="text" class="form-control form-control-sm number-input">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ghi_chu')}
                    </label>
                    <div class="col-lg-8 col-xl-4">
                        <textarea class="form-control form-control-sm" name="note"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>