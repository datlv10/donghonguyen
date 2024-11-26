<div id="discount-product-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'giam_gia_hang_loat')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="discount-product" action="{ADMIN_PATH}/product/discount-product" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>{__d('admin', 'khuyen_mai')} (%)</label>
                                <input type="number" name="discount_percent" value="" class="form-control" placeholder="{__d('admin', 'khuyen_mai')}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="html">{__d('admin', 'ap_dung_voi_toan_bo_san_pham')}</label>
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="all_product" value="1">
                                    {__d('admin', 'co')}
                                <span></span>
                            </label>
                            <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                <input type="radio" name="all_product" value="0" checked> 
                                    {__d('admin', 'khong')}
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ap_dung_voi_danh_muc')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-align-justify w-20px"></i>
                                </span>
                            </div>
                            {assign var = categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                                {TYPE} => {PRODUCT}, 
                                {LANG} => $lang
                            ])}

                            {assign var = categories_selected value = []}

                            {$this->Form->select('categories', $categories, ['id' => 'categories', 'empty' => null, 'default' => $categories_selected, 'class' => 'form-control kt-select-multiple', 'multiple' => 'multiple', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}"])}
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ap_dung_voi_thuong_hieu')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-align-justify w-20px"></i>
                                </span>
                            </div>
                            {assign var = brands value = $this->BrandAdmin->getListBrands()}
                            {assign var = brands_selected value = []}
                            {$this->Form->select('brands', $brands, ['id' => 'brands', 'empty' => null, 'default' => $brands_selected, 'class' => 'form-control kt-select-multiple', 'multiple' => 'multiple', 'data-placeholder' => "{__d('admin', 'chon_danh_muc')}"])}
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                            {__d('admin', 'dong')}
                        </button>
                        
                        <button id="btn-apply-discount-product" type="button" class="btn btn-sm btn-primary" >
                            <span class="icon-spinner spinner-grow spinner-grow-sm d-none"></span>
                            {__d('admin', 'ap_dung')}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>