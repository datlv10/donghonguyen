<div class="bg-white p-4 mb-3">
	{assign var = contact value = []}
	{if !empty($order_info.contact)}
		{assign var = contact value = $order_info.contact}
	{/if}

	{$this->element('../Order/element_product_info')}	
</div>
<div class="bg-white p-4 mb-3">
	<div class="billing-details mt-4">
	    <div class="d-flex justify-content-between align-items-center mb-4">
	    	<div class="h4 font-weight-bold mb-0">
				{__d('template', 'dia_chi_nhan_hang')}
			</div>

			{if !empty($member_info)}
				<a nh-address="list" href="javascript:;" class="color-main">
					{__d('template', 'thay_doi')}
				</a>
			{else}
				<p class="mb-0">
					{__d('template', 'ban_da_co_tai_khoan')}  
					<a class="color-highlight" nh-order-login href="javascript:;">
						{__d('template', 'dang_nhap')}
					</a>
				</p>
			{/if}
	    </div>
	    
		{if !empty($contact && !empty($member_info))}
			{$this->element('../Order/contact_info',[
				'contact' => $contact
			])}
		{else}
			<div class="inner-col-1">
				<div class="form-billing">
					<div class="form-group validate-form">									
						<input class="form-control" name="full_name" value="{if !empty($contact.full_name)}{$contact.full_name}{/if}" type="text"  placeholder="{__d('template', 'ho_va_ten')}">
					</div>

					<div class="row">
					    <div class="col-lg-8 col-12">
				        	<div class="form-group validate-form">
								<input class="form-control" name="email" value="{if !empty($contact.email)}{$contact.email}{/if}" type="text"  placeholder="{__d('template', 'email')}">
							</div>
				        </div>

				        <div class="col-lg-4 col-12">
				        	<div class="form-group validate-form">
								<input class="form-control" name="phone" value="{if !empty($contact.phone)}{$contact.phone}{/if}" type="text"  placeholder="{__d('template', 'so_dien_thoai')}">
							</div>
				        </div>
				    </div>

	                <div class="form-group validate-form">
						<input class="form-control" name="address" value="{if !empty($contact.address)}{$contact.address}{/if}" placeholder="{__d('template', 'so_nha_ngo_duong')}" type="text"  >
					</div>

				    {assign var = city_id value = null}
	                {if !empty($contact.city_id)}
	                    {assign var = city_id value = $contact.city_id}
	                {/if}

	                {assign var = district_id value = null}
	                {if !empty($contact.district_id)}
	                    {assign var = district_id value = $contact.district_id}
	                {/if}

	                {assign var = ward_id value = null}
	                {if !empty($contact.ward_id)}
	                    {assign var = ward_id value = $contact.ward_id}
	                {/if}

					<div class="row">
				        <div class="col-lg-6 col-12">
				            <div class="form-group validate-form">
				                {$this->Form->select('city_id', $this->Location->getListCitiesForDropdown(), ['id' => 'city_id', 'empty' => "-- {__d('template', 'tinh_thanh')} --", 'default' => $city_id, 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
				            </div>
				        </div>

				        <div class="col-lg-6 col-12">
				            <div class="form-group validate-form">
				                {$this->Form->select('district_id', $this->Location->getListDistrictForDropdown($city_id), ['id' => 'district_id', 'empty' => "-- {__d('template', 'quan_huyen')} --", 'default' => $district_id, 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
				            </div>
				        </div>
				    </div>	

				    <div class="row">
			            <div class="col-md-6 col-12">
			                <div class="form-group validate-form">
			                    {$this->Form->select('ward_id', $this->Location->getListWardForDropdown($district_id), ['id' => 'ward_id', 'empty' => "-- {__d('template', 'phuong_xa')} --", 'default' => $ward_id, 'class' => 'form-control selectpicker', 'data-size' => 10, 'data-live-search' => true])}
			                </div>
			            </div>
			        </div>							   
				</div>
			</div>
		{/if}

		<div class="inner-col-2 mb-5">
			<label>{__d('template', 'ghi_chu')}</label>
			<textarea class="form-control border-gray" name="note" rows="2" cols="5">{if !empty($contact.note)}{$contact.note}{/if}</textarea>
		</div>
	</div>
</div>
