<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'thuoc_tinh_ap_dung_theo_danh_muc')}
            </h3>
        </div>
    </div>
</div>
<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'trang_thai_ap_dung_thuoc_tinh_theo_danh_muc')}
                </h3>
            </div>
        </div>

        <div class="kt-portlet__body">
            <form id="status-apply-category" action="{ADMIN_PATH}/setting/save/brands_category" method="POST" autocomplete="off">
                <div class="form-group mb-30">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="status" value="1" {if !empty($setting_brands_category.status)}checked{/if}>
                            {__d('admin', 'co')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="status" value="0" {if empty($setting_brands_category.status)}checked{/if}>
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                {* <div class="form-group">
                    <label class="kt-font-bold fs-11 text-danger">
                        {__d('admin', 'luu_y')}:
                        {__d('admin', 'khi_kich_hoat_trang_thai_thi_nhung_danh_muc_khong_cau_hinh_thuong_hieu_khi_them_moi_hoac_cap_nhat_se_khong_ap_dung_duoc_thuong_hieu')}
                    </label>
                </div> *}

                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'cap_nhat')}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="kt-grid kt-grid--desktop kt-grid--ver-desktop  kt-todo entire-brand" id="kt_todo">
        <div class="kt-grid__item kt-grid__item--fluid kt-todo__content" id="kt_todo_content">
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="kt-grid__item kt-grid__item--fluid  kt-portlet kt-portlet--height-fluid kt-todo__list" id="kt_todo_list">
                        <div class="kt-portlet__body kt-portlet__body--fit-x">
                            <div class="kt-todo__head">
                                <div class="kt-todo__toolbar">
                                    <div class="kt-searchbar">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="flaticon2-search"></i>
                                                </span>
                                            </div>
                                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" autocomplete="off">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary btn-search" type="button">{__d('admin', 'tim_kiem')}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-separator kt-separator--space-lg kt-margin-t-20 kt-margin-b-0"></div>
                            <div class="kt-todo__body">
                                <div class="kt-todo__items" data-type="task">
                                    <div class="kt-scroll" data-scroll="true" data-height="600">
                                        {$this->element('../Brand/list_category_element')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="kt-portlet kt-portlet--height-fluid">
                        {$this->element('../Brand/list_brands_element', ['brands' => $brands])}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>