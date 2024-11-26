{assign var = city_id value = ''}
{assign var = district_id value = ''}
{assign var = ward_id value = ''}
{assign var = value value = $value|json_decode:1}
{if !empty($value.city_id)}
	{$city_id = $value.city_id}
{/if}

{if !empty($value.district_id)}
	{$district_id = $value.district_id}
{/if}

{if !empty($value.ward_id)}
	{$ward_id = $value.ward_id}
{/if}

{assign var = cities value = $this->LocationAdmin->getListCitiesForDropdown()}
{assign var = districts value = []}
{assign var = wards value = []}
{if !empty($city_id)}
	{$districts = $this->LocationAdmin->getListDistrictForDropdown($city_id)}
{/if}

{if !empty($district_id)}
	{$wards = $this->LocationAdmin->getListWardForDropdown($district_id)}
{/if}

<div class="row wrap-location">
	<div class="col-lg-4 col-xl-3">
		{$this->Form->select("{$code}[city_id]", $cities, ['id' => 'attribute_city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => $city_id, 'input-attribute' => "{CITY_DISTRICT}", 'class' => "form-control form-control-sm", 'data-size' => 10])}
	</div>

	<div class="col-lg-4 col-xl-3">
		{$this->Form->select("{$code}[district_id]", $districts, ['id' => 'attribute_district_id', 'empty' => "-- {__d('admin', 'quan_huyen')} --", 'default' => $district_id, 'class' => "form-control form-control-sm", 'data-size' => 10])}
	</div>

	<div class="col-lg-4 col-xl-3">
		{$this->Form->select("{$code}[ward_id]", $wards, ['id' => 'attribute_ward_id', 'empty' => "-- {__d('admin', 'phuong_xa')} --", 'default' => $ward_id, 'class' => "form-control form-control-sm", 'data-size' => 10])}
	</div>
</div>
