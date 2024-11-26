<div id="modal-add-coupon-random" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_ma_ngau_nhien')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <form id="quick-add-coupon-form-random" promotion-suggest action="{ADMIN_PATH}/promotion/coupon/add-coupon" method="POST" autocomplete="off">
                <input type="hidden" name="promotion_id" value="{if !empty($promotion.id)}{$promotion.id}{/if}">
                <input type="hidden" name="type" value="1">
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
                            {__d('admin', 'so_luong_ma')}
                        </label>

                        <input id="total_code" name="total_code" placeholder="{__d('admin', 'toi_da_1000_ma')}" class="form-control form-control-sm w-50" type="text">
                    </div>

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

                    <div class="row">
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'tien_to')}
                                </label>
                                <input id="prefix" name="prefix" placeholder="VD: NH" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'so_ky_tu_ngau_nhien')}
                                    <span class="kt-font-danger">*</span>
                                </label>
                                <input id="length_code" name="length_code" placeholder="VD: 6" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'hau_to')}
                                </label>
                                <input id="suffixes" name="suffixes" placeholder="VD: SALE" class="form-control" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <small>{__d('admin', 'ma_cua_ban_se_co_dinh_dang')}: NHXXXXXXSALE ({__d('admin', 'voi_x_la_so_ky_tu_ngau_nhien')})</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        {__d('admin', 'dong')}
                    </button>
                    
                    <button id="quick-add-coupon-random" type="button" class="btn btn-sm btn-primary">
                        {__d('admin', 'them_ma')}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>