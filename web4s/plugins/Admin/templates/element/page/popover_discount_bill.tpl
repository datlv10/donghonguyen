<div id="popover-discount-bill" class="d-none">
    <div class="form-group">
        <label>
            {__d('admin', 'chiet_khau')}
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span data-discount-type="{MONEY}" class="input-group-text w-auto cursor-p discount-type-item active">$</span>
                <span data-discount-type="{PERCENT}" class="input-group-text w-auto discount-type-item cursor-p">%</span>
            </div>
            <input id="discount" class="form-control form-control-sm text-right number-input" type="text" value="">
        </div>
    </div>

    <div role="separator" class="dropdown-divider"></div>

    <div class="form-group">
        <label>
            {__d('admin', 'ly_do')}
        </label>
        
        <input id="note" value="" class="form-control form-control-sm" type="text">
    </div>

    <div class="form-group mb-0">
        <button id="confirm-discount-bill" type="button" class="btn btn-sm btn-brand mr-10">
            {__d('admin', 'dong_y')}
        </button>

        <button id="cancel-discount-bill" type="button" class="btn btn-sm btn-secondary">
            {__d('admin', 'huy_bo')}
        </button>
    </div>
</div>