<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($template_code)}
                <div class="btn-group">
                    <button id="btn-save" type="button" class="btn btn-brand btn-sm btn-save" shortcut="112">
                        {__d('admin', 'luu_giao_dien')} (F1)
                    </button>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    {if !empty($template_code)}
        <div class="row" data-sticky-container>
            <div class="col-xl-10 col-lg-9">
                <form id="main-form" action="{ADMIN_PATH}/template/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
                    <div class="kt-portlet nh-portlet">
                        <div class="kt-portlet__body p-10">
                            <div class="row">
                                <div class="col-6">
                                    {if !empty($template_name)}
                                        <span class="kt-badge kt-badge--inline kt-badge--success fs-13 lh-30px">
                                            {$template_name}
                                        </span>
                                    {/if}
                                </div>

                                <div class="col-6 text-right">
                                    <div class="btn-group" role="group">
                                        <span class="btn btn-secondary w-50px active btn-select-device" data-device="0" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="{__d('admin', 'desktop')}">
                                            <i class="fa fa-desktop w-20px"></i>
                                        </span>   

                                        <span class="btn btn-secondary w-50px btn-select-device" data-device="1" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="{__d('admin', 'mobile')}">
                                            <i class="fa fa-mobile-alt w-10px"></i>
                                        </span>

                                        <span class="btn btn-secondary w-50px btn-select-device" data-device="2" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Tablet/Ipad">
                                            <i class="fa fa-tablet-alt w-10px"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet nh-portlet">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-6">
                                    <div id="wrap-dropdown-page" class="form-group">
                                        {$this->Form->select('page', $this->TemplateAdmin->getAllPageForDropdown(), ['id' => 'select-page', 'empty' => null, 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                    </div>
                                </div>

                                <div class="col-6 text-right">
                                    <span class="btn btn-sm btn-brand btn-update-page">
                                        <i class="fa fa-cogs fs-12"></i>
                                        {__d('admin', 'sua_cau_hinh_trang')}
                                    </span>

                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cogs"></i>
                                            {__d('admin', 'hanh_dong')}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item btn-create-page" href="javascript:;">
                                                <i class="fa fa-plus fs-14"></i>
                                                {__d('admin', 'them_trang_moi')}
                                            </a>

                                            <a class="dropdown-item btn-create-layout" href="javascript:;">
                                                <i class="fa fa-plus fs-14"></i>
                                                {__d('admin', 'them_trang_bo_cuc')}
                                            </a>

                                            <a class="dropdown-item btn-dulicate-page" href="javascript:;">
                                                <i class="fa fa-copy fs-14"></i>
                                                {__d('admin', 'nhan_ban_trang_nay')}
                                            </a>

                                            <div class="dropdown-divider"></div>

                                            <a class="dropdown-item btn-delete-config-page" href="javascript:;">
                                                <i class="fa fa-trash-alt fs-14"></i>
                                                {__d('admin', 'xoa_cau_hinh_trang')}
                                            </a>

                                            <a class="dropdown-item text-danger btn-delete-page" href="javascript:;">
                                                <i class="fa fa-trash-alt text-danger fs-14"></i>
                                                {__d('admin', 'xoa_trang')}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="wrap-structure-template" class="clearfix"></div>                        
                        </div>
                    </div>

                    <div class="d-none">                    
                        <input type="hidden" name="config" value="">
                        <input type="hidden" name="device" value="0">
                    </div>
                </form>
            </div>

            <div class="col-xl-2 col-lg-3">
                <div class="kt-portlet nh-portlet sticky" data-sticky="true" data-margin-top="140px" data-sticky-class="kt-sticky">
                    <div class="kt-portlet__body p-10">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="flaticon-search"></i>
                                            </span>
                                        </div>
                                        <input id="search-block" value="" type="text" class="form-control form-control-sm" autocomplete="off" placeholder="{__d('admin', 'tim_kiem_block')}">
                                    </div>
                                </div>

                                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-10"></div>

                                {if !empty($list_block)}
                                    <div class="clearfix mb-10">
                                        <span class="fs-11 fw-400 mb-10">
                                            {__d('admin', 'hay_keo_block_vao_trong_cot_ma_ban_muon')}
                                        </span>
                                    </div>
                                {/if}

                                <div class="nh-list-block">
                                    <ul>    
                                        {if !empty($list_block)}
                                            {foreach from = $list_block item = block}
                                                <li data-code="{if !empty($block.code)}{$block.code}{/if}" class="block-item {if empty($block.status)}disable{/if}">
                                                    <i class="fa fa-file-alt w-20px fs-14"></i>
                                                    <span>
                                                        {if !empty($block.name)}
                                                            {$block.name}
                                                        {/if}
                                                    </span>
                                                </li>
                                            {/foreach}
                                        {else}
                                            <span class="fs-12 fw-400">
                                                {__d('admin', 'chua_co_block_nao_duoc_tao_tren_he_thong')}.
                                                <a href="{ADMIN_PATH}/template/block/add">
                                                    {__d('admin', 'tao_block_moi')}
                                                </a>
                                            </span>
                                        {/if}
                                    </ul>
                                </div>   
                            </div>                        
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    {else}
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__body p-10">
                <span class="fs-12 fw-400">
                    {__d('admin', 'chua_co_giao_dien_nao_duoc_kich_hoat')}.
                    <a href="{ADMIN_PATH}/template/list" target="_blank">
                        {__d('admin', 'quan_ly_giao_dien')}
                    </a>
                </span>
            </div>
        </div>
    {/if}
</div>

{$this->element('../Template/popover_setting_row')}
{$this->element('../Template/modal_row_setting_general')}
{$this->element('../Template/modal_row_setting_column')}

<div id="page-info-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thong_tin_trang')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="page-info-form" action="{ADMIN_PATH}/template/page/save" method="POST" autocomplete="off"></form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-save-page" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="layout-info-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thong_tin_trang_bo_cuc')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                <form id="layout-info-form" action="{ADMIN_PATH}/template/page/save-layout" method="POST" autocomplete="off"></form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-save-layout" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="block-info-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'thong_tin_block')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-save-info-block" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>