<div class="nh-search-advanced">
    <div class="kt-form">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input id="nh-keyword" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}...">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        {$this->Form->select('status', $this->ListConstantAdmin->listStatusProduct(), ['id' => 'nh_status', 'empty' => "-- {__d('admin', 'trang_thai')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                    
                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                        {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                            {TYPE} => 'product', 
                            {LANG} => $lang
                        ])}
                        {$this->Form->select('id_categories', $list_categories, ['id' => 'id_categories', 'empty' => "-- {__d('admin', 'danh_muc')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>                    

                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-icon collapse-search-advanced" data-toggle="collapse" data-target="#collapse-search-advanced">
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <button id="btn-refresh-search" type="button" class="btn btn-outline-secondary btn-sm btn-icon">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="collapse-search-advanced" class="collapse collapse-search-advanced-content">
        <div class="kt-margin-t-20">
            <div class="form-group row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'muc_gia')}
                        </label>
                        
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__group">
                                <div class="input-group">
                                    <input id="price_from" type="text" class="form-control number-input" name="price_from" placeholder="{__d('admin', 'tu')}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-long-arrow-alt-right"></i></span>
                                    </div>
                                    <input id="price_to" type="text" class="form-control number-input" name="price_to" placeholder="{__d('admin', 'den')}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'thuong_hieu')}
                        </label>
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {assign var = list_brands value = $this->BrandAdmin->getListBrands()}
                                {$this->Form->select('id_brands', $list_brands, ['id'=>'id_brands', 'empty' => "-- {__d('admin', 'chon')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'danh_dau')}
                        </label>
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                <select name="product_mark" id="product_mark" class="form-control form-control-sm kt-selectpicker">
                                    <option value="" selected="selected">
                                        -- {__d('admin', 'chon')} --
                                    </option>

                                    <option value="featured">
                                        {__d('admin', 'san_pham_noi_bat')}
                                    </option>

                                    <option value="discount">
                                        {__d('admin', 'san_pham_giam_gia')}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'nguoi_tao')}
                        </label>
                        <div class="kt-form__control">
                            {$this->Form->select('created_by', $this->UserAdmin->getListUser(), ['id' => 'created_by', 'empty' => "-- {__d('admin', 'chon')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tinh_trang')}
                        </label>
                        <div class="kt-form__control">
                            <select name="stocking" id="stocking" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>

                                <option value="0">
                                    {__d('admin', 'het_hang')}
                                </option>

                                <option value="1">
                                    {__d('admin', 'con_hang')}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ngay_tao')}
                        </label>
                        <div class="input-daterange input-group">
                            <input id="create_from" type="text" class="form-control kt_datepicker" name="create_from" placeholder="{__d('admin', 'tu')}" autocomplete="off" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-ellipsis-h"></i>
                                </span>
                            </div>
                            <input id="create_to" type="text" class="form-control kt_datepicker" name="create_to" placeholder="{__d('admin', 'den')}" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>