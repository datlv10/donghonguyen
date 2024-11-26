<div class="product-detail-footer my-5">
	<ul class="nav" role="tablist">
	  	<li class="nav-item">
	    	<a class="nav-link active" data-toggle="pill" href="#content" role="tab" aria-controls="content" aria-selected="true">
	    		{__d('template', 'thong_tin_san_pham')}
	    	</a>
	  	</li>
	  	<li class="nav-item">
	    	<a nh-to-anchor="rating" class="nav-link" href="javascript:;">
	    		{__d('template', 'danh_gia')}
	    	</a>
	  	</li>
	  	<li class="nav-item">
	    	<a nh-to-anchor="comment" class="nav-link" href="javascript:;">
	    		{__d('template', 'binh_luan')}
	    	</a>
	  	</li>
	</ul>

	<div class="tab-content">
	  	<div class="tab-pane fade show active" id="content" role="tabpanel">
	  		<div class="p-5 bg-light">
		  		<div {if !empty($product.catalogue) && !empty($product.content)}nh-table-content="content"{/if}>
			  		{if !empty($product.content)}
			  			<div class="row">
			  				<div class="col-12">
			  					{$this->LazyLoad->renderContent($product.content)}
					  		</div>
				  		</div>
			  		{/if}
		  		</div>

		  		{if !empty($product.files)}
			  		<div class="entire-file">
						{foreach from = $product.files item = file}
							{assign var = file_name value = $this->Utilities->getFileNameInUrl($file)}
							<a href="{CDN_URL}{$this->Utilities->checkInternalUrl($file)}" download="{CDN_URL}{$this->Utilities->checkInternalUrl($file)}" class="btn btn-submit text-lowercase" target="_blank">
							    <i class="las la-cloud-download-alt"></i> {__d('template', 'tai_xuong')} {urldecode($file_name)}
							</a>
						{/foreach}
					</div>
				{/if}

		  		<div class="row mt-10">
			    	<div class="col-12">
			    		{if !empty($product.tags)}
				    		<div class="d-flex align-items-center flex-wrap mt-3">
				    			<span class="tags-title">
				                    <label>
				                        <b>{__d('template', 'the_bai_viet')}: </b>
				                    </label>
				                </span>
				        
				                <ul class="tags list-unstyled mb-0">
				                	{assign var = url_tag_page value = $this->Block->getLocale('duong_dan_trang_the_bai_viet', $data_extend)}
							        {foreach from = $product.tags item = tag}			        	
							        	{if !empty($tag.name)}
										    <li>
										        <a href="{if !empty($tag.url)}{TAG_PATH}{$this->Utilities->checkInternalUrl($tag.url)}{/if}">
										        	{$tag.name}
										        </a>
										    </li>
										{/if}
							        {/foreach}
								</ul>
				    		</div>
			    		{/if}
			    	</div>
			    </div>
		    </div>
	  	</div>
	</div>
</div>