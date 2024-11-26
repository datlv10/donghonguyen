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
			<div nh-affiliate class="h-100 bg-white p-4">
			    {if !empty($affiliate)}
			    	<div class="row">
						{foreach from = $affiliate item = item}
							<div class="col-12 col-md-6">
						        <div class="item-address-member border-bottom pb-3 mb-3">
						            {if !empty($item.bank_name)}
		    				            <div class="font-weight-bold mb-2">
		    			        		    {__d('template', 'ten_ngan_hang')}: {$item.bank_name}
		    			        		</div>
		    			        	{/if}

		    			        	{if !empty($item.account_holder)}
		    					        <div class="mb-1">
		    					            {__d('template', 'chu_tai_khoan')}: {$item.account_holder}
		    					        </div>
		    			        	{/if}

		    			        	{if !empty($item.bank_branch)}
		    			        	    <div class="mb-1">
		    			        		    {__d('template', 'chi_nhanh')}: {$item.bank_branch}
		    			        		 </div>
		    			        	{/if}

		    			        	{if !empty($item.account_number)}
		    					        <div class="mb-1">
		    					            {__d('template', 'so_tai_khoan')}: {$item.account_number}
		    					        </div>
		    			        	{/if}

		    						<div class="d-flex justify-content-between mt-3">
		    							<div class="d-flex justify-content-end">
		    							    <a nh-affiliate="delete-bank" href="javascript:;" class="color-highlight" data-id="{if !empty($item.id)}{$item.id}{/if}">
		    							    	{__d('template', 'xoa')}
		    	                            </a>
		    								<a nh-affiliate="edit" data-affiliate="{htmlentities($item|@json_encode)}" class="text-dark ml-3" href="javascript:;">
		    									{__d('template', 'sua')}
		    		                        </a>
		    		                        
		                                </div>
		    						</div>
						        </div>
					    	</div>
					    {/foreach}
				    </div>
				{else}
					<div>
						{__d('template', 'hien_chua_co_ngan_hang_nao_duoc_lien_ket')}
					</div>
			    {/if}
                <div class="mt-4">
                    <a nh-affiliate="add" href="javascript:;" class="btn btn-submit">
                    	{__d('template', 'them_ngan_hang')}
                    </a>
                </div>
			</div>
			
		</div>
	</div>	
</div>
{$this->element('../Member/change_associate_bank_modal')}