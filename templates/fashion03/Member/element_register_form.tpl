<div class="h2 text-uppercase text-center font-weight-bold pt-5">
    {__d('template', 'dang_ky')}
</div>
<form nh-form="member-register" id="member-register" action="/member/ajax-register" method="post" autocomplete="off">
    <div class="p-5">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="full_name" type="text" class="form-control required" placeholder="{__d('template', 'ho_va_ten')} *">
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="username" type="text" class="form-control required" placeholder="{__d('template', 'tai_khoan')} *">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="password" id="password-register" type="password" class="form-control required" placeholder="{__d('template', 'mat_khau')} *">
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="verify_password" type="password" class="form-control required" placeholder="{__d('template', 'xac_nhan_mat_khau')} *">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="email" type="text" class="form-control required" placeholder="{__d('template', 'email')} *">
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="phone" type="text" class="form-control required" placeholder="{__d('template', 'so_dien_thoai')} *">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    {$this->Form->select('city_id', $this->Location->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('template', 'tinh_thanh')} --", 'default' => '', 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    {$this->Form->select('district_id', $this->Location->getListDistrictForDropdown(), ['id' => 'district_id', 'empty' => "-- {__d('template', 'quan_huyen')} --", 'default' => '', 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    {$this->Form->select('ward_id', $this->Location->getListWardForDropdown(), ['id' => 'ward_id', 'empty' => "-- {__d('template', 'phuong_xa')} --", 'default' => '', 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group mb-5">
                    <input name="address" type="text" class="form-control required" placeholder="{__d('template', 'dia_chi')} *">
                </div>
            </div>
        </div>

        <button nh-btn-action="submit" class="btn btn-submit w-100 mb-4">
            {__d('template', 'dang_ky')}
        </button>
        <div class="text-center">
            {__d('template', 'ban_da_co_tai_khoan')}
            <a href="/member/login" class="font-weight-bold text-dark ml-2">
                {__d('template', 'dang_nhap')}
            </a>
        </div>
    </div>
</form>

