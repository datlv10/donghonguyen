{assign var = url_list value = "{ADMIN_PATH}/brand"}
{assign var = url_add value = "{ADMIN_PATH}/brand/add"}
{assign var = url_edit value = "{ADMIN_PATH}/brand/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit,
    'show_lang' => true
])}

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/brand/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="d-none">
            <input type="hidden" name="seo_score" value="" id="seo-score">
            <input type="hidden" name="keyword_score" value="" id="keyword-score">
        </div>

        <div class="row">
            <div class="col-lg-12 col-xs-12">
                {if !empty($brand.id)}
                    <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    {__d('admin', 'thong_tin_cap_nhat')}
                                </h3>
                            </div>

                            {if !empty($brand.url)}
                                <div class="kt-portlet__head-toolbar">
                                    <a target="_blank" href="/{$brand.url}" class="kt-link kt-font-bolder kt-link--info">
                                        {__d('admin', 'xem_bai_viet')}
                                    </a>
                                </div>
                            {/if}
                        </div>

                        <div class="kt-portlet__body pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-xs-6">
                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'trang_thai')}
                                        </label>

                                        <div class="col-lg-8 col-xl-8">
                                            {if isset($brand.status) && $brand.status == 1}
                                                <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'hoat_dong')}
                                                </span>
                                            {elseif isset($brand.status) && $brand.status == 0}
                                                <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'khong_hoat_dong')}
                                                </span>   
                                            {elseif isset($brand.status) && $brand.status == -1}
                                                <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-10">
                                                    {__d('admin', 'cho_duyet')}
                                                </span>   
                                            {/if}
                                        </div>
                                    </div>

                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'nguoi_tao')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($brand.user_full_name)}
                                                    {$brand.user_full_name}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'thoi_gian_tao')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($brand.created)}
                                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.created)}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'cap_nhat_moi')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                {if !empty($brand.updated)}
                                                    {$this->UtilitiesAdmin->convertIntgerToDateTimeString($brand.updated)}
                                                {/if}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xs-6">  
                                    <div class="form-group form-group-xs row">
                                        <label class="col-lg-4 col-xl-4 col-form-label">
                                            {__d('admin', 'ngon_ngu_hien_tai')}
                                        </label>
                                        <div class="col-lg-8 col-xl-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                <div class="list-flags">
                                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="flag mr-10" />
                                                    {if !empty($list_languages[$lang])}
                                                        {$list_languages[$lang]}
                                                    {/if}
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {assign var = all_name_content value = $this->BrandAdmin->getAllNameContent($id)}
                                    {if !empty($use_multiple_language) && !empty($list_languages) }
                                        <div class="form-group form-group-xs row">
                                            <label class="col-lg-4 col-xl-4 col-form-label">
                                                {__d('admin', 'sua_ban_dich')}
                                            </label>
                                            <div class="col-lg-12 col-xs-12">
                                                <table class="table table-bordered mb-10">
                                                    <tbody>
                                                        {foreach from = $list_languages key = k_language item = language}
                                                            <tr>
                                                                <td class="w-90">
                                                                    <div class="list-flags d-inline mr-5">
                                                                        <img src="{ADMIN_PATH}{FLAGS_URL}{$k_language}.svg" alt="{$k_language}" class="flag" />
                                                                    </div>
                                                                    {$language}: 
                                                                    <i>
                                                                        {if !empty($all_name_content[$k_language])}
                                                                            {$all_name_content[$k_language]|truncate:100:" ..."}
                                                                        {else}
                                                                            <span class="kt-font-danger fs-12">
                                                                                {__d('admin', 'chua_nhap')}
                                                                            </span>
                                                                        {/if}
                                                                    </i>

                                                                    <a href="{ADMIN_PATH}/brand/update/{$brand.id}?lang={$k_language}" class="pl-10">
                                                                        <i class="fa fa-pencil-alt"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                    </tbody>
                                                </table>
                                            </div>                                            
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_chinh')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_thuong_hieu')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($brand.name)}{$brand.name|escape}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'duong_dan')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="la la-link"></i>
                                    </span>
                                </div>
                                <input name="link" value="{if !empty($brand.url)}{$brand.url}{/if}" data-link-id="{if !empty($brand.url_id)}{$brand.url_id}{/if}" type="text" class="form-control form-control-sm nh-link" maxlength="255">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-2 col-lg-3">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'vi_tri')}
                                    </label>
                                    <input name="position" value="{if !empty($position)}{$position}{/if}" class="form-control form-control-sm" type="text">
                                </div>
                            </div>
                        </div>                        
                        
                        <div class="form-group">
                            <label>
                                {__d('admin', 'noi_dung')}
                            </label>
                            <div class="clearfix">
                                <textarea name="content" id="content" class="mce-editor">{if !empty($brand.content)}{$brand.content}{/if}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'media')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'anh_chinh')}
                            </label>
                            <div class="clearfix">
                                {assign var = bg_avatar value = ''}
                                {if !empty($brand.image_avatar)}
                                    {assign var = bg_avatar value = "background-image: url('{CDN_URL}{$brand.image_avatar}');background-size: contain;background-position: 50% 50%;"}
                                {/if}

                                {assign var = url_select_avatar value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=image_avatar&lang={LANGUAGE_ADMIN}"}

                                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_avatar)}kt-avatar--changed{/if}">
                                    <a {if !empty($brand.image_avatar)}href="{CDN_URL}{$brand.image_avatar}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_avatar}"></a>
                                    <label class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-src="{$url_select_avatar}" data-type="iframe">
                                        <i class="fa fa-pen"></i>
                                    </label>
                                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                        <i class="fa fa-times"></i>
                                    </span>

                                    <input id="image_avatar" name="image_avatar" value="{if !empty($brand.image_avatar)}{htmlentities($brand.image_avatar)}{/if}" type="hidden" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'album_anh')}
                            </label>
                            <div class="row wrap-album">
                                <div class="col-xl-8 col-lg-8">
                                    <input id="images" name="images" value="{if !empty($brand.images)}{htmlentities($brand.images|@json_encode)}{/if}" type="hidden" />
                                    <div class="clearfix mb-5 list-image-album">
                                        {if !empty($brand.images)}
                                            {foreach from = $brand.images item = image}
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
                                    {assign var = url_select_album value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&multiple=1&token={$access_key_upload}&field_id=images&lang={LANGUAGE_ADMIN}"}

                                    <span class="col-12 btn btn-sm btn-success btn-select-image-album" data-src="{$url_select_album}" data-type="iframe">
                                        <i class="fa fa-images"></i> 
                                        {__d('admin', 'chon_anh_album')}
                                    </span>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label>
                                {__d('admin', 'tep_dinh_kem')}
                            </label>
                            <div class="row">
                                <div class="col-xl-8 col-lg-8">
                                    <div class="wrap-files">
                                        <input id="files" name="files" value="{if !empty($brand.files)}{htmlentities($brand.files|@json_encode)}{/if}" type="hidden" />
                                        <div class="list-files">                                
                                            {if !empty($brand.files)}
                                                {foreach from = $brand.files item = file}
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
                                    <span class="col-12 btn btn-sm btn-success btn-select-file" data-src="{$url_select_files}" data-type="iframe">
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
                                    <input name="url_video" id="url_video" value="{if !empty($brand.url_video)}{$brand.url_video}{/if}" type="text" class="form-control form-control-sm">
                                    <span class="form-text text-muted">
                                        {__d('admin', 'voi_kieu_video_youtube_url_chi_dien_ma_video')} 
                                        <img src="{ADMIN_PATH}/assets/media/note/upload_video.png" width="300px" />
                                    </span>
                                </div>

                                <div class="col-xl-4 col-lg-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-12">
                                            {$this->Form->select('type_video', $this->ListConstantAdmin->listTypeVideo(), ['id' => 'type_video', 'empty' => null, 'default' => "{if !empty($brand.type_video)}{$brand.type_video}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker mb-10'])}
                                        </div>

                                        {assign var = url_select_video value = "{CDN_URL}/myfilemanager/?type_file=video&cross_domain=1&token={$access_key_upload}&field_id=url_video&lang={LANGUAGE_ADMIN}"}

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
                    </div>
                </div>

                <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_tin_seo')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">      
                        <div class="row">
                            <div class="col-11">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tieu_de_seo')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-list-alt"></i>
                                            </span>
                                        </div>
                                        <input name="seo_title" value="{if !empty($brand.seo_title)}{$brand.seo_title|escape}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                    </div>
                                    <div id="progress-bar-title" class="progress mt-10">
                                        <div class="progress-bar progress-bar-striped"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'mo_ta_seo')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-file-text"></i>
                                            </span>
                                        </div>
                                        <input name="seo_description" value="{if !empty($brand.seo_description)}{$brand.seo_description|escape}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                    </div>
                                    <div id="progress-bar-description" class="progress mt-10">
                                        <div class="progress-bar progress-bar-striped"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tu_khoa_seo')}
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-tags"></i>
                                            </span>
                                        </div>

                                        <input name="seo_keyword" id="seo_keyword" value="{if !empty($brand.seo_keyword)}{$brand.seo_keyword}{/if}" type="text" class="form-control form-control-sm tagify-input">
                                    </div>
                                    <span class="form-text text-muted">
                                        {__d('admin', 'chi_ho_tro_{0}_tu_khoa_va_do_dai_moi_tu_khoa_khong_qua_{1}_ky_tu', [10, 45])}
                                    </span>
                                </div>
                            </div>
                        </div>      
                    </div>
                </div>

                <div class="kt-portlet nh-portlet nh-active-hover position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'phan_tich_bai_viet_va_tu_khoa')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div id="nh-analysis" class="form-group row">
                            <div class="col-xl-12 col-lg-12 all-analysis"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>