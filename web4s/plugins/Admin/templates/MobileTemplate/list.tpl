<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid box-list-template">
    <div class="row">
        {if !empty($template)}
            {foreach from = $template item = item}
                <div class="col-xl-3 col-md-4 col-12">
                    <div class="kt-portlet kt-portlet--height-fluid kt-ribbon kt-ribbon--success kt-ribbon--shadow kt-ribbon--left">
                        {if !empty($item.is_default) && $item.is_default == 1}
                            <div class="kt-ribbon__target fs-12" style="top: 12px; right: -2px;">
                                {__d('admin', 'dang_kich_hoat')}
                            </div>
                        {/if}

                        <div class="nh-screenshot">
                            <img {if empty($item.is_default) && $item.is_default != 1}class="bg-inactive"{/if} src="/templates/mobile_{if !empty($item.code)}{$item.code}{/if}/screenshot.png">

                            <span class="overbackground"></span>

                            <div class="entire-template">
                                <div class="kt-portlet__body">
                                    <div class="kt-widget kt-widget--user-profile-2">

                                        <div class="kt-widget__head">
                                            <div class="kt-widget__info pl-0">
                                                <span class="kt-widget__titel">
                                                    {if !empty($item.name)}
                                                        {$item.name}
                                                    {/if}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="kt-widget__body">
                                            <div class="kt-widget__section">
                                                {if !empty($item.description)}
                                                    {$item.description}
                                                {/if}
                                            </div>

                                            <div class="kt-widget__item">
                                                <div class="kt-widget__contact">
                                                    <span class="kt-widget__label">
                                                        {__d('admin', 'code')}:
                                                    </span>
                                                    <a href="#" class="kt-widget__data">
                                                        {if !empty($item.code)}
                                                            {$item.code}
                                                        {/if}
                                                    </a>
                                                </div>

                                                <div class="kt-widget__contact">
                                                    <span class="kt-widget__label">
                                                        {__d('admin', 'tac_gia')}:
                                                    </span>

                                                    <a href="#" class="kt-widget__data">
                                                        {if !empty($item.author)}
                                                            {$item.author}
                                                        {/if}
                                                    </a>
                                                </div>

                                                <div class="kt-widget__contact">
                                                    <span class="kt-widget__label">
                                                        {__d('admin', 'phien_ban')}:
                                                    </span>

                                                    <span class="kt-widget__data">
                                                        {if !empty($item.version)}
                                                            {$item.version}
                                                        {/if}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="kt-widget__footer">
                                        <div class="kt-separator kt-separator--border-solid mt-10 mb-20"></div>

                                        <span class="btn btn-sm btn-brand mb-10 nh-export" data-id="{if !empty($item.id)}{$item.id}{/if}">
                                            <i class="fa fa-file-export"></i>
                                            {__d('admin', 'xuat_giao_dien')}
                                        </span>

                                        {if empty($item.is_default)}
                                            <span class="btn btn-sm btn-success mb-10 nh-set-default" data-id="{if !empty($item.id)}{$item.id}{/if}">
                                                <i class="fa fa-check"></i>
                                                {__d('admin', 'kich_hoat_giao_dien')}
                                            </span>
                                        {/if}

                                        <span class="btn btn-sm btn-danger mb-10 nh-delete-template" data-id="{if !empty($item.id)}{$item.id}{/if}">
                                            <i class="fa fa-trash-alt"></i>
                                            {__d('admin', 'xoa_giao_dien')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}            
        {/if}

        <div class="col-xl-3 col-md-4 col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__body kt-portlet__body--center mh-300">
                    <span class="btn btn-sm btn-brand btn-upper nh-import-template">
                        <i class="la la-plus"></i> 
                        {__d('admin', 'them_giao_dien_moi')}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{$this->element('../Template/modal_export_template')}
{$this->element('../Template/modal_import_template')}