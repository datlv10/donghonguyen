{assign var = url_list value = "{ADMIN_PATH}/tag"}
{assign var = url_add value = "{ADMIN_PATH}/tag/add"}
{assign var = url_edit value = "{ADMIN_PATH}/tag/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/tag/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-6 col-lg-8 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_the')}
                                 <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($tag.name)}{$tag.name}{/if}" class="form-control form-control-sm nh-format-link" type="text" maxlength="255">
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
                                <input name="link" value="{if !empty($tag.url)}{$tag.url}{/if}" type="text" class="form-control form-control-sm nh-link" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'ngon_ngu')}
                    </label>
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-12">
                            {$this->Form->select('lang', $list_languages, ['empty' => null, 'default' => "{if !empty($tag.lang)}{$tag.lang}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'mo_ta_the_bai_viet')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">                
                <div class="form-group">
                    <label>
                        {__d('admin', 'noi_dung')}
                    </label>
                    <div class="clearfix">
                        <textarea name="content" id="content" class="mce-editor">{if !empty($tag.content)}{$tag.content}{/if}</textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_seo')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
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
                        <input name="seo_keyword" id="seo_keyword" value="{if !empty($tag.seo_keyword)}{$tag.seo_keyword}{/if}" type="text" class="form-control form-control-sm tagify-input">
                    </div>
                    <span class="form-text text-muted">
                        {__d('admin', 'chi_ho_tro_{0}_tu_khoa_va_do_dai_moi_tu_khoa_khong_qua_{1}_ky_tu', [10, 45])}
                    </span>
                </div>

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
                        <input name="seo_title" value="{if !empty($tag.seo_title)}{$tag.seo_title}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
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
                        <input name="seo_description" value="{if !empty($tag.seo_description)}{$tag.seo_description}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                    </div>
                </div>                
            </div>
        </div>
    </form>
</div>