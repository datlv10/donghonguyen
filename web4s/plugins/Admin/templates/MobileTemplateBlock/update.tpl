{assign var = url_list value = "{ADMIN_PATH}/mobile-app/block"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'cap_nhat_block')}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>            
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-config-form" action="{ADMIN_PATH}/mobile-app/block/save-main-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <i class="fa fa-file-alt mr-5"></i>
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group form-group row">
                    <label class="col-xl-1 col-lg-2 col-form-label">
                        {__d('admin', 'loai_block')}:
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {assign var = type value = "{if !empty($block_info.type)}{$block_info.type}{/if}"}
                            {assign var = list_type_block value = $this->MobileTemplateAdmin->getListTypeMobileBlock()}

                            {if !empty($type) && !empty($list_type_block[$type])}
                                {$list_type_block[$type]}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-lg-9 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_block')}
                                <span class="kt-font-danger">*</span>
                            </label>

                            <input name="name" value="{if !empty($block_info.name)}{$block_info.name}{/if}" class="form-control form-control-sm" type="text">
                        </div>                        

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'trang_thai')}
                                    </label>

                                    <div class="kt-radio-inline mt-5">
                                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                            <input type="radio" name="status" value="1" {if !empty($block_info.status)}checked="true"{/if}> 
                                                {__d('admin', 'hoat_dong')}
                                            <span></span>
                                        </label>

                                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                            <input type="radio" name="status" value="0" {if empty($block_info.status)}checked="true"{/if}> 
                                                {__d('admin', 'ngung_hoat_dong')}
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>

                <div class="d-none">
                    <input id="type-block" type="hidden" value="{if !empty($block_info.type)}{$block_info.type}{/if}">
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-main-config-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
        </div>
    </form>

    {if !empty($type)}
        <div id="wrap-block-config" data-code="{if !empty($code)}{$code}{/if}" class="clearfix">
            {$this->element("../MobileTemplateBlock/element_config_{$type}", ['config' => $config])}
        </div>
    {/if}
</div>