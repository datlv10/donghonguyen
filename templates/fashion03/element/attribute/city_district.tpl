<div class="d-flex justify-content-between align-items-center border-bottom border-gray pb-4 mb-4">
    {assign var = "delete_{$code}" value = "style='display: block'"}
    {if strpos($current_url, "attr_{$code}") === false}
        {assign var = "delete_{$code}" value = "style='display: none'"}
    {/if}
    <div class="text-uppercase h5 font-weight-bold mb-0">
        {if !empty($attribute.name)}
            {$attribute.name}
        {/if}
    </div>
    <a {$delete_{$code}} href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($current_url, [], ["attr_{$code}"])}" class="reset-attribute border-0 color-highlight">
        {__d('template', 'xoa')}
    </a>
</div>

{assign var = param_address value = $this->Utilities->getParamsByKey("attr_{$code}")}
{assign var = city_id value = ""}
{assign var = district_id value = ""}
{if !empty($param_address)} 
    {assign var = list_address value="-"|explode:$param_address}
    {if !empty($list_address)}
        {foreach from = $list_address item = address}
            {assign var = part_address value="_"|explode:$address}
            {if strpos($address, '_city') > 0}
                {if !empty($part_address[0])}
                    {assign var = city_id value = $part_address[0]}
                {/if}
            {/if}
            {if strpos($address, '_district') > 0}
                {if !empty($part_address[0])}
                    {assign var = district_id value = $part_address[0]}
                {/if}
            {/if}
        {/foreach}
    {/if}
{/if}
<div nh-location-wrap class="mb-5">
    <div class="form-group validate-form">
        {$this->Form->select('city_id', $this->Location->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('template', 'tinh_thanh')} --", 'default' => $city_id, 'class' => 'form-control selectpicker input-hover', 'data-size' => 10, 'data-live-search' => true])}
    </div>
    <div class="form-group validate-form">
        {$this->Form->select('district_id', $this->Location->getListDistrictForDropdown($city_id), ['id' => 'district_id', 'empty' => "-- {__d('template', 'quan_huyen')} --", 'default' => $district_id, 'class' => 'form-control form-control-sm selectpicker input-hover', 'data-size' => 10, 'data-live-search' => true])}
    </div>
    <a class="btn btn-sm px-4 btn-submit" href="javascript:;" nh-location-filter="attr_{$code}">{__d('template', 'loc')}</a>
</div> 