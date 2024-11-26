<div id="popover-price-special" class="d-none">
    <div class="form-group">
        <label>
            {__d('admin', 'gia_dac_biet')}
        </label>
        <input id="price-special" value="" class="form-control form-control-sm number-input" type="text">

        <span class="form-text text-muted">
            {__d('admin', 'gia_ban_le_san_pham_cho_chuong_trinh_khuyen_mai')}
        </span>
    </div>

    <div class="form-group">
        <label>
            {__d('admin', 'ngay_khuyen_mai')}
        </label>
        
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-calendar-alt w-20px"></i>
                </span>
            </div>

            <input id="date-special" value="" class="form-control form-control-sm date-ranger-picker fs-11" readonly="true" type="text" style="height: 35px;">

            <div id="delete-special-price" class="input-group-append" style="cursor: pointer;">
                <span class="input-group-text">
                    <i class="fa fa-times w-20px"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="form-group mb-0">
        <button id="confirm-special-price" type="button" class="btn btn-sm btn-brand mr-5">
            {__d('admin', 'dong_y')}
        </button>

        <button id="cancel-special-price" type="button" class="btn btn-sm btn-secondary mr-5">
            {__d('admin', 'huy_bo')}
        </button>
    </div>
</div>