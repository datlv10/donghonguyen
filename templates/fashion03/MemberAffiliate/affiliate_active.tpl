{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="bg-white p-4 h-100">
				{if empty($member.is_partner_affiliate)}
					<div class="font-weight-bold h4 mb-4">
						{__d('template', 'thong_tin_ca_nhan')}
					</div>

					<div class="row">
						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'ho_va_ten')}: 
				                </label>

				                {if !empty($member.full_name)}
				                	<input readonly placeholder="{$member.full_name}" type="text" class="form-control" autocomplete="off">
				                {else}
				                	{__d('template', 'chua_co_thong_tin')} 
			                		<a class="font-danger" href="/member/profile">
			                			({__d('template', 'cap_nhap')})
			                		</a>
				                {/if}
				            </div>
						</div>

						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    Email: 
				                </label>
			                	<div>
			                		{if !empty($member.email)}
			                			<input readonly placeholder="{$member.full_name}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/profile">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>
						</div>
					</div>

					<div class="row">
						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'so_dien_thoai')}: 
				                </label>
			                	<div>
			                		{if !empty($member.phone)}
			                			<input readonly placeholder="{$member.phone}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/profile">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>
						</div>

						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'dia_chi')}: 
				                </label>
			                	<div>
			                		{if !empty($member.address)}
			                			<input readonly placeholder="{$member.address}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/address">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>
						</div>
					</div>

					<div class="row">
						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'tinh_thanh')}: 
				                </label>
			                	<div>
			                		{if !empty($member.city_name)}
			                			<input readonly placeholder="{$member.city_name}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/address">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>	
						</div>

						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'quan_huyen')}: 
				                </label>
			                	<div>
			                		{if !empty($member.district_name)}
			                			<input readonly placeholder="{$member.district_name}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/address">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>
						</div>
					</div>

					<div class="row">
						<div class="col-6">
							<div class="form-group">
				                <label class="font-weight-normal">
				                    {__d('template', 'phuong_xa')}: 
				                </label>
			                	<div>
			                		{if !empty($member.ward_name)}
			                			<input readonly placeholder="{$member.ward_name}" type="text" class="form-control" autocomplete="off">
			                		{else}
			                			{__d('template', 'chua_co_thong_tin')} 
			                			<a class="font-danger" href="/member/address">
			                				({__d('template', 'cap_nhap')})
			                			</a>
			                		{/if}
								</div>
				            </div>
						</div>
					</div>

					<form nh-form="process-active" action="/member/affiliate/process-active" method="post" autocomplete="off">
						<div class="font-weight-bold h4 my-4">
							{__d('template', 'thong_tin_cmnd_cccd')}
						</div>

						<div class="row">
							<div class="col-6">
								<div class="form-group">
					                <label class="font-weight-normal">
					                    {__d('template', 'ho_va_ten_cmnd_cccd')}: 
					                </label>
					                <input name="identity_card_name" type="text" class="form-control" value="{if !empty($member.identity_card_name)}{$member.identity_card_name}{/if}">
					            </div>
							</div>

							<div class="col-6">
								<div class="form-group">
					                <label class="font-weight-normal">
					                    CMND/CCCD: 
					                </label>
				                	<input name="identity_card_id" value="{if !empty($member.identity_card_id)}{$member.identity_card_id}{/if}" type="text" class="form-control" autocomplete="off">
					            </div>
							</div>
						</div>
						<div class="row">
							<div class="col-6">
								<div class="form-group">
					                <label class="font-weight-normal">
					                    {__d('template', 'ngay_cap')}: 
					                </label>
					                <input nh-date name="identity_card_date" type="text" data-date-end-date="0d" class="form-control" value="{if !empty($member.identity_card_date)}{$member.identity_card_date}{/if}" placeholder="dd/mm/yyyy">
					            </div>
							</div>
							<div class="col-6">
								<div class="form-group">
					                <label class="font-weight-normal">
					                    {__d('template', 'noi_cap')}: 
					                </label>
					                <input name="identity_card_where" type="text" class="form-control" value="{if !empty($member.identity_card_where)}{$member.identity_card_where}{/if}">
					            </div>
							</div>
						</div>

						<div class="font-weight-bold h4 my-4">
							{__d('template', 'thong_tin_ngan_hang')}
						</div>
				    	<div class="row">
				    		<div class="col-6">
				    			<div class="form-group">
							        <label for="bank_key" class="font-weight-normal">
							            {__d('template', 'ten_ngan_hang')}: 
							        </label>
							        {$this->Form->select('bank_key', $this->Member->getListBank(), ['id' => 'bank_key', 'empty' => "-- {__d('template', 'ten_ngan_hang')} --", 'default' => "", 'class' => 'form-control selectpicker ', 'data-size' => 10, 'data-live-search' => true])}
						        </div>
				    		</div>

				    		<div class="col-6">
							    <div class="form-group">
							        <label for="bank_branch" class="font-weight-normal">
							            {__d('template', 'chi_nhanh')}: 
							        </label>
							        <input name="bank_branch" value="{if !empty($affiliate.bank_branch)}{$affiliate.bank_branch}{/if}" type="text" class="form-control" autocomplete="off">
							    </div>
				    		</div>
				    	</div>

				    	<div class="row">
				    		<div class="col-6">
				    			<div class="form-group">
							        <label for="account_holder" class="font-weight-normal">
							            {__d('template', 'chu_tai_khoan')}: 
							        </label>
							        <input name="account_holder" value="{if !empty($affiliate.account_holder)}{$affiliate.account_holder}{/if}" type="text" class="form-control" autocomplete="off">
							    </div>
				    		</div>

				    		<div class="col-6">
				    			<div class="form-group">
							        <label for="account_number" class="font-weight-normal">
							            {__d('template', 'so_tai_khoan')}: 
							        </label>
							        <input name="account_number" value="{if !empty($affiliate.account_number)}{$affiliate.account_number}{/if}" type="text" class="form-control" autocomplete="off">
							    </div>
				    		</div>
				    	</div>

					    <div class="row mt-5">
					    	<div class="col-6">
					    		<a href="/member/dashboard" class="btn btn-submit-1 w-100">
						            {__d('template', 'huy_dang_ky')}
						        </a>
					    	</div>
					    	
					    	<div class="col-6">
					    		<span nh-btn-action="submit" class="btn btn-submit w-100">
						            {__d('template', 'xac_nhan')}
						        </span>
					    	</div>
					    </div>
					</form>
				{elseif $member.is_partner_affiliate == 2}
					<div class="text-center">
						<b>{__d('template', 'dang_cho_quan_tri_xet_duyet')}</b>
					</div>
				{else}
					<div class="text-center">
						<b>{__d('template', 'ban_hien_dang_la_doi_tac_cua_chung_toi')}</b>
					</div>
				{/if}
			</div>
		</div>
	</div>	
</div>