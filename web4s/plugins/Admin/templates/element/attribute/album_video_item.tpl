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

        <div class="row wrap-video">
            <div class="col-xl-6 col-lg-6">
                <div class="form-group">
                    <label>
                        {__d('admin', 'duong_dan_video')}
                    </label>
                    <input id="{$code}_{$index}_video" name="" data-name="url" value="{if !empty($album.url)}{$album.url}{/if}" input-attribute="{ALBUM_VIDEO}" input-attribute-code="{$code}" type="text" class="form-control form-control-sm">
                    <span class="form-text text-muted">
                        {__d('admin', 'voi_kieu_video_youtube_url_chi_dien_ma_video')} 
                        <img src="{ADMIN_PATH}/assets/media/note/upload_video.png" width="300px" />
                    </span>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="form-group">
                    <label>
                        {__d('admin', 'loai_video')}
                    </label>
                    <div class="row">
                        <div class="col-xl-6 col-lg-12">
                            {$this->Form->select('type', $this->ListConstantAdmin->listTypeVideo(), ['id' => 'type_video', 'data-name' => 'type', 'empty' => null, 'default' => "{if !empty($album.type)}{$album.type}{/if}", 'class' => 'form-control form-control-sm'])}                     
                        </div>

                        {assign var = url_select_video value = "{CDN_URL}/myfilemanager/?type_file=video&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$code}_{$index}_url"}

                        <div class="col-xl-6 col-lg-12">
                            <span class="col-12 btn btn-sm btn-success d-none btn-select-video" data-src="{$url_select_video}" data-type="iframe">
                                <i class="fa fa fa-photo-video"></i> 
                                {__d('admin', 'chon_video')}
                            </span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        {assign var = preview value = ''}
        {if !empty($album.avatar)}
            {$preview = "background-image: url('{CDN_URL}{$album.avatar}');background-size: contain;background-position: 50% 50%;"}
        {/if}

        {assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$code}_{$index}_avatar_video"}

        <div class="form-group">
            <label>
                {__d('admin', 'anh_dai_dien')}
            </label>

            <div class="clearfix">
                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($preview)}kt-avatar--changed{/if}">
                    <a {if !empty($album.avatar)}href="{CDN_URL}{$album.avatar}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$preview}"></a>

                    <label class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-src="{$url_select_avatar}" data-type="iframe">
                        <i class="fa fa-pen"></i>
                    </label>

                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                        <i class="fa fa-times"></i>
                    </span>

                    <input id="{$code}_{$index}_avatar_video" name="" data-name="avatar" value="{if !empty($album.avatar)}{htmlentities($album.avatar)}{/if}" type="hidden" class="input-select-image" />
                </div>
            </div>        
        </div>
    </div>
</div>