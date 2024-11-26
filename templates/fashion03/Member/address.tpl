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
			<div class="h-100 bg-white p-4">
			    <div class="address-member">
				    {if !empty($member.addresses)}
						{foreach from = $member.addresses item = item}
					        <div class="item-address-member border-bottom pb-3 mb-3">
					            {if !empty($item.address_name)}
	    				            <div class="name-address-member font-weight-bold">
	    			        		    {$item.address_name}
	    			        		</div>
	    			        	{/if}

	    			        	{if !empty($item.phone)}
	    			        	    <div class="phone-address-member mb-1">
	    			        		    {$item.phone}
	    			        		 </div>
	    			        	{/if}

	    					    {if !empty($item.full_address)}
	    					        <div class="full-address mb-1">
	    					            {$item.full_address}
	    					        </div>
	    			        	{/if}

	    						<div class="d-flex justify-content-between">
	    						    <div class="color-hover d-flex align-items-center">
	    						    	{if !empty($item.is_default) && $item.is_default eq 1}
	    						    		<i class="fa-light fa-location-dot"></i>
	        						        <span class="ml-2">{__d('template', 'dia_chi_mac_dinh')}</span> 
	        						    {else}
											<a class="btn btn-sm btn-submit-1" href="javascript:;" nh-address="default" data-id="{$item.id}"> 
												{__d('template', 'dat_lam_dia_chi_mac_dinh')}
											</a>
	                                    {/if}
	    						    </div>
	    							<div class="d-flex justify-content-end">
	    							    <a nh-address="delete" href="javascript:;" class="color-highlight" data-id="{if !empty($item.id)}{$item.id}{/if}">
	    							    	{__d('template', 'xoa')}
	    	                            </a>
	    								<a nh-address="edit" data-address="{htmlentities($item|@json_encode)}" href="javascript:;" class="text-dark ml-3">
	    									{__d('template', 'sua')}
	    		                        </a>
	    		                        
	                                </div>
	    						</div>
					        </div>
					    {/foreach}
					{else}
						<tr>
							<td colspan="5">{__d('template', 'hien_chua_co_dia_chi_nao')}</td>
						</tr>
				    {/if}
				</div>
	            <div class="btn-add-member mt-4">
	                <a nh-address="add" href="javascript:;" class="btn btn-submit">
	                	{__d('template', 'them_dia_chi')}
	                </a>
	            </div>
			</div>
		</div>
	</div>	
</div>
{$this->element('../Member/change_address_modal')}

