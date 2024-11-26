<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'sap_xep_theo')}
            </label>
            <div class="row">
                <div class="col-6">
                    {$this->Form->select("config[{SORT_FIELD}]", $this->TemplateAdmin->getListSortFieldOfBrand(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config[{SORT_FIELD}])}{$config[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
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

{assign var = brands_selected value = []}
{if !empty($config.data_ids)}
    {assign var = brands_selected value = $config.data_ids}
{/if}

<div class="form-group">
    <label>
        {__d('admin', 'thuong_hieu')}
    </label>
    {assign var = list_brands value = $this->BrandAdmin->getListBrands([
        {LANG} => $lang
    ])}
    
    {if !empty($list_brands)}
        {foreach from = $list_brands key = brand_id item = name}
            <div class="clearfix mt-10">                                        
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-5">
                    <input name="config[data_ids][]" value="{$brand_id}" {if !empty($brands_selected) && $brand_id|in_array:$brands_selected}checked{/if} type="checkbox"> 
                    {if !empty($name)}
                        {$name}
                    {/if}
                    <span></span>
                </label>
            </div> 
        {/foreach}
    {else}
        <i class="mb-10">{__d('admin', 'chua_co_thuong_hieu_duoc_kich_hoat')}</i>
    {/if}
</div>