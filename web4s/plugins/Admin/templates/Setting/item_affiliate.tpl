<div class="wrap-item col-lg-4 col-md-4 col-12">
    <div class="border rounded pt-10 pb-10 pl-10 pr-10 mb-15">
        <span class="btn btn-sm btn-danger btn-delete-item">
            <i class="fas fa-times mr-0"></i>
        </span>
    	<div class="form-group">
    		<label class="kt-font-bold">
                {__d('admin', 'ten_cap')}
            </label>
            <input class="form-control form-control-sm" type="text" value="{if !empty($item.name)}{$item.name}{/if}" name="" data-name="name">
            <input id="key_level" class="form-control form-control-sm" type="hidden" value="{if !empty($item.key)}{$item.key}{/if}" name="" data-name="key">
    	</div>

        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="form-group">
                    <label>
                        {__d('admin', 'anh_dai_dien')}
                    </label>

                    {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_item"}

                    <div class="clearfix">
                        <span data-src="{$url_select_image}" btn-select-media-block="cdn" action="preview" data-type="iframe" class="btn btn-sm btn-brand mb-10">
                            <i class="fa fa-images"></i>
                            {__d('admin', 'chon_anh_tu_cdn')}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">

                {assign var = image_background value = ''}
                {if !empty($image_url) && $image_source == 'cdn'}
                    {$image_background = "background-image: url('{CDN_URL}{$image_url}');"}
                {/if}

                {if !empty($image_url) && $image_source == 'template'}
                    {$image_background = "background-image: url('{$image_url}');background-size: contain;background-position: 50% 50%;"}
                {/if}         

                <div block-preview-image="image_item" class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($image_url)}kt-avatar--changed{/if} mb-10">
                    <div class="kt-avatar__holder" style="{$image_background}"></div>
                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                        <i class="fa fa-times"></i>
                    </span>

                    <input id="image_item" name="" data-name="image" value="{if !empty($image_url)}{htmlentities($image_url)}{/if}" class="input-select-image" type="hidden" />
                    <input block-image-source="image_item" data-name="source" name="" value="{$image_source}" class="input-image-source" type="hidden" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-12">
            	<div class="form-group">
                    <label>
                        {__d('admin', 'so_luot_gioi_thieu')}
                    </label>
                    <input class="form-control form-control-sm number-input" type="text" value="{if !empty($item.number_referral)}{$item.number_referral}{/if}" name="" data-name="number_referral">
                </div>
            </div>

            <div class="col-sm-6 col-12">
                <div class="form-group">
                    <label>
                        {__d('admin', 'gia_tri_don_hang')}
                    </label>
                    <input class="form-control form-control-sm number-input" type="text" value="{if !empty($item.total_order)}{$item.total_order}{/if}" name="" data-name="total_order">
                </div>
            </div>
        </div>


        <div class="form-group">
            <label>
                {__d('admin', 'phan_tram_hoa_hong')}
            </label>
            <input class="form-control form-control-sm" type="text" value="{if !empty($item.profit)}{$item.profit}{/if}" name="" data-name="profit">
        </div>

        <div class="form-group">
            <label class="kt-font-bold">
                {__d('admin', 'chiet_khau_cung_ma_khuyen_mai')}
            </label>

            <div class="kt-radio-inline mt-5">
                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                    <input type="radio" name="" data-name="status_discount_sale" value="1" {if !empty($item.status_discount_sale)}checked{/if}>
                    {__d('admin', 'hoat_dong')}
                    <span></span>
                </label>

                <label class="kt-radio kt-radio--tick kt-radio--danger">
                    <input type="radio" name="" data-name="status_discount_sale" value="0" {if empty($item.status_discount_sale)}checked{/if}>
                    {__d('admin', 'khong_hoat_dong')}
                    <span></span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'phan_tram_hoa_hong')}
            </label>
            <input class="form-control form-control-sm" type="text" value="{if !empty($item.profit_sale)}{$item.profit_sale}{/if}" name="" data-name="profit_sale">
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'mo_ta_ngan')}
            </label>                            
            <div class="clearfix">
                <textarea id="description_{$key}" name="" data-name="description" class="mce-editor-simple">{if !empty($item.description)}{$item.description}{/if}</textarea>
            </div>
        </div>
    </div>
</div>