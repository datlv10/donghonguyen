{assign var = domain_name value = "{$this->getRequest()->scheme()}://{$this->getRequest()->host()}"}
<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_sitemap')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="sitemap-form" action="{ADMIN_PATH}/setting/save/sitemap" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-xl-2 col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'phan_nhom_sitemap')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="combine_sitemap" value="1" {if !empty($sitemap.combine_sitemap)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="combine_sitemap" value="0" {if empty($sitemap.combine_sitemap)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-xl-10 col-lg-9 col-12">
                        <span class="form-text text-muted">
                            - {__d('admin', 'chia_sitemap_thanh_cac_nhom_nho_giup_viec_hien_thi_sitemap_nhanh_va_de_dang_hon')}
                        </span>
                        <span class="form-text text-muted">
                            - {__d('admin', 'chung_toi_khuyen_khich_su_dung_chuc_nang_nay_voi_cac_website_co_tu_500_duong_dan_tro_len')}
                        </span>                        
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid"></div>

                <div class="row">
                    <div class="col-xl-2 col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'chia_sitemap_theo_nam')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="split_by_year" value="1" {if !empty($sitemap.split_by_year)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="split_by_year" value="0" {if empty($sitemap.split_by_year)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-10 col-lg-9 col-12">
                        <span class="form-text text-muted">
                            - {__d('admin', 'khi_website_cua_ban_hoat_dong_lau_nam_va_da_co_qua_nhieu_duong_dan_thi_ban_nen_chia_sitemap_theo_cac_nam_de_dam_bao_sitemap_luon_duoc_hien_thi_tot_nhat')}
                        </span>

                        <span class="form-text text-muted">
                            - {__d('admin', 'chi_hoat_dong_khi_sitemap_duoc_cau_hinh_phan_theo_nhom')}
                        </span>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

                <div class="row">
                    <div class="col-12">
                        <button id="btn-save-sitemap" type="button" class="btn btn-brand btn-sm">
                            {__d('admin', 'luu_cau_hinh_sitemap')}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'cau_hinh_duong_dan')}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="url-form" action="{ADMIN_PATH}/setting/save/url" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-xl-2 col-lg-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'bo_dinh_dang_unicode_cho_duong_dan')}
                            </label>

                            <div class="kt-radio-inline">
                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                    <input type="radio" name="disable_unicode" value="1" {if !empty($url.disable_unicode)}checked{/if}> 
                                    {__d('admin', 'co')}
                                    <span></span>
                                </label>

                                <label class="kt-radio kt-radio--tick kt-radio--danger">
                                    <input type="radio" name="disable_unicode" value="0" {if empty($url.disable_unicode)}checked{/if}> 
                                    {__d('admin', 'khong')}
                                    <span></span>
                                </label>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button id="btn-save-url" type="button" class="btn btn-brand btn-sm">
                            {__d('admin', 'luu_thong_tin')}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'the_bai_viet')}
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <form id="tag-form" action="{ADMIN_PATH}/setting/save/tag" method="POST" autocomplete="off">
                        <div class="row">
                            {assign var = prefix_url value = "{if !empty($tag.prefix_url)}{$tag.prefix_url}{/if}"}
                            <div class="col-xl-2 col-lg-3 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'duong_dan_the')}
                                    </label>
                                    <input id="prefix-url" name="prefix_url" value="{$prefix_url}" class="form-control form-control-sm" type="text" maxlength="100">
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-12">
                                <div class="form-text text-muted kt-margin-t-25">
                                    - {__d('admin', 'thay_doi_duong_dan_the_bai_viet')}
                                </div>
                                <span class="kt-badge kt-badge--inline kt-label-bg-color-link fs-12">
                                    {$domain_name}/
                                    <span id="label-prefix-url" class="kt-font-bolder">
                                        {$prefix_url}
                                    </span>
                                    /duong-dan-the
                                </span>
                            </div>

                            {assign var = prefix_seo_title value = "{if !empty($tag.prefix_seo_title)}{$tag.prefix_seo_title}{else}{__d('admin', 'tien_to_tieu_de_seo')} | {/if}"}

                            <div class="col-xl-2 col-lg-3 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tien_to_tieu_de_seo')}
                                    </label>
                                    <input id="prefix-seo-title" name="prefix_seo_title" value="{if !empty($tag.prefix_seo_title)}{$tag.prefix_seo_title}{/if}" class="form-control form-control-sm" type="text" maxlength="100">
                                </div>
                            </div>
                            <div class="col-xl-10 col-lg-9 col-12">
                                <span class="form-text text-muted kt-margin-t-25">
                                    - {__d('admin', 'them_tien_to_vao_tieu_de_seo_cua_the')}
                                </span>  
                                <span class="form-text text-muted">
                                    - {__d('admin', 'nhap')}: "<span class="label-prefix-seo-title">{$prefix_seo_title}</span>"
                                </span>   
                                <span class="form-text text-muted">
                                    - {__d('admin', 'ket_qua')}:
                                    "<span class="label-prefix-seo-title kt-font-bolder">{$prefix_seo_title}</span>duong-dan-the"
                                </span>  
                            </div>

                            {assign var = suffixes_seo_title value = "{if !empty($tag.suffixes_seo_title)}{$tag.suffixes_seo_title}{else} | {__d('admin', 'hau_to_tieu_de_seo')}{/if}"}
                            <div class="col-xl-2 col-lg-3 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'hau_to_tieu_de_seo')}
                                    </label>
                                    <input id="suffixes-seo-title" name="suffixes_seo_title" value="{if !empty($tag.suffixes_seo_title)}{$tag.suffixes_seo_title}{/if}" class="form-control form-control-sm" type="text" maxlength="100"> 
                                </div>
                            </div>

                            <div class="col-xl-10 col-lg-9 col-12">
                                <span class="form-text text-muted kt-margin-t-25">
                                    - {__d('admin', 'them_hau_to_vao_tieu_de_seo_cua_the')}
                                </span>   
                                <span class="form-text text-muted">
                                    - {__d('admin', 'nhap')}: "<span class="label-suffixes-seo-title">{$suffixes_seo_title}</span>"
                                </span>
                                <span class="form-text text-muted">
                                    - {__d('admin', 'ket_qua')}:
                                    "duong-dan-the<span class="label-suffixes-seo-title kt-font-bolder">{$suffixes_seo_title}</span>"
                                </span>       
                            </div>
                        </div>

                        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

                        <div class="row">
                            <div class="col-12">
                                <button id="btn-save-tag" type="button" class="btn btn-brand btn-sm">
                                    {__d('admin', 'luu_cau_hinh_the_bai_viet')}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            ROBOTS.TXT
                        </h3>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div class="row">                    
                        <div class="col-lg-6">
                            <label>ROBOTS.TXT</label>

                            <div class="form-group">
                                {assign var = dropzone_params value = [
                                    'id_dropzone' => 'robots',
                                    'title_dropzone' => "{__d('admin', 'tai_tep_robots')}",
                                    'slogan_dropzone' => "{__d('admin', 'chon_tep_robots')}"
                                ]}

                                {$this->element('Admin.page/dropzone_upload', ['dropzone_params' => $dropzone_params])}

                                <div class="dropzone dropzone-multi">
                                    <div class="template-container">
                                        <div class="dropzone-item">
                                            <div class="dropzone-file">
                                                <div class="dropzone-filename" title="robots.txt">
                                                    <span data-dz-name>
                                                        {if !empty($exist_robots_file)}
                                                            robots.txt
                                                        {else}
                                                            <span class="kt-font-danger">
                                                                {__d('admin', 'chua_co_file_robots')}
                                                            </span>
                                                        {/if}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {if !empty($exist_robots_file)}
                                    <a class="btn btn-label-primary btn-sm mt-20" target="_bank" href="/robots.txt">
                                        <i class="fa fa-file-alt"></i> 
                                        {__d('admin', 'xem_tep_robots')}
                                    </a>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>