<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-database mr-5"></i>
                {__d('admin', 'cau_hinh_du_lieu')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form action="{ADMIN_PATH}/mobile-app/block/save-data-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_san_pham_hien_thi')}
                        </label>            
                        <input name="{NUMBER_RECORD}" value="{if !empty($config_data[{NUMBER_RECORD}])}{$config_data[{NUMBER_RECORD}]}{/if}" class="form-control form-control-sm" type="number">
                        <span class="form-text text-muted">
                            {__d('admin', 'so_luong_ban_ghi_se_hien_thi_trong_block')}
                        </span>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'su_dung_phan_trang')}
                        </label>
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                <input type="radio" name="{HAS_PAGINATION}" value="0" {if empty($config_data[{HAS_PAGINATION}])}checked="true"{/if}> 
                                    {__d('admin', 'khong')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="{HAS_PAGINATION}" value="1" {if !empty($config_data[{HAS_PAGINATION}])}checked="true"{/if}> 
                                    {__d('admin', 'co')}
                                <span></span>
                            </label>

                            <span class="form-text text-muted">
                                {__d('admin', 'neu_su_dung_phan_trang_thi_cau_hinh_so_san_pham_se_la_so_san_pham_tren_1_trang')}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'sap_xep_theo')}
                        </label>
                        <div class="row">
                            <div class="col-6">
                                {$this->Form->select("{SORT_FIELD}", $this->TemplateAdmin->getListSortFieldOfProduct(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config_data[{SORT_FIELD}])}{$config_data[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>

                            <div class="col-6">
                                {assign var = sort_type value = ''}
                                {if !empty($config_data[{SORT_TYPE}])}
                                    {assign var = sort_type value = $config_data[{SORT_TYPE}]}
                                {/if}
                                <select name="{SORT_TYPE}" class="form-control form-control-sm kt-selectpicker">
                                    <option value="{DESC}" {if $sort_type == {DESC}}selected="true"{/if}>
                                        {__d('admin', 'giam_dan')}
                                    </option>

                                    <option value="{ASC}" {if $sort_type == {ASC}}selected="true"{/if}>
                                        {__d('admin', 'tang_dan')}
                                    </option>                        
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {assign var = data_type value = ''}
            {if !empty($config_data[{DATA_TYPE}])}
                {assign var = data_type value = $config_data[{DATA_TYPE}]}
            {/if}

            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'lay_du_lieu_theo')}
                        </label>
                        <select id="{DATA_TYPE}" name="{DATA_TYPE}" class="form-control form-control-sm kt-selectpicker">
                            <option value="">
                                {__d('admin', 'tat_ca')}
                            </option>

                            <option value="{CATEGORY_PRODUCT}" {if $data_type == {CATEGORY_PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'danh_muc_san_pham')}
                            </option>

                            <option value="{BY_PAGE_ID}" {if $data_type == {BY_PAGE_ID}}selected="true"{/if}>
                                {__d('admin', 'tu_dong_theo_trang')}
                            </option>

                            <option value="{PRODUCT}" {if $data_type == {PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'chon_san_pham')}
                            </option>

                            <option value="{PRODUCTS_VIEWED}" {if $data_type == {PRODUCTS_VIEWED}}selected="true"{/if}>
                                {__d('admin', 'san_pham_da_xem')}
                            </option>

                            <option value="{WISHLIST_PRODUCT}" {if $data_type == {WISHLIST_PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'san_pham_yeu_thich')}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {assign var = data value = []}
            {if !empty($config_data.data_ids)}
                {assign var = data value = $config_data.data_ids}
            {/if}

            <div id="wrap-view-data">
                {if !empty($data_type)}
                    {$this->element("../MobileTemplateBlock/load_view_data", ['data_type' => $data_type, 'block_type' => $type, 'data' => $data])}
                {/if}
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-tablet-alt mr-5"></i>
                {__d('admin', 'cau_hinh_giao_dien')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form action="{ADMIN_PATH}/mobile-app/block/save-layout-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tieu_de_hien_thi')}
                        </label>
                        <input name="title" value="{if !empty($config_layout.title)}{$config_layout.title}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'dem_nguoc_thoi_gian')}
                        </label>
                        <input name="countdown_timer" value="{if !empty($config_layout.countdown_timer)}{$config_layout.countdown_timer}{/if}" type="text" class="form-control form-control-sm datetime-picker">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ti_le_chieu_cao_anh')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-arrows-alt-v"></i>
                                </span>
                            </div>
                            <input name="image_height" value="{if !empty($config_layout.image_height)}{$config_layout.image_height}{/if}" class="form-control form-control-sm" type="number">
                        </div>            
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ti_le_chieu_rong_anh')}
                        </label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-arrows-alt-h"></i>
                                </span>
                            </div>
                            <input name="image_width" value="{if !empty($config_layout.image_width)}{$config_layout.image_width}{/if}" class="form-control form-control-sm" type="number">
                        </div>            
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'chuyen_huong_xem_them')}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-external-link-alt"></i>
                                </span>
                            </div>
                            <input name="action" value="{if !empty($config_layout.action)}{htmlentities($config_layout.action)}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                        <span class="form-text text-muted">{literal}{"page_type": "product", "product_id" : 10, "params": "{\"status\":\"featured\", \"limit\": 1}"}{/literal}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Mobile)</b>
                        </label>            
                        <input name="number_on_line" value="{if !empty($config_layout.number_on_line)}{$config_layout.number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Ipad/Tablet)</b>
                        </label>            
                        <input name="ipad_number_on_line" value="{if !empty($config_layout.ipad_number_on_line)}{$config_layout.ipad_number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>           
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'dinh_dang')}
                        </label>        
                        {$this->Form->select('format', $this->MobileTemplateAdmin->listFormatItem(), ['empty' => null, 'default' => "{if !empty($config_layout.format)}{$config_layout.format}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            Element
                        </label>        
                        {$this->Form->select('element', $this->MobileTemplateAdmin->listElementProduct(), ['empty' => null, 'default' => "{if !empty($config_layout.element)}{$config_layout.element}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            Style View
                        </label>        
                        {$this->Form->select('style_view', $this->MobileTemplateAdmin->listStyleView(), ['empty' => null, 'default' => "{if !empty($config_layout.style_view)}{$config_layout.style_view}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
            </div>

            {$this->element("../MobileTemplateBlock/config_color_block", ['config_layout' => $config_layout])}

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            {$this->element("../MobileTemplateBlock/config_block_spacing", ['config_layout' => $config_layout])}
            {$this->element("../MobileTemplateBlock/config_item_spacing", ['config_layout' => $config_layout])}

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

