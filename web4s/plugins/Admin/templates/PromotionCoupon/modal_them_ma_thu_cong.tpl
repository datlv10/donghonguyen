<div id="modal-add-coupon-handmade" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_ma_thu_cong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <form id="quick-add-coupon-form" promotion-suggest action="{ADMIN_PATH}/promotion/coupon/add-coupon" method="POST" autocomplete="off">
                <input type="hidden" name="promotion_id" value="{if !empty($promotion.id)}{$promotion.id}{/if}">
                <input type="hidden" name="type" value="0">
                <div class="modal-body">

                    {if empty($promotion.id)}
                        <div id="customer-search" class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="flaticon-search"></i>
                                    </span>
                                </div>
                                <input input-suggest value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'ten_chuong_trinh')}" shortcut="115">                                
                            </div>
                        </div>
                    {/if}

                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_lan_su_dung')}
                        </label>

                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="use_check" value="1" > 
                                {__d('admin', 'so_lan')}
                                <span></span>
                            </label>
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="use_check" value="0" checked> 
                                {__d('admin', 'khong_gio_han')}
                                <span></span>
                            </label>

                            <input id="number_use" name="number_use" placeholder="{__d('admin', 'so_lan_su_dung')}" class="form-control form-control-sm number-use mt-10 w-50" type="hidden">
                        </div>
                    </div>

                    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma_coupon')}
                            <span class="kt-font-danger">*</span>
                        </label>
                    </div>

                    <div id="wrap-item-coupon" data-scroll="true" data-scrollbar-shown="false">
                        <div class="form-group wrap-item">
                            <div class="input-group">
                                <input id="code" data-name="code" name="code" placeholder="{__d('admin', 'toi_da_6_ky_tu')}" class="form-control" type="text">
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn-delete-item">
                                        <i class="la la-trash-o text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <span id="add_coupon" class="kt-nav__link text-primary kt-pointer fs-13" shortcut="13">
                            <i class="la la-plus"></i>
                            <span class="kt-nav__link-text">
                                {__d('admin', 'them_ma_khac')} (Enter)
                            </span>
                        </span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        {__d('admin', 'dong')}
                    </button>
                    
                    <button id="quick-add-coupon" type="button" class="btn btn-sm btn-primary">
                        {__d('admin', 'them_ma')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>