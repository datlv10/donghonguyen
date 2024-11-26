<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}..." autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status', $this->ListConstantAdmin->listStatusArticle(), ['id'=>'nh_status', 'empty' => {__d('admin', 'trang_thai')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>