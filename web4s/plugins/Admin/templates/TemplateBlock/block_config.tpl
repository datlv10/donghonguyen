{assign var = languages value = $this->LanguageAdmin->getList()}
{assign var = lang_default value = $this->LanguageAdmin->getDefaultLanguage()}

{assign var = normal_data_extend value = []}
{if !empty($block_info.normal_data_extend)}
    {$normal_data_extend = $block_info.normal_data_extend|json_decode:1}
{/if}

<div id="wrap-block-config" data-code="{if !empty($code)}{$code}{/if}" class="clearfix">
    <ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-primary" role="tablist">
        <li class="nav-item">
            <a href="#tab-general" class="nav-link active" data-toggle="tab" role="tab">
                <i class="fa fa-cogs"></i> 
                {__d('admin', 'cau_hinh_chung')}
            </a>
        </li>

        <li class="nav-item">
            <a href="#tab-data-extend" class="nav-link" data-toggle="tab" role="tab">
                <i class="fa fa-database"></i> 
                {__d('admin', 'du_lieu_mo_rong')}
            </a>
        </li>

        {if $type != {HTML}}
            <li class="nav-item">
                <a href="#tab-modify-view" class="nav-link" data-toggle="tab" role="tab">
                    <i class="fa fa-edit"></i> 
                    {__d('admin', 'sua_giao_dien')}
                </a>
            </li>
        {/if}

        <li class="nav-item">
            <a href="#tab-logs" class="nav-link" data-toggle="tab" role="tab">
                <i class="fa fa-history"></i>
                {__d('admin', 'lich_su_cap_nhat')}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tab-general" class="tab-pane active" role="tabpanel">
            <form id="general-config-form" action="{ADMIN_PATH}/template/block/save/general-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label>
                                Style Class
                            </label>
                            <input name="config[class]" value="{if !empty($config.class)}{$config.class}{/if}" class="form-control form-control-sm" type="text">
                            <span class="form-text text-muted">
                                {__d('admin', 'nhung_class_cau_hinh_se_duoc_dat_o_the_bao_ngoai_cua_block')}
                            </span>
                        </div>
                    </div>

                    {if !empty($files_view) && $type != {HTML}}
                        <div class="col-lg-3 col-12">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'giao_dien_block')}
                                </label>
                                {$this->Form->select('view', $files_view, ['empty' => null, 'default' => "{if !empty($block_info.view)}{$block_info.view}{else}view.tpl{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                            </div>
                        </div>
                    {/if}

                    {if $type == {HTML}}
                        <input type="hidden" name="view" value="{$code}.tpl">
                    {/if}

                    <div class="col-lg-3 col-12">
                        <div class="form-group">
                            <label class="kt-font-danger">
                                {__d('admin', 'su_dung_cache')} *
                            </label>
                            
                            <div class="kt-radio-inline mt-5">
                                <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                    <input type="radio" name="config[cache]" value="0" {if empty($config.cache)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                    <input type="radio" name="config[cache]" value="1" {if !empty($config.cache) || !isset($config.cache)}checked{/if}> 
                                        {__d('admin', 'co')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="wrap-config-type-load" class="clearfix d-none">
                    {$this->element('../TemplateBlock/config_type_load', ['type_load' => "{if !empty($config.type_load)}{$config.type_load}{else}{NORMAL}{/if}", 'config' => $config])}
                </div>

                {if !empty($type)}
                    {$this->element("../TemplateBlock/element_config_{$type}", ['config' => $config])}
                {/if}

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <div class="btn-group">
                        <span class="btn btn-sm btn-brand btn-save">
                            {__d('admin', 'luu_cau_hinh')}
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <div id="tab-data-extend" class="tab-pane" role="tabpanel">
            <form id="data-extend-form" action="{ADMIN_PATH}/template/block/save/data-extend{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">                

                <div class="form-group">
                    <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
                        <i class="fa fa-images"></i>
                        {__d('admin', 'chon_anh_giao_dien')}
                    </span>
                    <input id="image_template" type="hidden" value="">
                    
                    {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

                    <span btn-select-media-block="cdn" action="copy" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand">
                        <i class="fa fa-photo-video"></i>
                        {__d('admin', 'chon_anh_tu_cdn')}
                    </span>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed mt-10"></div>

                {* active tab dữ liệu Json khi chưa có cập nhật ở tab normal *}
                {assign var = active_json_tab value = false}
                {if !empty($block_info.data_extend) && empty($normal_data_extend)}
                    {$active_json_tab = true}
                {/if}
                <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                    <li class="nav-item">
                        <a href="#tab-normal-data-extend" class="nav-link {if !$active_json_tab}active{/if}" data-toggle="tab" role="tab">
                            {__d('admin', 'co_ban')}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#tab-json-data-extend" class="nav-link {if $active_json_tab}active{/if}" data-toggle="tab" role="tab">
                            {__d('admin', 'nang_cao')}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="tab-normal-data-extend" class="tab-pane {if !$active_json_tab}active{/if}" role="tabpanel">
                        <div class="form-group">
                            <label class="lh-35px m-0">
                                {__d('admin', 'nhan_da_ngon_ngu')}
                                <i class="fs-11 text-muted">
                                    (*{__d('admin', 'dung_de_hien_thi_cac_du_lieu_theo_ngon_ngu')})
                                </i>
                            </label>

                            <span id="btn-add-locale-label" class="float-right btn btn-success btn-sm mb-5">
                                <i class="fa fa-plus"></i>
                                {__d('admin', 'them_nhan_moi')}
                            </span>

                            <table id="table-locale-label" class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="w-20">
                                            {__d('admin', 'ma')}
                                        </th>

                                        {if !empty($languages)}
                                            {foreach from = $languages key = lang item = language}
                                                <th>
                                                    <img src="{ADMIN_PATH}{FLAGS_URL}{$lang}.svg" alt="{$lang}" class="h-15px w-15px rounded mr-5"/>
                                                    {$language}
                                                </th>
                                            {/foreach}
                                        {/if}

                                        <th class="w-3 pr-0 text-center">
                                            <i class="fa fa-cog"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {* Nếu không có dữ liệu locale thì set value của nó bằng 1 mảng mặc định để không phải xử lý riêng với trường hợp nó rỗng *}
                                    {if empty($normal_data_extend.locale)}
                                        {$normal_data_extend.locale = ['vi' => ['' => '']]}
                                    {/if}

                                    {$first_language = $normal_data_extend.locale|reset}

                                    {foreach from = $first_language key = key item = item}
                                        <tr>
                                            <td class="pl-0 pr-0">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-barcode"></i>
                                                        </span>
                                                    </div>
                                                    <input value="{if !empty($key)}{$key}{/if}" nh-input="key" type="text" class="form-control form-control-sm fs-12 text-danger">
                                                </div>
                                            </td>

                                            {if !empty($languages)}
                                                {foreach from = $languages key = lang item = language}
                                                    {assign var = value value = ''}
                                                    {if !empty($normal_data_extend.locale[$lang][$key])}
                                                        {$value = $normal_data_extend.locale[$lang][$key]}
                                                    {/if}
                                                    <td class="pr-0">
                                                        <div class="input-group">                                                            
                                                            <textarea nh-input="value" nh-language="{$lang}" class="form-control form-control-sm fs-12" rows="1" placeholder="{$language}" style="min-height: 33px;">{if !empty($value)}{$value}{/if}</textarea>
                                                            <div class="input-group-append">
                                                                {if $lang == $lang_default}
                                                                    <span nh-btn="data-extend-translate" nh-language-default="{$lang_default}" class="input-group-text cursor-p" title="{__d('admin', 'dich')}">
                                                                        <i class="fa fa-language kt-font-brand"></i>
                                                                    </span>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                {/foreach}
                                            {/if}
                                            
                                            <td class="pr-0 text-center">
                                                <i nh-delete="data-extend" class="fa fa-trash-alt btn btn-secondary btn-sm"></i>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="tab-json-data-extend" class="tab-pane {if $active_json_tab}active{/if}" role="tabpanel">
                        <span class="form-text text-muted mb-10">
                            Dữ liệu mở rộng sẽ được lưu dưới dạng <a href="https://www.w3schools.com/whatis/whatis_json.asp" target="_blank">JSON</a> và block có thể đọc được dữ liệu này để hiển thị ra ngoài.
                            <a href="javascript:;" data-toggle="modal" data-target="#data-example-modal">
                                Dữ liệu mẫu hay dùng
                            </a>
                        </span>

                        <div id="editor-data-extend" class="nh-editor"></div>  
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-10"></div>

                <br class="mb-20">

                <input id="input-data-extend" name="data_extend" value="{if !empty($block_info.data_extend)}{htmlentities($block_info.data_extend)}{/if}" type="hidden">
                <input id="input-normal-data-extend" name="normal_data_extend" value="{if !empty($block_info.data_extend)}{htmlentities($block_info.data_extend)}{/if}" type="hidden">

                <div class="form-group mb-0">
                    <div class="btn-group">
                        <span class="btn btn-sm btn-brand btn-save">
                            {__d('admin', 'luu_du_lieu')}
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <div id="tab-modify-view" class="tab-pane" role="tabpanel">
            <form id="modify-view-form" action="{ADMIN_PATH}/template/block/save/file-view{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
                
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="form-group validated">
                            <label>
                                {__d('admin', 'giao_dien_block')}
                            </label>
                            {$this->Form->select('view_file', $files, ['id' => 'view-file', 'empty' => null, 'default' => "{if !empty($block_info.view)}{$block_info.view}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker is-invalid'])}
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="form-group">
                            <label class="h-15px"></label>
                            <div class="clearfix">
                                <span id="btn-add-view" class="btn btn-sm btn-success">
                                    <i class="fa fa-plus"></i>
                                    {__d('admin', 'them_giao_dien_moi')}
                                </span>
                                
                                <span id="btn-delete-view" class="btn btn-sm btn-secondary">
                                    <i class="fa fa-trash-alt"></i>
                                    {__d('admin', 'xoa_giao_dien')}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>

                <div class="form-group">
                    <span btn-select-media-block="template" action="copy" data-src="{ADMIN_PATH}/myfilemanager/?cross_domain=1&token={$filemanager_access_key_template}&field_id=image_template" data-type="iframe" class="btn btn-sm btn-success">
                        <i class="fa fa-images"></i>
                        {__d('admin', 'chon_anh_giao_dien')}
                    </span>
                    <input id="image_template" type="hidden" value="">
                    
                    {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=image_block"}

                    <span btn-select-media-block="cdn" action="copy" data-src="{$url_select_image}" data-type="iframe" class="btn btn-sm btn-brand">
                        <i class="fa fa-photo-video"></i>
                        {__d('admin', 'chon_anh_tu_cdn')}
                    </span>                    

                    <span nh-btn="view-full-screen-editor" class="btn btn-sm btn-secondary float-right">
                        <i class="fa fa-expand"></i>
                        {__d('admin', 'toan_man_hinh')}
                    </span>

                    <span nh-btn="view-history-change-file" data-path="{if !empty($path_first_file)}{$path_first_file}{/if}" class="btn btn-sm btn-secondary mr-5 float-right">
                        <i class="fa fa-file-alt"></i>
                        {__d('admin', 'lich_su_thay_doi_cua_tep')}
                    </span>
                </div>

                <div id="editor-modify-view" class="nh-editor"></div>
                <input id="input-view-file-content" name="view_file_content" value="{if !empty($file_first_content)}{htmlentities($file_first_content)}{/if}" type="hidden">

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>

                <div class="form-group mb-0">
                    <span class="btn btn-sm btn-brand btn-save">
                        <i class="fa fa-check"></i>
                        {__d('admin', 'luu_giao_dien')}
                    </span>
                </div>
            </form>
        </div>

        <div id="tab-logs" class="tab-pane" role="tabpanel">
            <div nh-wrap="logs" class="kt-list-timeline">
                
            </div>
        </div>
    </div>
</div>

<div id="data-example-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'du_lieu_mo_rong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu dữ liệu đa ngôn ngữ (locale):
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "locale": {
        "vi": {
            "tieu_de_01": "Tiêu đề 01",
            "tieu_de_02": "Tiêu đề 02"
        },
        "en": {
            "tieu_de_01": "Title 01",
            "tieu_de_02": "Title đề 02"
        },
    }
}
</pre>
                    </div>
                </div>

                <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu cấu hình slider (Owl Carousel 2):
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "config_slider": {
        "items": 1,
        "margin": 0,        
        "loop" : true,
        "center": false,
        "mouseDrag": true,
        "touchDrag": true,
        "pullDrag": true,
        "freeDrag": true,
        "stagePadding": true,
        "merge": false,
        "mergeFit": true,
        "autoWidth": false,
        "startPosition": 0,
        "URLhashListener": false,
        "nav": true,
        "rewind": true,
        "navElement": "div",
        "slideBy": 1,
        "slideTransition": "",
        "dots": true,
        "dotsEach": false,
        "dotsData": false,
        "lazyLoad": false,
        "lazyLoadEager": 0,
        "autoplay": false,
        "autoplayTimeout": 5000,
        "autoplayHoverPause": false,
        "smartSpeed": 250,
        "video": false,
        "videoHeight": false,
        "videoWidth": false,
        "animateOut": false,
        "animateIn": false,
        "fallbackEasing": "swing",
        "itemElement": "div",
        "checkVisible":true,
        "responsive": {
            "0":{
                "items": 1
            },
            "600":{
                "items": 3
            },
            "1000":{
                "items": 5
            }
        }
    }
}
</pre>
                    </div>
                </div>

                <div class="kt-section">
                    <span class="kt-section__info">
                        Mẫu dữ liệu menu tuỳ biến:
                    </span>
                    <div class="kt-section__content kt-section__content--solid">
<pre>
{
    "locale": {
        "vi": {
            "data_sub_menu": [
                {
                    "name": "Giới thiệu",
                    "url": "/ve-chung-toi",
                    "children": [
                        {
                            "name": "Về chúng tôi",
                            "url": "/ve-chung-toi"
                        },
                        {
                            "name": "Địa chỉ",
                            "url": "/dia-chi"
                        },
                        {
                            "name": "Tầm nhìn - Sứ mệnh",
                            "url": "/tam-nhin-su-menh"
                        },
                        {
                            "name": "Tải Profile",
                            "url": "/profile"
                        }
                    ]
                }
            ]
        },
        "en": {
            "data_sub_menu": [
                {
                    "name": "Giới thiệu",
                    "url": "/ve-chung-toi",
                    "children": [
                        {
                            "name": "Về chúng tôi",
                            "url": "/ve-chung-toi"
                        },
                        {
                            "name": "Địa chỉ",
                            "url": "/dia-chi"
                        },
                        {
                            "name": "Tầm nhìn - Sứ mệnh",
                            "url": "/tam-nhin-su-menh"
                        },
                        {
                            "name": "Tải Profile",
                            "url": "/profile"
                        }
                    ]
                }
            ]
        }
    }
}
</pre>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
