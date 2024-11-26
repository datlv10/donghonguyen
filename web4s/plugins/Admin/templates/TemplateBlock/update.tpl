{assign var = url_list value = "{ADMIN_PATH}/template/block/list"}

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
    <form id="main-config-form" action="{ADMIN_PATH}/template/block/save/main-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet kt-portlet--collapsed" data-ktportlet="true">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {if !empty($block_info.name)}
                            {$block_info.name}
                        {else}
                            {__d('admin', 'thong_tin_chinh')}
                        {/if}                    
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <a href="javascript:;" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-secondary btn-icon-md">
                            <i class="la la-angle-down"></i>
                        </a>                       
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group form-group row">
                    <label class="col-xl-1 col-lg-2 col-form-label">
                        {__d('admin', 'loai_block')}
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {assign var = type value = "{if !empty($block_info.type)}{$block_info.type}{/if}"}
                            {assign var = list_type_block value = $this->TemplateAdmin->getListTypeBlock()}

                            {if !empty($type) && !empty($list_type_block[$type])}
                                {$list_type_block[$type]}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="form-group form-group row">
                    <label class="col-xl-1 col-lg-2 col-form-label">
                        {__d('admin', 'ma')}
                    </label>
                    <div class="col-xl-10 col-lg-10">
                        <span class="form-control-plaintext kt-font-bolder">
                            {if !empty($code)}
                                {$code}
                            {/if}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-md-9 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_block')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($block_info.name)}{$block_info.name}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>                    
                </div>                

                <div class="row">
                    <div class="col-xl-3 col-md-4 col-12">
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

                <div class="d-none">
                    <input id="type-block" type="hidden" value="{if !empty($block_info.type)}{$block_info.type}{/if}">
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <span class="btn btn-sm btn-brand btn-main-config-save">
                        {__d('admin', 'luu_thong_tin')}
                    </span>
                </div>
            </div>
        </div>
    </form>

    <div nh-wrap="block-config" class="kt-portlet nh-portlet mb-100">
        <div class="kt-portlet__body pt-0">
            {$this->element('../TemplateBlock/block_config')}
        </div>
    </div>
</div>

<div id="add-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_giao_dien_block')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="add-view-form" action="{ADMIN_PATH}/template/block/add-view{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_giao_dien')}
                        </label>
                        <div class="input-group">
                            <input name="name_file" value="" type="text" class="form-control form-control-sm required">
                            <div class="input-group-append">
                                <span class="input-group-text">.tpl</span>
                            </div>
                        </div>
                    </div> 
                </form>                 
            </div>

            <div class="modal-footer">
                <span class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </span>
                
                <span id="btn-save-view" class="btn btn-sm btn-brand">
                    <i class="fa fa-plus"></i>
                    {__d('admin', 'them_moi')}
                </span>
            </div>
        </div>
    </div>
</div>