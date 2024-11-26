{assign var = type_menu value = ''}
{if !empty($item.type)}
    {assign var = type_menu value = $item.type}
{/if}                

<div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item {if !empty($item)}kt-portlet--collapse{/if}">
    <div class="kt-portlet__head p-5">
        <div class="kt-portlet__head-label ml-5">
            <h3 class="kt-portlet__head-title">
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
                <span class="btn btn-sm btn-icon btn-secondary btn-icon-md m-0 btn-delete-item">
                    <i class="la la-trash-o"></i>
                </span>

                <span class="btn btn-sm btn-icon btn-secondary btn-icon-md m-0 btn-toggle-item">
                    <i class="la la-angle-down"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="kt-portlet__body p-10" style="{if !empty($item)}display: none;{/if}">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <label>
                        {__d('admin', 'loai_menu')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    {$this->Form->select('', $this->BlockAdmin->getAllTypeMenuDropdown(), ['id' => 'menu-type', 'data-name' => 'type', 'empty' => "{__d('admin', 'chon')}", 'default' => "{$type_menu}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                </div>
            </div>

            <div  id="wrap-categories"class="col-lg-6 col-12">
                {if !empty($item.category_id)}
                    {assign var = type_category value = {$type_menu|replace:'category_':''}}
                    {$this->element("../TemplateBlock/load_dropdown_categories", [
                        'category_id' => $item.category_id,
                        'lang' => $lang,
                        'type' => $type_category
                    ])}
                {/if}
            </div>            
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-10"></div>

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

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                Class Item
                            </label>

                            <input name="" data-name="class_item" value="{if !empty($item.class_item)}{$item.class_item}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <label></label>
                        <div class="kt-checkbox-inline">
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mt-10">
                                <input name="" data-name="blank_link" value="1" type="checkbox" {if !empty($item.blank_link)}checked{/if}> 
                                {__d('admin', 'mo_duong_dan_tren_tab_moi')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>            

            <div class="col-lg-6 col-12">
                <div id="wrap-list-url" class="clearfix {if $type_menu != {CUSTOM}}d-none{/if}">
                    {if !empty($list_language)}
                        {foreach from = $list_language item = language key = k_lang}
                            <div class="form-group ">
                                <label>
                                    {__d('admin', 'duong_dan')}
                                    ({$language})
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <div class="list-flags">
                                                <i class="fa fa-link"></i>
                                            </div>
                                        </span>
                                    </div>

                                    {assign var = key_url value = "url_{$k_lang}"}
                                    <input name="" data-name="url_{$k_lang}" value="{if !empty($item.$key_url)}{$item.$key_url}{/if}" class="form-control form-control-sm" type="text">
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
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chon_anh_slider')}
                            </label>

                            <div class="clearfix">
                                <div btn-select-media-block="template" action="preview" data-src="{ADMIN_PATH}/myfilemanager/?type_file=image&cross_domain=1&token={$filemanager_access_key_template}&field_id=image_item" data-type="iframe" class="btn btn-sm btn-success mb-10">
                                    <i class="fa fa-images"></i>
                                    {__d('admin', 'chon_anh_giao_dien')}
                                </div>

                                {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_item"}

                                <div btn-select-media-block="cdn" action="preview" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand  mb-10">
                                    <i class="fa fa-photo-video"></i>
                                    {__d('admin', 'chon_anh_tu_cdn')}
                                </div>
                            </div>  
                        </div>                    
                    </div>

                    <div class="col-lg-6 col-12">
                        {assign var = image_source value = ''}
                        {if !empty($item.image_source) && !empty($item.image)}
                            {assign var = image_source value = $item.image_source}
                        {/if}

                        {assign var = image_url value = ''}
                        {if !empty($item.image) && $image_source == 'cdn'}
                            {assign var = image_url value = "background-image: url('{CDN_URL}{$item.image}');"}
                        {/if}

                        {if !empty($item.image) && $image_source == 'template'}
                            {assign var = image_url value = "background-image: url('{$item.image}');background-size: contain;background-position: 50% 50%;"}
                        {/if}                 

                        <div block-preview-image="image_item" class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($item.image)}kt-avatar--changed{/if} mb-10">
                            <div class="kt-avatar__holder" style="{$image_url}"></div>
                            <input id="image_item" name="" data-name="image" value="{if !empty($item.image)}{htmlentities($item.image)}{/if}" class="input-select-image" type="hidden" />
                            <input block-image-source="image_item" name="" data-name="image_source" value="{$image_source}" class="input-image-source" type="hidden" />
                            
                            <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-10"></div>

        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <div class="kt-checkbox-inline">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mt-10">
                            <input id="has-sub-menu" name="" data-name="has_sub_menu" value="1" type="checkbox" {if !empty($item.has_sub_menu)}checked{/if}> 
                            {__d('admin', 'su_dung_menu_phu')}
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div id="wrap-config-sub-menu" class="{if empty($item.has_sub_menu)}d-none{/if}">
            <div class="row">
                
                <div class="col-lg-6 col-12">
                    {if !empty($files_item)}
                        <div class="form-group">
                            <label>
                                {__d('admin', 'giao_dien_menu_phu')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            {assign var = menu_layout value = "{if !empty($files_item[0])}{$files_item[0]}{/if}"}
                            {if !empty($item.view_item)}
                                {$menu_layout = $item.view_item}
                            {/if}
                            
                            {$this->Form->select('', $files_item, ['data-name' => 'view_item', 'empty' => "{__d('admin', 'chon')}", 'default' => $menu_layout, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    {/if}

                    <div class="form-group">
                        <label>
                            {__d('admin', 'du_lieu_menu_phu')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        {$this->Form->select('', $this->BlockAdmin->getAllTypeSubMenuDropdown(), ['id' => 'sub-menu-type', 'data-name' => 'type_sub_menu', 'empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($item.type_sub_menu)}{$item.type_sub_menu}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
                </div>
                
                <div id="wrap-data-sub-menu" class="col-lg-8 col-12">
                    {assign var = categories_selected value = []}
                    {if !empty($item.sub_categories_id)}
                        {assign var = categories_selected value = $item.sub_categories_id}
                    {/if}

                    {if !empty($item.type_sub_menu) && $item.type_sub_menu == {CATEGORY_PRODUCT}}
                        {$this->element("../TemplateBlock/load_checkbox_categories", [
                            'type' => {PRODUCT}, 
                            'categories_selected' => $categories_selected
                        ])}
                    {/if}

                    {if !empty($item.type_sub_menu) && $item.type_sub_menu == {CATEGORY_ARTICLE}}
                        {$this->element("../TemplateBlock/load_checkbox_categories", ['type' => {ARTICLE}, 'categories_selected' => $categories_selected])}
                    {/if}

                    {if !empty($item.type_sub_menu) && $item.type_sub_menu == {CUSTOM}}
                        {assign var = data_extend_sub_menu value = ''}
                        {if !empty($item.data_extend_sub_menu)}
                            {assign var = data_extend_sub_menu value = $item.data_extend_sub_menu}                            
                        {/if}
                        {$this->element("../TemplateBlock/load_editor_data_extend_sub_menu", ['data_extend_sub_menu' => $data_extend_sub_menu])}                        
                    {/if}
                </div>
            </div>
        </div>        
    </div>
</div>