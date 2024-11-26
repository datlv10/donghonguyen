<div id="sync-all-product-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'dong_bo_tat_ca_san_pham_{0}', ['KiotViet'])}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="sync-product" action="{ADMIN_PATH}/product/kiotviet-sync-all-product" method="POST" autocomplete="off">
                    <div class="kt-widget4">
                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-box-open fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'so_san_pham_tu_{0}', ['KiotViet'])}
                            </span>

                            <span id="total_product" class="kt-widget4__number kt-font-info">
                                0
                            </span>
                        </div>

                        <div class="kt-widget4__item">
                            <span class="kt-widget4__icon">
                                <i class="fa fa-check-circle text-success fs-14"></i>
                            </span>

                            <span class="kt-widget4__title kt-widget4__title--light">
                                {__d('admin', 'so_san_pham_da_duoc_dong_bo_tu_{0}_ve_he_thong', ['KiotViet'])}
                            </span>

                            <span id="label-sync" class="kt-widget4__number kt-font-success">
                                0
                            </span>
                        </div>
                        <div class="kt-checkbox-list kt-widget4__item">
                            <label class="kt-checkbox mb-0">
                                <input type="checkbox" value="1" nh-product /> {__d('admin', 'dong_bo_san_pham_moi_tu_{0}', ['KiotViet'])}
                                <span></span>
                            </label>
                        </div>
                        <div class="kt-widget4__item red text-danger justify-content-sm-start">
                            <span class="kt-widget4__icon ">
                                <i class="fa fa-exclamation-circle fs-14 text-danger"></i>
                            </span>

                            <span class="font-weight-bold mr-2">
                                {__d('admin', 'luu_y')}:
                            </span>

                            {__d('admin', 'dong_bo_thuoc_tinh_truoc_khi_dong_bo_san_pham')}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                <button type="button" class="btn btn-sm btn-primary" nh-sync>
                    <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                    {__d('admin', 'ap_dung')}
                </button>
            </div>
        </div>
    </div>

    
</div>