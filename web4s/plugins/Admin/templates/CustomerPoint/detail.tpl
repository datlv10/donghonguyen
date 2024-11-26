{if !empty($customer_point)}
    {assign var = url_list value = "{ADMIN_PATH}/customer/point"}

    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-sm btn-default">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            </div>
        </div>
    </div>


    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_khach_hang')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ho_va_ten')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer_point.full_name)}
                                        {$customer_point.full_name}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'email')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer_point.email)}
                                        {$customer_point.email}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'so_dien_thoai')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer_point.phone)}
                                        {$customer_point.phone}
                                    {/if}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'diem_hien_tai')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder text-primary">
                                    {if !empty($customer_point.point)}
                                        {$customer_point.point|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}
                                    <small class="fs-13 text-lowercase">{__d('admin', 'diem')}</small>
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'diem_khuyen_mai')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer_point.point_promotion)}
                                        {$customer_point.point_promotion|number_format:0:".":","}
                                    {else}
                                        0
                                    {/if}
                                    <small class="fs-13 text-lowercase">{__d('admin', 'diem')}</small>

                                    {if !empty($customer_point.expiration_time) && !empty($customer_point.point_promotion)}
                                        <span class="text-danger">({__d('admin', 'thoi_gian_su_dung_den')}: {$this->UtilitiesAdmin->convertIntgerToDateTimeString($customer_point.expiration_time)})</span>
                                    {/if}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'lich_su_tich_diem')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                {$this->element('../CustomerPoint/search_advanced')}
            </div>

            <div class="kt-portlet__body p-0">
                <div class="kt-datatable"></div>
                <input type="hidden" name="customer_id" value="{$customer_point.customer_id}">
            </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">
        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
    </span>
{/if}

{$this->element('Admin.page/popover_view_give_point')}