<div class="form-group">
    <label>
        {__d('admin', 'anh_san_pham')}
    </label>
    <div class="row mb-10 wrap-album collapse show">
        <div class="col-lg-9 col-xl-8">
            <input id="album" name="album" upload-id="{if !empty($product.id)}{$product.id}{/if}" value="{if !empty($product.images)}{htmlentities($product.images|@json_encode)}{/if}" type="hidden" />
            <div class="list-image-album">
				{if !empty($product.images)}                       
				    {foreach from = $product.images item = image}
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

        {assign var = url_select_album value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=album"}

        <div class="col-lg-3 col-xl-2">
            <div class="row">
                <span data-src="{$url_select_album}" class="col-lg-12 btn-sm btn btn-success btn-select-image-album" data-type="iframe">
                    <i class="fa fa-images"></i> 
                    {__d('admin', 'chon_anh')}
                </span>
                <span class="col-lg-12 btn-sm btn btn-brand btn-quick-upload kt-margin-t-10">
                    <i class="fa fa-cloud-upload-alt"></i> 
                    {__d('admin', 'cap_nhat')}
                </span>
            </div>
        </div>
    </div>
</div>