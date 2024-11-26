<div class="kt-portlet nh-template-portlet">
    <div class="kt-portlet__head p-10">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">                
                {if !empty($page_info.name)}
                    {$page_info.name}
                {else}
                    {__d('admin', 'cai_dat_giao_dien')}
                {/if}
            </h3>
        </div>

        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-group">                
                <span class="btn btn-sm btn-success btn-icon-md m-0 nh-btn-add-row" data-toggle="kt-tooltip" data-placement="top" title="{__d('admin', 'them_dong_moi')}" >
                    <i class="la la-plus"></i>
                    {__d('admin', 'them_dong_moi')}
                </span>
            </div>
        </div>
    </div>

    <div class="kt-portlet__body p-0">
        <div class="kt-portlet__content p-10">            
            <div class="row">
                <div class="col-12 wrap-list-item">
                    {if !empty($structure)}
                        {foreach from = $structure item = row}
                            <div class="row nh-row-item">
                                <div class="nh-column-item col-12">
                                    <div class="nh-content-column">
                                        {foreach from = $row item = block_info}
                                            <li data-code="{if !empty($block_info.code)}{$block_info.code}{/if}" class="block-item {if empty($block_info.status)}disable{/if}">
                                                <i class="fa fa-file-alt w-20px fs-14"></i>
                                                <span>
                                                    {if !empty($block_info.name)}{$block_info.name}{/if}
                                                </span>
                                            </li>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
    
