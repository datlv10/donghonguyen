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

                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('generation', $list_generation, ['id'=>'generation', 'empty' => 'Thuộc đời', 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('status', $this->ListConstantAdmin->listStatusGenealogy(), ['id'=>'nh_status', 'empty' => 'Tình trạng', 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('genealogical', $this->ListConstantAdmin->listGenealogicalGenealogy(), ['id'=>'genealogical', 'empty' => 'Thuộc phả đồ', 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
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
                            Giới tính
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="sex" id="sex" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>    
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            Độ tuổi
                        </label>
                        
                        <div class="kt-form__group">
                            <select name="birthday" id="birthday" class="form-control form-control-sm kt-selectpicker">
                                <option value="" selected="selected">
                                    -- {__d('admin', 'chon')} --
                                </option>
                                <option value="0-4">0-4 tuổi</option>
                                <option value="5-9">5-9 tuổi</option>
                                <option value="10-14">10-14 tuổi</option>
                                <option value="15-19">15-19 tuổi</option>
                                <option value="20-24">20-24 tuổi</option>
                                <option value="25-29">25-29 tuổi</option>
                                <option value="30-34">30-34 tuổi</option>
                                <option value="35-39">35-39 tuổi</option>
                                <option value="40-44">40-44 tuổi</option>
                                <option value="45-49">45-49 tuổi</option>
                                <option value="50-54">50-54 tuổi</option>
                                <option value="55-59">55-59 tuổi</option>
                                <option value="60-64">60-64 tuổi</option>
                                <option value="65-69">65-69 tuổi</option>
                                <option value="70-74">70-74 tuổi</option>
                                <option value="75-79">75-79 tuổi</option>
                                <option value="80-84">80-84 tuổi</option>
                                <option value="85">Trên 85 tuổi</option>
                            </select>
                        </div>    
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tinh_thanh_thanh_pho')}
                        </label>
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('city_id', $this->LocationAdmin->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'autocomplete' => 'off'])}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'quan_huyen')}
                        </label>
                        <div class="kt-form__group">
                            <div class="kt-form__control">
                                {$this->Form->select('district_id', [], ['id' => 'district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => '', 'class' => 'form-control form-control-sm kt-selectpicker', 'data-size' => '5', 'autocomplete' => 'off'])}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>