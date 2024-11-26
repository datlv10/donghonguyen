<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <div class="btn-group">
                <span id="btn-save" class="btn btn-sm btn-brand btn-save" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'cap_nhat')} (F1)
                </span>
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
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>

                {if !empty($languages)}
                    <div class="kt-portlet__head-toolbar">
                        <ul class="nav nav-tabs border-0" role="tablist">
                            {foreach from = $languages key = code item = item}
                                {if !empty($item)}
                                    <li class="nav-item">
                                        <a id="add-item" class="btn btn-sm btn-default {if !empty($code) && !empty($lang) && $code == $lang}active{/if} list-flags {if !$item@last}mr-10{/if}" data-toggle="tab" href="#kt_tab_setting_{$code}" role="tab" aria-selected="false">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$code}.svg" alt="{$item}" class="flag mr-5">
                                            {$item}
                                        </a>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div>

            <!--begin::Form-->
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="tab-content">
                                {foreach from = $languages key = code item = item}
                                    {if !empty($code)}
                                        <div class="tab-pane {if !empty($code) && !empty($lang) && $code == $lang}active{/if}" id="kt_tab_setting_{$code}" role="tabpanel">
                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'ten_website')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-globe-americas"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_website_name" value="{if !empty($website_info[$code].website_name)}{$website_info[$code].website_name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'ten_cong_ty')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-city"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_company_name" value="{if !empty($website_info[$code].company_name)}{$website_info[$code].company_name}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'hotline')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-headset"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_hotline" value="{if !empty($website_info[$code].hotline)}{$website_info[$code].hotline}{/if}" type="text" class="form-control form-control-sm phone-input">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'so_dien_thoai')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-phone"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_phone" value="{if !empty($website_info[$code].phone)}{$website_info[$code].phone}{/if}" type="text" class="form-control form-control-sm phone-input">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'email')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-envelope"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_email" value="{if !empty($website_info[$code].email)}{$website_info[$code].email}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'dia_chi')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-map"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_address" value="{if !empty($website_info[$code].address)}{$website_info[$code].address}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-12">
                                                    {__d('admin', 'copyright')}
                                                </label>
                                                <div class="input-group col-lg-10">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fa fa-copyright"></i>
                                                        </span>
                                                    </div>
                                                    <input name="{$code}_copyright" value="{if !empty($website_info[$code].copyright)}{$website_info[$code].copyright}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="w-100">
                                    {__d('admin', 'favicon')}
                                </label>
                                {assign var = favicon value = "/favicon.ico?v={$smarty.now}"}
                                {if !empty($website_info[$lang].favicon)}
                                    {$favicon = "{CDN_URL}{$website_info[$lang].favicon}?v={$smarty.now}"}
                                {/if}

                                {assign var = bg_favicon value = "background-image: url('{$favicon}');background-size: contain;background-position: 50% 50%;"}

                                <div class="kt-avatar kt-avatar--outline">
                                    <div class="kt-avatar__holder kt-favicon favicon-preview" style="{$bg_favicon}"></div>  
                                    <label class="kt-avatar__upload">
                                        <i class="fa fa-pen"></i>
                                        <input id="favicon-select"  type="file" accept="image/x-icon" style="display: none;" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}">
                                        <input type="hidden" name="favicon_select" value="">
                                    </label>                                  
                                </div>

                                <span class="form-text text-muted">
                                    {__d('admin', 'chi_chap_nhan_dinh_dang')}: 
                                    <strong>
                                        .ico
                                    </strong>
                                    .
                                    <a href="https://convertio.co/vn/png-ico/" target="_blank">
                                        <i class="fa fa-external-link-alt"></i>
                                        {__d('admin', 'chuyen_doi_sang_dinh_dang_{0}', ['ico'])}                                        
                                    </a>
                                </span>
                            </div>

                            <div class="form-group">
                                <label class="w-100">{__d('admin', 'logo_cong_ty')}</label>
                                {assign var = bg_logo value = ''}
                                {if !empty($website_info[$lang].company_logo)}
                                    {assign var = bg_logo value = "background-image: url('{CDN_URL}{$website_info[$lang].company_logo}');background-size: contain;background-position: 50% 50%;"}
                                {/if}

                                {assign var = url_select_logo value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=company_logo"}

                                <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($bg_logo)}kt-avatar--changed{/if}">
                                    <a {if !empty($website_info[$lang].company_logo)}href="{CDN_URL}{$website_info[$lang].company_logo}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$bg_logo}"></a>
                                    <label data-src="{$url_select_logo}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                        <i class="fa fa-pen"></i>
                                    </label>
                                    <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                        <i class="fa fa-times"></i>
                                    </span>

                                    <input id="company_logo" name="company_logo" value="{if !empty($website_info[$lang].company_logo)}{htmlentities($website_info[$lang].company_logo)}{/if}" type="hidden" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Form-->
        </div>

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'chi_nhanh')}
                    </h3>
                </div>

                {if !empty($languages)}
                    <div class="kt-portlet__head-toolbar">
                        <ul class="nav nav-tabs border-0" role="tablist">
                            {foreach from = $languages key = code item = item}
                                {if !empty($item)}
                                    <li class="nav-item">
                                        <a id="add-item" class="btn btn-sm btn-default {if !empty($code) && !empty($lang) && $code == $lang}active{/if} list-flags {if !$item@last}mr-10{/if}" data-toggle="tab" href="#kt_tab_sub_branch_{$code}" role="tab" aria-selected="false">
                                            <img src="{ADMIN_PATH}{FLAGS_URL}{$code}.svg" alt="{$item}" class="flag mr-5">
                                            {$item}
                                        </a>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div>

            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="tab-content">
                        {foreach from=$languages key=code item=item}
                            {if !empty($code)}
                                <div class="tab-pane {if !empty($code) && !empty($lang) && $code == $lang}active{/if}" id="kt_tab_sub_branch_{$code}" role="tabpanel">
                                    <div class="kt_repeater">
                                        <div class="row" data-repeater-list="sub_branch[{$code}]">
                                            {if !empty($sub_branch) && !empty($sub_branch[$code])}
                                                {foreach from = $sub_branch[$code] key = key item = item}
                                                    <div class="col-lg-6 pb-20" data-repeater-item>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12">
                                                                {__d('admin', 'ten_co_so')}
                                                            </label>
                                                            <div class="input-group col-lg-10">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <i class="fa fa-store"></i>
                                                                    </span>
                                                                </div>
                                                                <input name="sub_name" value="{if !empty($item.sub_name)}{$item.sub_name}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12">
                                                                {__d('admin', 'so_dien_thoai')}
                                                            </label>
                                                            <div class="input-group col-lg-10">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <i class="fa fa-phone"></i>
                                                                    </span>
                                                                </div>
                                                                <input name="sub_phone" value="{if !empty($item.sub_phone)}{$item.sub_phone}{/if}" type="text" class="form-control form-control-sm phone-input">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12">
                                                                {__d('admin', 'email')}
                                                            </label>
                                                            <div class="input-group col-lg-10">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <i class="fa fa-envelope"></i>
                                                                    </span>
                                                                </div>
                                                                <input name="sub_email" value="{if !empty($item.sub_email)}{$item.sub_email}{/if}" type="text" class="form-control form-control-sm">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-12">
                                                                {__d('admin', 'dia_chi')}
                                                            </label>
                                                            <div class="input-group col-lg-10">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <i class="fa fa-map"></i>
                                                                    </span>
                                                                </div>
                                                                <input name="sub_address" value="{if !empty($item.sub_address)}{$item.sub_address}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label></label>
                                                            <div class="text-left">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                                                    <i class="la la-trash-o"></i>
                                                                    {__d('admin', 'xoa')}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            {else}
                                                <div class="col-lg-6 pb-20" data-repeater-item>
                                                    <div class="form-group row">
                                                        <label class="col-lg-12">
                                                            {__d('admin', 'ten_co_so')}
                                                        </label>
                                                        <div class="input-group col-lg-10">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-store"></i>
                                                                </span>
                                                            </div>
                                                            <input name="sub_name" value="" type="text" class="form-control form-control-sm" maxlength="255">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-12">
                                                            {__d('admin', 'so_dien_thoai')}
                                                        </label>
                                                        <div class="input-group col-lg-10">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-phone"></i>
                                                                </span>
                                                            </div>
                                                            <input name="sub_phone" value="" type="text" class="form-control form-control-sm phone-input">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-12">
                                                            {__d('admin', 'email')}
                                                        </label>
                                                        <div class="input-group col-lg-10">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-envelope"></i>
                                                                </span>
                                                            </div>
                                                            <input name="sub_email" value="" type="text" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-12">
                                                            {__d('admin', 'dia_chi')}
                                                        </label>
                                                        <div class="input-group col-lg-10">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-map"></i>
                                                                </span>
                                                            </div>
                                                            <input name="sub_address" value="" type="text" class="form-control form-control-sm" maxlength="255">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label></label>
                                                        <div class="text-left">
                                                            <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                                                <i class="la la-trash-o"></i>
                                                                {__d('admin', 'xoa')}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            {/if}
                                        </div>

                                        <div class="form-group form-group-last">
                                            <a href="javascript:;" data-repeater-create class="btn btn-sm btn-brand">
                                                <i class="la la-plus"></i>{__d('admin', 'them_moi_chi_nhanh')}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>