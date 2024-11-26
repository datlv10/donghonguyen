<div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet wrap-item {if !empty($album)}kt-portlet--collapse{/if}">
    <div class="kt-portlet__head p-5">
        <div class="kt-portlet__head-label ml-5">
            <h3 class="kt-portlet__head-title">
                {assign var = first_lang value = $languages|@key}
                {assign var = key_first_name value = "name_{$first_lang}"}
                
                {if !empty($album.$key_first_name)}
                    {$album.$key_first_name}
                {else}
                    New Item
                {/if}
            </h3>
        </div>

        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">
                <span class="btn btn-sm btn-icon btn-danger btn-icon-md m-0 btn-delete-item">
                    <i class="la la-trash-o"></i>
                </span>

                <span class="btn btn-sm btn-icon btn-info btn-icon-md m-0 btn-toggle-item">
                    <i class="la la-angle-down"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="kt-portlet__body p-10 " style="{if !empty($album)}display: none;{/if}">
        <div class="row">
            <div class="col-lg-6 col-12">
                {if !empty($languages)}
                    {foreach from = $languages item = language key = k_lang name = title_item}
                        <div class="form-group">
                            <label>
                                {__d('admin', 'tieu_de')}
                                ({$language})
                                <span class="kt-font-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <i class="fa fa-align-left"></i>
                                        </div>
                                    </span>
                                </div>

                                {assign var = key_name value = "name_{$k_lang}"}
                                <input name="" data-name="name_{$k_lang}" value="{if !empty($album.$key_name)}{$album.$key_name}{/if}" class="form-control form-control-sm {if !empty($required)}required{/if} {if $smarty.foreach.title_item.first}item-name{/if}" type="text">

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                        </div>
                                    </span>
                                </div>

                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>

            <div class="col-lg-6 col-12">
                {if !empty($languages)}
                    {foreach from = $languages item = language key = k_lang}
                        <div class="form-group">
                            <label>
                                {__d('admin', 'mo_ta_ngan')}
                                ({$language})
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <i class="fa fa-file-alt w-20px"></i>
                                        </div>
                                    </span>
                                </div>

                                {assign var = key_description value = "description_{$k_lang}"}
                                <input name="" data-name="description_{$k_lang}" value="{if !empty($album.$key_description)}{$album.$key_description}{/if}" class="form-control form-control-sm" type="text">

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <div class="list-flags">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$k_lang}.svg" alt="{$k_lang}" class="flag h-15px w-15px" />
                                        </div>
                                    </span>
                                </div>

                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>

        <div class="row wrap-album">
            <div class="col-xl-6 col-lg-6">
                <input id="{$code}_{$index}_images" name="" data-name="images" value="{if !empty($album.images)}{htmlentities($album.images)}{/if}" type="hidden" input-attribute="{ALBUM_IMAGE}" input-attribute-code="{$code}" />
                <div class="clearfix mb-5 list-image-album">
                    {assign var = images value = []}
                    {if !empty($album.images)}
                        {$images = $album.images|json_decode:1}
                    {/if}

                    {if !empty($images)}
                        {foreach from = $images item = image}
                            {if !empty($image)}
                                <a href="{CDN_URL}{$image}" target="_blank" class="kt-media kt-media--lg mr-10 position-relative item-image-album" data-image="{$image}">
                                    <img src="{CDN_URL}{$image}">
                                    <span class="btn-clear-image-album" title="{__d('admin', 'xoa_anh')}">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </a>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
            </div>

            <div class="col-xl-2 col-lg-4">
                {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$code}_{$index}_images"}

                <span class="col-12 btn btn-sm btn-success btn-select-image-album" data-src="{$url_select_image}" data-type="iframe">
                    <i class="fa fa-images"></i> 
                    {__d('admin', 'chon_anh')}
                </span>
            </div>
        </div>
    </div>
</div>