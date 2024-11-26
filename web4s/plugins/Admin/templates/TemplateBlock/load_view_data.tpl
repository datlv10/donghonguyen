{if $data_type == {CATEGORY_PRODUCT} || $data_type == {CATEGORY_ARTICLE}}
    <div class="form-group position-relative">
        <label>
            {__d('admin', 'chon_danh_muc')}
        </label>
        {assign var = type_category value = ''}
        {if $data_type == {CATEGORY_PRODUCT}}
            {assign var = type_category value = {PRODUCT}}
        {/if}

        {if $data_type == {CATEGORY_ARTICLE}}
            {assign var = type_category value = {ARTICLE}}
        {/if}

        {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForCheckboxList([
            {TYPE} => $type_category, 
            {LANG} => $lang
        ])}

        {if !empty($list_categories)}
            <div class="col-md-6 col-12">
                <div class="wrp-scrollbar">
                    {foreach from = $list_categories key = category_id item = category}
                        {assign var = level value = {$category.level|intval}}
                        <div class="clearfix mt-10">
                            {if !empty($level)}
                                {for $i = 1 to $level - 1}
                                    <label class="mr-20">&nbsp;</label>
                                {/for}
                            {/if}                                            
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                <input name="config[data_ids][]" value="{$category_id}" {if !empty($data) && $category_id|in_array:$data}checked="true"{/if} type="checkbox"> 
                                {if !empty($category.name)}
                                    {$category.name}
                                {/if}
                                <span></span>
                            </label>
                        </div> 
                    {/foreach}
                </div>
            </div>
        {else}
            <i class="mb-10">
                {__d('admin', 'chua_co_danh_muc_duoc_kich_hoat')}
            </i>
        {/if}
    </div>
{/if}

{if $data_type == {BRAND_PRODUCT}}
    <div class="form-group position-relative">
        <label>
            {__d('admin', 'chon_thuong_hieu')}
        </label>

        {assign var = list_brands value = $this->BrandAdmin->getListBrands([
            {LANG} => $lang
        ])}

        {if !empty($list_brands)}
            <div class="col-md-6 col-12">
                <div class="wrp-scrollbar">
                    {foreach from = $list_brands key = brand_id item = brand_name}
                        <div class="clearfix mt-10">
                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                <input name="config[data_ids][]" value="{$brand_id}" {if !empty($data) && $brand_id|in_array:$data}checked="true"{/if} type="checkbox"> 
                                {if !empty($brand_name)}
                                    {$brand_name}
                                {/if}
                                <span></span>
                            </label>
                        </div> 
                    {/foreach}
                </div>
            </div>
        {else}
            <i class="mb-10">
                {__d('admin', 'chua_co_thuong_hieu_duoc_kich_hoat')}
            </i>
        {/if}
    </div>
{/if}

{if $data_type == {PRODUCT}}
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'chon_san_pham')}
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="flaticon-search"></i>
                        </span>
                    </div>
                    <input id="product-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_san_pham')}" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
    

    <div class="form-group">
        <label>
            {__d('admin', 'san_pham_da_chon')}
        </label>
        <div id="wrap-data-selected" class="clearfix mh-35 tagify">
            {if !empty($data)}
                {foreach from = $data item = product_id}
                    {assign var = product_info value = $this->ProductAdmin->getDetailProduct($product_id, $lang)}
                    <span class="tagify__tag">
                        <x class="tagify__tag__removeBtn" role="button"></x>
                        <div>
                            <span class="tagify__tag-text">
                                {if !empty($product_info.name)}
                                    {$product_info.name}
                                {/if}
                            </span>
                        </div>
                        <input name="config[data_ids][]" value="{if !empty($product_info.id)}{$product_info.id}{/if}" type="hidden">
                    </span>
                {/foreach}
            {/if}
        </div>        
    </div>
{/if}

{if $data_type == {ARTICLE}}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label>
                    {__d('admin', 'chon_bai_viet')}
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="flaticon-search"></i>
                        </span>
                    </div>
                    <input id="article-suggest" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_bai_viet')}" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>
            {__d('admin', 'bai_viet_da_chon')}
        </label>
        <div id="wrap-data-selected" class="clearfix mh-35 tagify">
            
            {if !empty($data)}
                {foreach from = $data item = article_id}
                    {assign var = item_info value = $this->ArticleAdmin->getDetailArticle($article_id, $lang)}
                    <span class="tagify__tag">
                        <x class="tagify__tag__removeBtn" role="button"></x>
                        <div>
                            <span class="tagify__tag-text">
                                {if !empty($item_info.name)}
                                    {$item_info.name}
                                {/if}
                            </span>
                        </div>
                        <input name="config[data_ids][]" value="{if !empty($item_info.id)}{$item_info.id}{/if}" type="hidden">
                    </span>
                {/foreach}
            {/if}
        </div>        
    </div>
{/if}

{if $data_type == {CATEGORY_PRODUCT} || ($data_type == {BY_URL} && !empty($block_type) && $block_type == {PRODUCT})}
    <div class="row">
        <div class="col-lg-3 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'loc_du_lieu')}
                </label>
                {assign var = list_filter value = $this->TemplateAdmin->getMoreFilterDataProduct()}
                {$this->Form->select('config[filter_data]', $list_filter, ['empty' => {__d('admin', 'tat_ca')}, 'default' => "{if !empty($config.filter_data)}{$config.filter_data}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
            </div>
        </div>
    </div>
{/if}