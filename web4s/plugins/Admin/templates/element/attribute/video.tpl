{if !empty($code)}

	{assign var = video value = []}
	{if !empty($value)}
		{$video = $value|json_decode:1}
	{/if}

    <div class="row wrap-video">
        <div class="col-xl-8 col-lg-8">
            <input name="{$code}[url]" id="{$code}" value="{if !empty($video.url)}{$video.url}{/if}" input-attribute="video" type="text" class="form-control form-control-sm">
            <span class="form-text text-muted">
                {__d('admin', 'voi_kieu_video_youtube_url_chi_dien_ma_video')} 
                <img src="{ADMIN_PATH}/assets/media/note/upload_video.png" width="300px" />
            </span>
        </div>

        <div class="col-xl-4 col-lg-4">
            <div class="row">
                <div class="col-xl-6 col-lg-12">
                    {$this->Form->select("{$code}[type]", $this->ListConstantAdmin->listTypeVideo(), ['id' => 'type_video', 'empty' => null, 'default' => "{if !empty($video.type)}{$video.type}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker mb-10'])}
                </div>

                {assign var = url_select_video value = "{CDN_URL}/myfilemanager/?type_file=video&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id={$code}"}

                <div class="col-xl-6 col-lg-12">
                    <span class="col-12 btn btn-sm btn-success d-none btn-select-video" data-src="{$url_select_video}" data-type="iframe">
                        <i class="fa fa fa-photo-video"></i> 
                        {__d('admin', 'chon_video')}
                    </span>
                </div>
            </div>
        </div>
    </div>
{/if}