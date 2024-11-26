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
                        {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForDropdown([
                            {TYPE} => 'article', 
                            {LANG} => $lang
                        ])}
                        {$this->Form->select('id_categories', $list_categories, ['id'=>'id_categories', 'empty' => "-- {__d('admin', 'danh_muc')} --", 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>   

                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
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
                            {__d('admin', 'bai_viet_noi_bat')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="featured" id="featured" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0">{__d('admin', 'khong')}</option>
                                <option value="1">{__d('admin', 'co')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'co_album')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="has_album" id="has_album" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0">{__d('admin', 'khong')}</option>
                                <option value="1">{__d('admin', 'co')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'co_video')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="has_video" id="has_video" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0">{__d('admin', 'khong')}</option>
                                <option value="1">{__d('admin', 'co')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'co_tep_dinh_kem')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="has_file" id="has_file" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0">{__d('admin', 'khong')}</option>
                                <option value="1">{__d('admin', 'co')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'hien_thi_muc_luc')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="catalogue" id="catalogue" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0">{__d('admin', 'khong')}</option>
                                <option value="1">{__d('admin', 'co')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'seo')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="seo_score" id="seo_score" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="success">{__d('admin', 'tot')}</option>
                                <option value="warning">{__d('admin', 'binh_thuong')}</option>
                                <option value="danger">{__d('admin', 'chua_dat')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tu_khoa')}
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="keyword_score" id="keyword_score" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">{__d('admin', 'chua_co')}</option>
                                <option value="success">{__d('admin', 'tot')}</option>
                                <option value="warning">{__d('admin', 'binh_thuong')}</option>
                                <option value="danger">{__d('admin', 'chua_dat')}</option>
                            </select>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>