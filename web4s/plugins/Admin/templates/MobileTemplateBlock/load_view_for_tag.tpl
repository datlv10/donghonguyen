{if $type == {CATEGORY_PRODUCT} || $type == {CATEGORY_ARTICLE}}
    {assign var = data_type value = ''}
    {if $type == {CATEGORY_PRODUCT}}
        {assign var = data_type value = {PRODUCT}}
    {/if}
    {if $type == {CATEGORY_ARTICLE}}
        {assign var = data_type value = {ARTICLE}}
    {/if}
    {assign var = list_categories value = $this->CategoryAdmin->getListCategoriesForCheckboxList([
        {TYPE} => $data_type, 
        {LANG} => $lang
    ])}
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>
                    {__d('admin', 'chon_danh_muc')}
                </label>
                {if !empty($list_categories)}
                    <div class="wrp-scrollbar">
                        {foreach from = $list_categories key = category_id item = category}
                            {assign var = level value = {$category.level|intval}}
                            <div class="clearfix">
                                {if !empty($level)}
                                    {for $i = 1 to $level - 1}
                                        <label class="mr-20">&nbsp;</label>
                                    {/for}
                                {/if}                                            
                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                                    <input name="" data-name="data_ids" data-mutiple="true" value="{$category_id}" {if !empty($record_selected) && $category_id|in_array:$record_selected}checked{/if} type="checkbox"> 
                                    {if !empty($category.name)}
                                        {$category.name}
                                    {/if}
                                    <span></span>
                                </label>
                            </div> 
                        {/foreach}
                    </div>
                {else}
                    <i class="mb-10">{__d('admin', 'chua_co_danh_muc_duoc_kich_hoat')}</i>
                {/if}
            </div>
        </div>
    </div>
{/if}

{if $type == {PRODUCT}}
    <div class="row">
        <div class="col">
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
                    <input input-suggest="product" data-name="suggest" name="" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_san_pham')}" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
    

    <div class="form-group">
        <label>
            {__d('admin', 'san_pham_da_chon')}
        </label>
        <div id="wrap-data-selected" class="clearfix mh-35 tagify">
            {if !empty($record_selected)}
                {foreach from = $record_selected item = product_id}
                    {assign var = item_info value = $this->ProductAdmin->getDetailProduct($product_id, $lang)}
                    <span class="tagify__tag">
                        <x class="tagify__tag__removeBtn" role="button"></x>
                        <div>
                            <span class="tagify__tag-text">
                                {if !empty($item_info.name)}
                                    {$item_info.name}
                                {/if}
                            </span>
                        </div>
                        <input data-name="data_ids" data-mutiple value="{if !empty($item_info.id)}{$item_info.id}{/if}" type="hidden">
                    </span>
               {/foreach}
            {/if}
        </div>        
    </div>
{/if}

{if $type == {ARTICLE}}
    <div class="row">
        <div class="col">
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
                    <input input-suggest="article" data-name="suggest" name="" value="" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'nhap_ten_va_chon_bai_viet')}" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
    

    <div class="form-group">
        <label>
            {__d('admin', 'bai_viet_da_chon')}
        </label>
        <div id="wrap-data-selected" class="clearfix mh-35 tagify">
            {if !empty($record_selected)}
                {foreach from = $record_selected item = article_id}
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
                        <input data-name="data_ids" data-mutiple value="{if !empty($item_info.id)}{$item_info.id}{/if}" type="hidden">
                    </span>
               {/foreach}
            {/if}
        </div>        
    </div>
{/if}

{if !empty($type) && $type == {CATEGORY_PRODUCT}}
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>
                    {__d('admin', 'loc_du_lieu')}
                </label>
                {assign var = list_filter value = $this->TemplateAdmin->getMoreFilterDataProduct()}
                {$this->Form->select('config[filter_data]', $list_filter, ['empty' => {__d('admin', 'tat_ca')}, 'default' => "{if !empty($item.filter_data)}{$item.filter_data}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => 'filter_data'])}
            </div>
        </div>
    </div>
{/if}

{if !empty($type) && $type == {CATEGORY_ARTICLE}}
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>
                    {__d('admin', 'loc_du_lieu')}
                </label>
                {assign var = list_filter value = $this->TemplateAdmin->getMoreFilterDataArticle()}
                {$this->Form->select('config[filter_data]', $list_filter, ['empty' => {__d('admin', 'tat_ca')}, 'default' => "{if !empty($item.filter_data)}{$item.filter_data}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker', 'data-name' => 'filter_data'])}
            </div>
        </div>
    </div>
{/if}