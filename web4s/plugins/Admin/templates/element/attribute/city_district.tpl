{assign var = city_id value = ''}
{assign var = district_id value = ''}
{assign var = value value = $value|json_decode:1}
{if !empty($value.city_id)}
	{$city_id = $value.city_id}
{/if}

{if !empty($value.district_id)}
	{$district_id = $value.district_id}
{/if}

{assign var = cities value = $this->LocationAdmin->getListCitiesForDropdown()}
{assign var = districts value = []}
{if !empty($city_id)}
	{$districts = $this->LocationAdmin->getListDistrictForDropdown($city_id)}
{/if}
<div class="row wrap-location">
	<div class="col-lg-4 col-xl-3">
		{$this->Form->select("{$code}[city_id]", $cities, ['id' => 'attribute_city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => $city_id, 'input-attribute' => "{CITY_DISTRICT}", 'class' => "form-control form-control-sm", 'data-size' => 10])}
	</div>

	<div class="col-lg-4 col-xl-3">
		{$this->Form->select("{$code}[district_id]", $districts, ['id' => 'attribute_district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => $district_id, 'class' => "form-control form-control-sm", 'data-size' => 10])}
	</div>
</div>
