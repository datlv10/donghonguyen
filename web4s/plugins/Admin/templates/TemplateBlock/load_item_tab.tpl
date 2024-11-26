<div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item kt-portlet--collapse">
    <div class="kt-portlet__head p-5">
        <div class="kt-portlet__head-label ml-5">
            <h3 class="kt-portlet__head-title header-item">
                {assign var = first_lang value = $list_language|@key}
                {assign var = key_first_name value = "name_{$first_lang}"}
                
                {if !empty($item.$key_first_name)}
                    {$item.$key_first_name}
                {else}
                    New Item
                {/if}
            </h3>
        </div>

        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">                        
                <span class="btn btn-sm btn-icon btn-danger btn-icon-md m-0 btn-delete-item">
                    <i class="la la-trash-o"></i>
                </span>

                <span class="btn btn-sm btn-icon btn-info btn-icon-md m-0 btn-toggle-item">
                    <i class="la la-angle-down"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="kt-portlet__body p-10" style="{if !empty($item)}display: none;{/if}">
        <div class="row">
            <div class="col-lg-6 col-12">
                {if !empty($list_language)}
                    {foreach from = $list_language item = language key = k_lang name = title_item}
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tieu_de')}
                                ({$language})
                                <span class="kt-font-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <i class="fa fa-align-left"></i>
                                        </div>
                                    </span>
                                </div>

                                {assign var = key_name value = "name_{$k_lang}"}
                                <input name="" data-name="name_{$k_lang}" value="{if !empty($item.$key_name)}{$item.$key_name}{/if}" class="form-control form-control-sm required {if $smarty.foreach.title_item.first}item-name{/if}" type="text">

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                {assign var = data_type value = ''}
                {if !empty($item[{DATA_TYPE}])}
                    {assign var = data_type value = $item[{DATA_TYPE}]}
                {/if}
                {if !empty($type) && $type == {TAB_PRODUCT}}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'lay_du_lieu_theo')}
                        </label>
                        <select id="type_tag" name="" data-name="{DATA_TYPE}" class="form-control form-control-sm kt-selectpicker">
                            <option value="">{__d('admin', 'tat_ca')}</option>

                            <option value="{CATEGORY_PRODUCT}" {if $data_type == {CATEGORY_PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'danh_muc_san_pham')}
                            </option>

                            <option value="{BRAND_PRODUCT}" {if $data_type == {BRAND_PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'thuong_hieu')}
                            </option>

                            <option value="{BY_URL}" {if $data_type == {BY_URL}}selected="true"{/if}>
                                {__d('admin', 'tu_dong_theo_trang')}
                            </option>

                            <option value="{PRODUCT}" {if $data_type == {PRODUCT}}selected="true"{/if}>
                                {__d('admin', 'chon_san_pham')}
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
                {/if}

                {if !empty($type) && $type == {TAB_ARTICLE}}
                    <div class="form-group">
                        <label>
                            {__d('admin', 'lay_du_lieu_theo')}
                        </label>
                        <select id="type_tag" name="" data-name="{DATA_TYPE}" class="form-control form-control-sm kt-selectpicker">
                            <option value="">{__d('admin', 'tat_ca')}</option>

                            <option value="{CATEGORY_ARTICLE}" {if $data_type == {CATEGORY_ARTICLE}}selected="true"{/if}>
                                {__d('admin', 'danh_muc_bai_viet')}
                            </option> 

                            <option value="{BY_URL}" {if $data_type == {BY_URL}}selected="true"{/if}>
                                {__d('admin', 'tu_dong_theo_trang')}
                            </option>    

                            <option value="{ARTICLE}" {if $data_type == {ARTICLE}}selected="true"{/if}>
                                {__d('admin', 'chon_bai_viet')}
                            </option>
                            
                            <option value="{ARTICLES_VIEWED}" {if $data_type == {ARTICLES_VIEWED}}selected="true"{/if}>
                                {__d('admin', 'bai_viet_da_xem')}
                            </option>

                            <option value="{WISHLIST_ARTICLE}" {if $data_type == {WISHLIST_ARTICLE}}selected="true"{/if}>
                                {__d('admin', 'bai_viet_yeu_thich')}
                            </option>                       
                        </select>
                    </div>
                {/if}

                <div id="wrap-data-tab">
                    {assign var = record_selected value = []}
                    {if !empty($item.sub_categories_id)}
                        {assign var = record_selected value = $item.sub_categories_id}
                    {/if}

                    {if !empty($item.data_ids)}
                        {assign var = record_selected value = $item.data_ids}
                    {/if}

                    {if !empty($item.data_type)}
                        {$this->element("../TemplateBlock/load_view_data_for_tab", [
                            'type' => {$item.data_type}, 
                            'record_selected' => $record_selected
                        ])}
                    {/if}
                </div>
            </div>
            
            <div class="col-lg-6 col-12">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'sap_xep_theo')}
                            </label>
                            
                            <div class="row">
                                <div class="col-6">
                                    {if !empty($type) && $type == {TAB_PRODUCT}}
                                        {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfProduct(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => {SORT_FIELD}])}
                                    {/if}
                                    {if !empty($type) && $type == {TAB_ARTICLE}}
                                        {$this->Form->select("{SORT_FIELD}", $this->TemplateAdmin->getListSortFieldOfArticle(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($item[{SORT_FIELD}])}{$item[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => {SORT_FIELD}])}
                                    {/if}
                                </div>

                                <div class="col-6">
                                    {assign var = sort_type value = ''}
                                    {if !empty($item[{SORT_TYPE}])}
                                        {assign var = sort_type value = $item[{SORT_TYPE}]}
                                    {/if}
                                    <select name="" data-name="{SORT_TYPE}" class="form-control form-control-sm kt-selectpicker">
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

                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'su_dung_phan_trang')}
                            </label>
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="" data-name="{HAS_PAGINATION}" value="0" {if empty($item[{HAS_PAGINATION}])}checked="true"{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="" data-name="{HAS_PAGINATION}" value="1" {if !empty($item[{HAS_PAGINATION}])}checked="true"{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label>
                        {__d('admin', 'giao_dien_tab_child')}
                    </label>

                    {if !empty($type) && $type == {TAB_PRODUCT}}
                        {$this->Form->select('view', $this->BlockAdmin->getListViewBlock({PRODUCT}, 'view'), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($item.view_child)}{$item.view_child}{else}view.tpl{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => 'view_child'])}
                        <span class="form-text text-muted">
                            {__d('admin', 'giao_dien_cua_block_san_pham')}
                        </span>
                    {/if}

                    {if !empty($type) && $type == {TAB_ARTICLE}}
                        {$this->Form->select('view', $this->BlockAdmin->getListViewBlock({ARTICLE}, 'view'), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($item.view_child)}{$item.view_child}{else}view.tpl{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => 'view_child'])}
                    {/if}
                </div>
            </div>

        </div>
    </div>
</div>