<div class="row">
    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'so_san_pham_hien_thi')}
            </label>            
            <input name="config[{NUMBER_RECORD}]" value="{if !empty($config[{NUMBER_RECORD}])}{$config[{NUMBER_RECORD}]}{/if}" class="form-control form-control-sm" type="number">
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
                    <input type="radio" name="config[{HAS_PAGINATION}]" value="0" {if empty($config[{HAS_PAGINATION}])}checked="true"{/if}> 
                        {__d('admin', 'khong')}
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="config[{HAS_PAGINATION}]" value="1" {if !empty($config[{HAS_PAGINATION}])}checked="true"{/if}> 
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
                    {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfProduct(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>

                <div class="col-6">
                    {assign var = sort_type value = ''}
                    {if !empty($config[{SORT_TYPE}])}
                        {assign var = sort_type value = $config[{SORT_TYPE}]}
                    {/if}
                    <select name="config[{SORT_TYPE}]" class="form-control form-control-sm kt-selectpicker">
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
{if !empty($config[{DATA_TYPE}])}
    {assign var = data_type value = $config[{DATA_TYPE}]}
{/if}
<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'lay_du_lieu_theo')}
            </label>
            <select id="{DATA_TYPE}" name="config[{DATA_TYPE}]" class="form-control form-control-sm kt-selectpicker">
                <option value="">{__d('admin', 'tat_ca')}</option>

                <option value="{CATEGORY_PRODUCT}" {if $data_type == {CATEGORY_PRODUCT}}selected="true"{/if}>
                    {__d('admin', 'danh_muc_san_pham')}
                </option>

                <option value="{BRAND_PRODUCT}" {if $data_type == {BRAND_PRODUCT}}selected="true"{/if}>
                    {__d('admin', 'thuong_hieu')}
                </option>

                <option value="{PRODUCT}" {if $data_type == {PRODUCT}}selected="true"{/if}>
                    {__d('admin', 'chon_san_pham')}
                </option>

                <option value="{BY_URL}" {if $data_type == {BY_URL}}selected="true"{/if}>
                    {__d('admin', 'tu_dong_theo_duong_dan')}
                </option>

                <option value="{PRODUCTS_VIEWED}" {if $data_type == {PRODUCTS_VIEWED}}selected="true"{/if}>
                    {__d('admin', 'san_pham_da_xem')}
                </option>

                <option value="{WISHLIST_PRODUCT}" {if $data_type == {WISHLIST_PRODUCT}}selected="true"{/if}>
                    {__d('admin', 'san_pham_da_them_vao_yeu_thich')}
                </option>

                <option value="{COMPARE}" {if $data_type == {COMPARE}}selected="true"{/if}>
                    {__d('admin', 'san_pham_da_them_vao_so_sanh')}
                </option>
            </select>
        </div>
    </div>
</div>

{assign var = data value = []}
{if !empty($config.data_ids)}
    {assign var = data value = $config.data_ids}
{/if}

<div id="wrap-view-data">
    {if !empty($data_type)}
        {$this->element("../TemplateBlock/load_view_data", ['data_type' => $data_type, 'block_type' => $type, 'data' => $data])}
    {/if}
</div>