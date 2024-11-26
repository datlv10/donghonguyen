<form nh-form="member-address" id="member-address" action="/member/address/save" method="post" autocomplete="off">
    {assign var = address value = []}
    {assign var = city_id value = null}
    {if !empty($address.city_id)}
        {assign var = city_id value = $address.city_id}
    {/if}

    {assign var = district_id value = null}
    {if !empty($address.district_id)}
        {assign var = district_id value = $address.district_id}
    {/if}

    {assign var = ward_id value = null}
    {if !empty($address.ward_id)}
        {assign var = ward_id value = $address.ward_id}
    {/if}

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="name">
                    {__d('template', 'ten_dia_chi')} 
                    <span class="required">*</span>
                </label>
                <input name="name" value="{if !empty($address.name)}{$address.name}{/if}" type="text" class="form-control" autocomplete="off">
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="phone">
                    {__d('template', 'so_dien_thoai')} 
                    <span class="required">*</span>
                </label>
                <input name="phone" value="{if !empty($address.phone)}{$address.phone}{/if}" type="text" class="form-control" autocomplete="off">
            </div>
        </div>
    </div>

   <div class="form-group">
        <label for="address">
            {__d('template', 'dia_chi')}
            <span class="required">*</span>
        </label>
        <input name="address" value="{if !empty($address.address)}{$address.address}{/if}" type="text" class="form-control" autocomplete="off">
    </div>


    <div class="form-group">
        <label for="city_id">
            {__d('template', 'tinh_thanh')}
            <span class="required">*</span>
        </label>
        {$this->Form->select('city_id', $this->Location->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('template', 'tinh_thanh')} --", 'default' => $city_id, 'class' => 'form-control form-control-sm selectpicker', 'data-size' => 10, 'data-live-search' => true])}
    </div>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="district_id">
                    {__d('template', 'quan_huyen')}
                    <span class="required">*</span>
                </label>
                {$this->Form->select('district_id', $this->Location->getListDistrictForDropdown($city_id), ['id' => 'district_id', 'empty' => "-- {__d('template', 'quan_huyen')} --", 'default' => $district_id, 'class' => 'form-control form-control-sm selectpicker', 'data-size' => 10, 'data-live-search' => true])}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="ward_id">
                    {__d('template', 'phuong_xa')}
                </label>
                {$this->Form->select('ward_id', $this->Location->getListWardForDropdown($district_id), ['id' => 'ward_id', 'empty' => "-- {__d('template', 'phuong_xa')} --", 'default' => $ward_id, 'class' => 'form-control form-control-sm selectpicker', 'data-size' => 10, 'data-live-search' => true])}
            </div>
        </div>
    </div>
    <div class="d-none">
        <input name="address_id" type="hidden" value="{if !empty($address.id)}{$address.id}{/if}">
    </div>

    <input name="callback" type="hidden" value="">

    <span nh-btn-action="submit" class="btn btn-submit">
        {__d('template', 'cap_nhat')}
    </span>
</form>