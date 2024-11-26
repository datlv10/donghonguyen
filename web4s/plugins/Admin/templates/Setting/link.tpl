{assign var = domain_name value = "{$this->getRequest()->scheme()}://{$this->getRequest()->host()}"}
<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <div class="btn-group">
                <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'cap_nhat')} (F1)
                </button>
            </div>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cai_dat_co_ban')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="kt-form">
                    
                    <div class="kt_radio-list mt-20">
                        <div class="form-group row mb-20">
                            <div class="col-lg-3 col-xl-2">
                                <label class="kt-radio kt-radio--tick kt-radio--brand">
                                    <input name="type" value="" {if empty($link.type)}checked="checked"{/if} type="radio" > 
                                    {__d('admin', 'mac_dinh')}
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-lg-9 col-xl-10">
                                <span class="kt-badge kt-badge--inline kt-label-bg-color-link fs-12 mr-5">
                                    {$domain_name}
                                </span>
                                <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill kt-badge--rounded fs-12">
                                    /tieu-de
                                </span>
                            </div>
                        </div>                        

                        <div class="form-group row mb-20">
                            <div class="col-lg-3 col-xl-2">
                                <label class="kt-radio kt-radio--tick kt-radio--brand">
                                    <input name="type" value="|ngon-ngu|tieu-de|" {if !empty($link.type) && $link.type eq '|ngon-ngu|tieu-de|'}checked="checked"{/if} type="radio"> 
                                    {__d('admin', 'ngon_ngu_va_ten_bai_viet')}
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-lg-9 col-xl-10">
                                <span class="kt-badge kt-badge--inline kt-label-bg-color-link fs-12 mr-5">
                                    {$domain_name}
                                </span>
                                <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill kt-badge--rounded fs-12">
                                    /ngon-ngu/tieu-de
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-xl-2">
                                {__d('admin', 'bo_tien_to_ngon_ngu_mac_dinh')}
                            </div>

                            <div class="col-lg-9 col-xl-10">
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--tick kt-radio--success">
                                        <input type="radio" name="ignore_language_default" value="1" {if !empty($link.ignore_language_default)}checked{/if}> 
                                        {__d('admin', 'co')}
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--tick kt-radio--danger">
                                        <input type="radio" name="ignore_language_default" value="0" {if empty($link.ignore_language_default)}checked{/if}> 
                                        {__d('admin', 'khong')}
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {* <div class="form-group row mb-20">
                            <div class="col-lg-3 col-xl-2">
                                <label class="kt-radio kt-radio--tick kt-radio--brand">
                                    <input name="type" value="|danh-muc|tieu-de|" {if !empty($link.type) && $link.type eq '|danh-muc|tieu-de|'}checked="checked"{/if} type="radio"> 
                                    {__d('admin', 'danh_muc_va_ten_bai_viet')}
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-lg-9 col-xl-10">
                                <span class="kt-badge kt-badge--inline kt-label-bg-color-link fs-12 mr-5">
                                    {$domain_name}
                                </span>
                                <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill kt-badge--rounded fs-12">
                                    /danh-muc/tieu-de
                                </span>
                            </div>
                        </div>

                        <div class="form-group row mb-20">
                            <div class="col-lg-3 col-xl-2">
                                <label class="kt-radio kt-radio--tick kt-radio--brand">
                                    {assign var = all_type value = [
                                        '|danh-muc|tieu-de|',
                                        '|ngon-ngu|tieu-de|'
                                    ]}
                                    <input id="custom-type" name="type" value="" {if !empty($link.type) && !$link.type|in_array:$all_type}checked="checked"{/if} type="radio"> 
                                        {__d('admin', 'tuy_bien')}
                                    <span></span>
                                </label>
                            </div>
                            
                            <div class="col-lg-9 col-xl-10">
                                <span class="kt-badge kt-badge--inline kt-label-bg-color-link fs-12 mr-5">
                                    {$domain_name}
                                </span>

                                <input name="custom_url" value="{if !empty($link.custom_url)}{$link.custom_url}{/if}" class="input-inline-link kt-shape-bg-color-2 fs-12" type="text" >

                                <div id="list-link" class="kt-section__content kt-section__content--solid-- mt-20">
                                    <a data-code="ngon-ngu" class="btn btn-label-brand btn-pill btn-sm mr-10 pt-5 pb-5 pl-20 pr-20 suggest-item">
                                        <i class="fa fa-language"></i>
                                        {__d('admin', 'ngon_ngu')}
                                    </a>

                                    <a data-code="tieu-de" class="btn btn-label-brand btn-pill btn-sm mr-10 pt-5 pb-5 pl-20 pr-20 suggest-item">
                                        <i class="fa fa-list"></i>
                                        {__d('admin', 'tieu_de')}
                                    </a>

                                    <a data-code="danh-muc" class="btn btn-label-brand btn-pill btn-sm mr-10 pt-5 pb-5 pl-20 pr-20 suggest-item">
                                        <i class="fa fa-tags"></i>
                                        {__d('admin', 'danh_muc')}
                                    </a>
                                </div>
                                
                            </div>
                        </div> *}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
