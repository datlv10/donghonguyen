{if !empty($code)}
	{assign var = images value = []}
	{if !empty($value)}
		{$images = $value|json_decode:1}
	{/if}

	<div class="row wrap-album">
	    <div class="col-xl-8 col-lg-8">
	        <input id="{$code}" name="{$code}" value="{if !empty($value)}{htmlentities($value)}{/if}" type="hidden" input-attribute="images" />
	        <div class="clearfix mb-5 list-image-album">
	            {if !empty($images)}
	                {foreach from = $images item = image}
	                    <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
	                        <img src="{CDN_URL}{$image}">
	                        <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
	                            <i class="fa fa-times"></i>
	                        </span>
	                    </a>
	                {/foreach}
	            {/if}
	        </div>
	    </div>
	    <div class="col-xl-2 col-lg-4">
	    	{assign var = url_select_images value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$code}"}

	        <span class="col-12 btn btn-sm btn-success btn-select-image-album" data-src="{$url_select_images}" data-type="iframe">
	            <i class="fa fa-images"></i> 
	            {__d('admin', 'chon_anh')}
	        </span>
	    </div>
	</div>
{/if}