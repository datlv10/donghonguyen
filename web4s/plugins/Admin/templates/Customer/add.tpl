{assign var = url_list value = "{ADMIN_PATH}/customer"}
{assign var = url_add value = "{ADMIN_PATH}/customer/add"}
{assign var = url_edit value = "{ADMIN_PATH}/customer/update/"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/customer/save" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ho_va_ten')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-tie"></i>
                                </span>
                            </div>
                            <input name="full_name" value="" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'so_dien_thoai')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input name="phone" value="" type="text" class="form-control form-control-sm phone-input">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'email')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>
                            <input name="email" value="" type="text" class="form-control form-control-sm" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ma_khach_hang')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </span>
                            </div>
                            <input name="code" value="" class="form-control form-control-sm" type="text">
                        </div>
                        <span class="form-text text-muted">
                            {__d('admin', 'neu_khong_nhap_he_thong_se_tu_dong_sinh_ra_ma')}
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ngay_sinh')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-birthday-cake"></i>
                                </span>
                            </div>
                            <input name="birthday" value="" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'gioi_tinh')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="sex" value="male"> {__d('admin', 'nam')}
                                <span></span>
                            </label>
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="sex" value="female" > {__d('admin', 'nu')}
                                <span></span>
                            </label>
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="sex" value="other" checked> {__d('admin', 'khac')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'nhan_vien_cham_soc')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        {$this->Form->select('staff_id', $this->UserAdmin->getListUser(), ['name'=>'staff_id', 'empty' => "{__d('admin', 'lua_chon')} ...", 'class' => 'form-control form-control-sm kt-selectpicker'])} 
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                        <input id="create-account" type="checkbox"> 
                        {__d('admin', 'tao_tai_khoan')}
                        <span></span>
                    </label> 
                </div>
            </div>    

            <div id="wrap-create-account" class="collapse">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'tai_khoan')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div class="form-group row">
                        <label class="col-lg-2 col-xl-2 col-form-label">
                            {__d('admin', 'ten_dang_nhap')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <div class="col-lg-10 col-xl-5">
                            <input id="username" name="username" value="" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-xl-2 col-form-label">
                            {__d('admin', 'mat_khau')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <div class="col-lg-10 col-xl-5">
                            <input id="password" name="password" value="" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                    <input name="status_account" value="1" type="hidden">
                </div>
            </div>

            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'dia_chi')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'tinh_thanh_thanh_pho')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5'])}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'quan_huyen')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        {$this->Form->select('district_id', [], ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5'])}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'phuong_xa')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        {$this->Form->select('ward_id', [], ['id' => 'ward_id', 'empty' => "-- {__d('admin', 'phuong_xa')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5'])}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'dia_chi')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <input name="address" value="" class="form-control form-control-sm" type="text">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'zip_code')}
                    </label>
                    <div class="col-lg-10 col-xl-5">
                        <input name="zip_code" value="" class="form-control form-control-sm" type="text">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>