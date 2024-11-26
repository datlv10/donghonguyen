{assign var = cities value = $this->LocationAdmin->getListCitiesForDropdown()}
<div class="row wrap-location">
	<div class="col-lg-4 col-xl-3">
		{$this->Form->select($code, $cities, ['id' => 'attribute_city_id', 'empty' => "-- {__d('admin', 'tinh_thanh')} --", 'default' => $value, 'input-attribute' => "{CITY}", 'input-attribute-code' => "{$code}", 'class' => "form-control form-control-sm", 'data-size' => 10])}	
	</div>
</div>
