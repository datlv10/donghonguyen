<div class="kt-portlet nh-portlet nh-active-hover position-relative">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'thong_tin_khac')}
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="form-group">
            <label>
                {__d('admin', 'tep_dinh_kem')}
            </label>
            <div class="row">                        
                <div class="col-xl-8 col-lg-8">
                    <div class="wrap-files">
                        <input id="files" name="files" value="{if !empty($product.files)}{htmlentities($product.files|@json_encode)}{/if}" type="hidden" />
                        <div class="list-files">
                            {if !empty($product.files)}
                                {assign var = files value = $product.files}
                                {foreach from = $files item = file}
                                    <a href="{CDN_URL}{$file}" class="kt-media kt-media--lg mr-20 item-file" data-file="{$file}" target="_blank">
                                        {assign var = file_type value = {$this->UtilitiesAdmin->getTypeFileByUrl($file)}}
                                        <i class="fa fa-file{if !empty($file_type)}-{$file_type}{/if}"></i>
                                        <span class="btn-clear-file" title="{__d('admin', 'xoa_tep')}">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </a>
                                {/foreach}
                            {/if}
                        </div>                            
                    </div>
                </div>

                {assign var = url_select_files value = "{CDN_URL}/myfilemanager/?cross_domain=1&multiple=1&token={$access_key_upload}&field_id=files&lang={LANGUAGE_ADMIN}"}

                <div class="col-xl-2 col-lg-4">
                    <span data-src="{$url_select_files}" class="col-lg-12 col-xl-12 btn btn-sm btn-success btn-select-file" data-type="iframe">
                        <i class="fa fa-file-alt"></i> 
                        {__d('admin', 'chon_tep')}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'duong_dan_video')}
            </label>

            <div class="row wrap-video">
                <div class="col-xl-8 col-lg-8">
                    <input name="url_video" id="url_video" value="{if !empty($product.url_video)}{$product.url_video}{/if}" type="text" class="form-control form-control-sm">
                    <span class="form-text text-muted">
                        {__d('admin', 'voi_kieu_video_youtube_url_chi_dien_ma_video')} 
                        <img src="{ADMIN_PATH}/assets/media/note/upload_video.png" width="300px" />
                    </span>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <div class="row">
                        <div class="col-xl-6 col-lg-12">
                            {$this->Form->select('type_video', $this->ListConstantAdmin->listTypeVideo(), ['id' => 'type_video', 'empty' => null, 'default' => "{if !empty($product.type_video)}{$product.type_video}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker mb-10'])}
                        </div>

                        {assign var = url_select_video value = "{CDN_URL}/myfilemanager/?type_file=video&cross_domain=1&token={$access_key_upload}&field_id=url_video&lang={LANGUAGE_ADMIN}"}

                        <div class="col-xl-6 col-lg-12">
                            <span data-src="{$url_select_video}" class="col-12 btn btn-sm btn-success d-none btn-select-video" data-type="iframe">
                                <i class="fa fa fa-photo-video"></i> 
                                {__d('admin', 'chon_video')}
                            </span>
                        </div>
                    </div>
                </div>                        
            </div>
        </div>
        
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="form-group">
                    <label>
                        {__d('admin', 'can_nang')}
                    </label>
                    <div class="row">
                        <div class="col-8 pr-0">
                            <input name="weight" value="{if !empty($product.weight)}{$product.weight}{/if}" class="form-control form-control-sm number-input" type="text">
                        </div>
                        <div class="col-4">
                            {$this->Form->select('weight_unit', $weight_unit, ['empty' => null, 'default' => "{if !empty($product.weight_unit)}{$product.weight_unit}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="form-group">
                    <label>
                        {__d('admin', 'chieu_dai')}
                    </label>
                    <div class="row">
                        <div class="col-8 pr-0">
                            <input name="length" value="{if !empty($product.length)}{$product.length}{/if}" class="form-control form-control-sm number-input" type="text">
                        </div>
                        <div class="col-4">
                            {$this->Form->select('length_unit', $length_unit, ['empty' => null, 'default' => "{if !empty($product.length_unit)}{$product.length_unit}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <div class="form-group">
                    <label>
                        {__d('admin', 'chieu_rong')}
                    </label>
                    <div class="row">
                        <div class="col-8 pr-0">
                            <input name="width" value="{if !empty($product.width)}{$product.width}{/if}" class="form-control form-control-sm number-input" type="text">
                        </div>
                        <div class="col-4">
                            {$this->Form->select('width_unit', $length_unit, ['empty' => null, 'default' => "{if !empty($product.width_unit)}{$product.width_unit}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <div class="form-group">
                    <label>
                        {__d('admin', 'chieu_cao')}
                    </label>
                    <div class="row">
                        <div class="col-8 pr-0">
                            <input name="height" value="{if !empty($product.height)}{$product.height}{/if}" class="form-control form-control-sm number-input" type="text">
                        </div>
                        <div class="col-4">
                            {$this->Form->select('height_unit', $length_unit, ['empty' => null, 'default' => "{if !empty($product.height_unit)}{$product.height_unit}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
            </div>                    
        </div>
    </div>
</div>