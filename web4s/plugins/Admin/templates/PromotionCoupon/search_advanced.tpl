<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_ma_coupon')}..." autocomplete="off">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    {if empty($promotion)}
                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                            <div promotion-suggest id="customer-search" class="form-group mb-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="flaticon-search"></i>
                                        </span>
                                    </div>
                                    <input input-suggest value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem_chuong_trinh')}" shortcut="115">
                                    <input id="promotion_id" name="promotion_id" value="" type="hidden">                                
                                </div>
                            </div>
                        </div>
                    {/if}

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status', $this->ListConstantAdmin->listStatusPromotionCoupon(), ['id'=>'nh_status', 'empty' => {__d('admin', 'trang_thai')}, 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-undo-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>